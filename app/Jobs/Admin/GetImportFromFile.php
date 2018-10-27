<?php

namespace App\Jobs\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\Admin\ImportUid;
use App\Jobs\Admin\ImportUidPhone;
use Illuminate\Support\Facades\Storage;

class GetImportFromFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;
    protected $lookId;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path, $lookId, $type)
    {
        $this->path = $path;
        $this->lookId = $lookId;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = Storage::url($this->path);
        $arr = explode("\r\n", file_get_contents(asset($file)));
        switch ($this->type) {
            case 'UID':
                $arrConvert = [];
                foreach ($arr as $item) {
                    if ($item && strlen($item) > 1 && strlen($item) <= 16) {
                        $id = (binary) $item;
                        array_push($arrConvert, $id);
                    }
                }
                $arrConvert = array_unique($arrConvert);
                if (count($arrConvert) > 0) {
                    foreach ($arrConvert as $uid) {
                        ImportUid::dispatch($uid, $this->lookId)->onQueue('statistic');
                    }
                }
                Storage::delete($this->path);
                break;
            case 'UID_PHONE':
                $arrUIDPhone = [];
                $arrUID = [];
                foreach ($arr as $row) {
                    $row = trim($row);
                    $doubItem = explode("|", $row);
                    if (isset($doubItem[0])) {
                        $uid = $doubItem[0];
                        if ($uid && strlen($uid) > 1 && strlen($uid) <= 16) {
                            $id = (binary) $uid;
                            array_push($arrUID, $id);
                        }
                        if (isset($doubItem[1]) && isset($id)) {
                            $phone = $doubItem[1];
                            array_push($arrUIDPhone, [
                                'uid' => $id,
                                'phone' => $phone
                            ]);
                        }
                    }
                }
                $arrUID = array_unique($arrUID);
                foreach ($arrUID as $uid) {
                    ImportUid::dispatch($uid, $this->lookId)->onQueue('statistic');
                }
                foreach ($arrUIDPhone as $item) {
                    ImportUidPhone::dispatch($item['uid'], $item['phone'])->onQueue('statistic');
                }
                Storage::delete($this->path);
                break;
            default:
                break;
        }
    }
}
