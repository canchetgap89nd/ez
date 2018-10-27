<?php

namespace App\Jobs\Dashboard;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Traits\ProcessDataFacebook;

class ReceiveDataFromFacebook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ProcessDataFacebook;

    protected $userId;
    protected $fbPageId;
    protected $threadId;
    protected $entityId;
    protected $timeMess;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $userId, $fbPageId, $threadId, $entityId, $timeMess, $type)
    {
        $this->userId = $userId;
        $this->fbPageId = $fbPageId;
        $this->threadId = $threadId;
        $this->entityId = $entityId;
        $this->timeMess = $timeMess;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->updateConversationJob($this->userId, $this->fbPageId, $this->threadId, $this->entityId, $this->timeMess, $this->type);
    }
}
