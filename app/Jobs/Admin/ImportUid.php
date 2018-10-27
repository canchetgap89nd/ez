<?php

namespace App\Jobs\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\models\Admin\UidFacebook;
use App\models\Admin\LookUid;

class ImportUid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $lookId;
    protected $uid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uid, $lookId)
    {
        $this->lookId = $lookId;
        $this->uid = $uid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $has = UidFacebook::where('uid', $this->uid)->first();
        if (empty($has)) {
            UidFacebook::create([
                'uid' => $this->uid
            ]);
        }
        $hasLookId = LookUid::where('uid', $this->uid)
                            ->where('look_id', $this->lookId)
                            ->first();
        if (empty($hasLookId)) {
            LookUid::create([
                'uid' => $this->uid,
                'look_id' => $this->lookId
            ]);
        }
    }
}
