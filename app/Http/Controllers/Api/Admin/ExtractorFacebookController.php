<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Admin\PhoneNumber;
use App\models\Admin\UidFacebook;
use App\models\Admin\LookUid;
use Illuminate\Support\Facades\DB;

class ExtractorFacebookController extends Controller
{
    public function getScan(Request $request)
    {
    	$request->validate([
            'count_of_time' => 'required|numeric|min:1',
    	]);
        $countOfTime = $request->count_of_time;
    	$uids = UidFacebook::where('scanned', false)
    						->orderBy('uid')
    						->take($countOfTime)
    						->get();
        $arr = array();
        foreach ($uids as $uid) {
            array_push($arr, $uid->uid);
        }
        UidFacebook::whereIn('uid', $arr)->update([
            'scanned' => true
        ]);
    	return response()->json([
    		'uids' => $uids
    	]);
    }

    public function createPhone(Request $request)
    {
    	$request->validate([
            'UidPhoneList' => 'array|nullable',
            'lookId' => 'nullable|numeric'
        ]);
        $uidPhoneList = $request->UidPhoneList;
        $lookId = $request->lookId;
        $newUid = [];
        $newPhone = [];
        $newUidLook = [];
        $time = date('Y-m-d H:i:s');
        foreach ($uidPhoneList as $item) {
            $item['phone'] = isset($item['phone']) ? convertPhoneNumber($item['phone']) : null;
            $item['uid'] = isset($item['uid']) ? (binary) $item['uid'] : null;
            $hasUid = DB::table('uid_facebooks')
                            ->select('uid')
                            ->where('uid', $item['uid'])
                            ->first();
            if (empty($hasUid)) {
                $flag = true;
                foreach ($newUid as $uid) {
                    if ($uid['uid'] == $item['uid']) {
                        $flag = false;
                        break;
                    }
                }
                if ($flag) {
                    array_push($newUid, [
                        'uid' => $item['uid'],
                        'created_at' => $time
                    ]);
                }
            }
            $hasPhone = DB::table('phone_numbers')
                            ->select('uid', 'phone')
                            ->where('uid', $item['uid'])
                            ->where('phone', $item['phone'])
                            ->first();
            if (empty($hasPhone)) {
                $flag = true;
                foreach ($newPhone as $phone) {
                    if ($phone['uid'] == $item['uid'] && $phone['phone'] == $item['phone']) {
                        $flag = false;
                        break;
                    }
                }
                if ($flag) {
                    array_push($newPhone, [
                        'uid' => $item['uid'],
                        'phone' => $item['phone'],
                        'created_at' => $time
                    ]);
                }
            }
            if (!empty($lookId)) {
                $hasLookUid = DB::table('look_uids')
                                    ->where('uid', $item['uid'])
                                    ->where('look_id', $lookId)
                                    ->first();
                if (empty($hasLookUid)) {
                    $flag = true;
                    foreach ($newUidLook as $look) {
                        if ($look['uid'] == $item['uid'] && $look['look_id'] == $lookId) {
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        array_push($newUidLook, [
                            'uid' => $item['uid'],
                            'look_id' => $lookId
                        ]);
                    }
                }
            }
        }
        UidFacebook::insert($newUid);
        PhoneNumber::insert($newPhone);
        LookUid::insert($newUidLook);
        return response()->json([
        	'success' => true
        ]);
    }
}
