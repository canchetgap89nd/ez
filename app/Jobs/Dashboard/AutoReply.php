<?php

namespace App\Jobs\Dashboard;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\models\Page;
use App\Http\Controllers\Facebook\ApiFacebookController;
use App\Traits\FacebookApiTrait;

class AutoReply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, FacebookApiTrait;

    protected $itemId;
    protected $page;
    protected $content;
    protected $type;
    protected $nameCus;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Page $page, $itemId, $content, $type, $nameCus)
    {
        $this->itemId = $itemId;
        $this->page = $page;
        $this->type = $type;
        $this->content = $content;
        $this->nameCus = $nameCus;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$content = str_replace('[FULL_NAME]', $this->nameCus, $this->content);
        switch ($this->type) {
            case 'COMMENT':
                $this->sendCommentToFb($this->itemId, $content, $this->page->page_token);
                break;
            case 'MESSAGE':
                $this->sendMessagesWithPostToFb($this->itemId, $content, $this->page->page_token);
                break;
            default:
                break;
        }
    }
}
