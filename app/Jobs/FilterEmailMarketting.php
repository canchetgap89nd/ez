<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\models\Page;
use App\User;
use App\models\InfoMarketing;

class FilterEmailMarketting implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page;
    protected $user;
    protected $converId;
    protected $entityId;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Page $page, $converId, $entityId, $message)
    {
        $this->page = $page;
        $this->user = $user;
        $this->converId = $converId;
        $this->entityId = $entityId;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $subject = getEmailFromText($this->message);

        if ((count($subject) > 0) && $this->user && $this->page) {
            foreach ($subject as $email) {
                InfoMarketing::create([
                    'user_id' => $this->user->id,
                    'page_id' => $this->page->page_id,
                    'from_conver_id' => $this->converId,
                    'from_entity_id' => $this->entityId,
                    'email_mar' => $email
                ]);
            }
        }
    }
}
