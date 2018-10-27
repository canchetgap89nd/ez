<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\Facebook\ApiFacebookController;
use App\models\Page;

class LikeComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page;
    protected $commentId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Page $page, $commentId)
    {
        $this->page = $page;
        $this->commentId = $commentId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->commentId && $this->page) {
            (new ApiFacebookController)->likeCommentOnFb($this->commentId, true, $this->page->page_token);
        }
    }
}
