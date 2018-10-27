<?php

namespace App\Jobs\Dashboard;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Traits\FacebookApiTrait;

class SubcribeApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, FacebookApiTrait;

    protected $pageId;
    protected $token;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pageId, $token, $type)
    {
        $this->pageId = $pageId;
        $this->token = $token;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->type) {
            case 'SUBCRIBE':
                $resp = $this->subcribeApp($this->pageId, $this->token);
                if (!$resp['success']) {
                    \Log::info(print_r($resp, true));
                }
                break;
            case 'UNSUBCRIBE':
                $resp = $this->unsubcribeApp($this->pageId, $this->token);
                if (!$resp['success']) {
                    \Log::info(print_r($resp, true));
                }
                break;
            default:
                \Log::info(print_r('Not have action SUBCRIBE or UNSUBCRIBE', true));
                break;
        }
    }
}
