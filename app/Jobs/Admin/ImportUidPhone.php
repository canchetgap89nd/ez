<?php

namespace App\Jobs\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\models\Admin\PhoneNumber;

class ImportUidPhone implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uid;
    protected $phone;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uid, $phone)
    {
        $this->uid = $uid;
        $this->phone = $phone;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->uid && $this->phone) {
            $has = PhoneNumber::where('uid', $this->uid)
                                    ->where('phone', $this->phone)
                                    ->first();
            if (empty($has)) {
                PhoneNumber::create([
                    'uid' => $this->uid,
                    'phone' => $this->phone
                ]);
            }
        }
    }
}
