<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\models\StatisticSale;
use App\models\Order;
use App\User;

class SaleStatistical implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Order $order)
    {
        $this->user = $user;
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $saleAmount = $this->order->total_value - $this->order->value_has_sale;
        $backAmount = $this->order->total_value;
        $discount = $this->order->discount;
        $revenue = $backAmount - $saleAmount - $discount;
        $actionTime = strtotime($this->order->created_at);
        $shipFee = $this->order->ship_fee;

        $prodsOrder = $this->order->products()->get();
        $originVal = 0;
        foreach ($prodsOrder as $prod) {
            $originVal += $prod->pivot->quantity * $prod->prod_price_imp;
        }
        $profit = $revenue - $originVal;

        StatisticSale::create([
            'user_id' => $this->user->id,
            'page_id' => $this->order->page_id,
            'order_id' => $this->order->id,
            'sale_amount' => $saleAmount,
            'back_amount' => $backAmount,
            'discount' => $discount,
            'revenue' => $revenue,
            'origin_val' => $originVal,
            'profit' => $profit,
            'ship_fee' => $shipFee,
            'action_time' => $actionTime
        ]);
    }
}
