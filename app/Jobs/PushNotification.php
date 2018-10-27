<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use App\models\DeviceInfo;
use App\Http\Controllers\client\NotificationController;

class PushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $conversation;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $conversation)
    {
        $this->conversation = $conversation;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $device = DeviceInfo::where('user_id', $this->user->id)->get();
        $playerIds = [];
        foreach ($device as $item) {
            array_push($playerIds, $item->token);
        }
        if (count($playerIds) > 0) {
            $payload = [
                'id' => $this->conversation->id,
                'fb_page_id' => $this->conversation->fb_page_id,
                'from_name' => $this->conversation->from_name,
                'from_id' => $this->conversation->from_id,
                'type' => $this->conversation->type
            ];
            $type = $this->conversation->type == COMMENT ? ' vừa bình luận' : ' vừa gửi nhắn tin';
            $title = $this->conversation->just_message->from_name . $type;
            $body = $this->conversation->last_message;
            (new NotificationController)->pushNotifyOneSignal($playerIds, $title, $body, $payload);
        }
    }
}
