<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Admin\PhoneNumber;
use App\models\Admin\UidFacebook;
use App\models\Admin\LookUid;
use App\Jobs\Admin\GetImportFromFile;
use Auth;
use App\models\Admin\ExtractorFacebook\AdminToken;
use App\Traits\FacebookApiTrait;

class ExtractorFacebookController extends Controller
{
    use FacebookApiTrait;

	public function getImportUid()
	{
		return view('admin.uid.import_uid');
	}

    public function importUid(Request $request)
    {
    	$request->validate([
    		'UIDList' => 'required|mimes:txt|max:2000',
            'look_id' => 'nullable|numeric|min:0'
    	]);
        $lookId = (binary) $request->look_id;
    	$path = $request->file('UIDList')->store('public/UID');
    	GetImportFromFile::dispatch($path, $lookId, 'UID')->onQueue('statistic');
    	return redirect()->route('admin.getImport.uid')->with([
            'message' => 'Upload Success!',
            'status' => 'success'
        ]);
    }

    public function getAllUid(Request $request)
    {
    	$keyword = $request->keyword;
    	$timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;
        $status = $request->status;
        if ($keyword || $timeFrom || $timeTo || $status) {
            $customeUrl = '';
    		if ($keyword) {
    			$query = UidFacebook::where('uid', $keyword);
                $customeUrl .= '?keyword=' . $keyword;
    		}
    		if ($timeFrom && $timeTo) {
    			$timeFrom = date('Y-m-d H:i:s', strtotime($timeFrom));
        		$timeTo = date('Y-m-d H:i:s', strtotime($timeTo));
    			if ($keyword) {
    				$query = $query->where('created_at', '>=', $timeFrom)
    								->where('created_at', '<=', $timeTo);
                    $customeUrl .= '&timeFrom=' . $timeFrom . '&timeTo=' . $timeTo;
    			} else {
    				$query = UidFacebook::where('created_at', '>=', $timeFrom)
    									->where('created_at', '<=', $timeTo);
                    $customeUrl .= '?timeFrom=' . $timeFrom . '&timeTo=' . $timeTo;
    			}
    		}
            if ($status) {
                if ($keyword || $timeFrom) {
                    if ($status == 1) {
                        $query = $query->where('scanned', true);
                    } else {
                        $query = $query->where('scanned', false);
                    }
                    $customeUrl .= '&status=' . $status;
                } else {
                    if ($status == 1) {
                        $query = UidFacebook::where('scanned', true);
                    } else {
                        $query = UidFacebook::where('scanned', false);
                    }
                    $customeUrl .= '?status=' . $status;
                }
            }
    		$uids = $query->paginate(20)->withPath($customeUrl);
    	} else {
    		$uids = UidFacebook::paginate(20);
    	}
    	return view('admin.uid.uid_all', compact('uids', 'keyword', 'timeFrom', 'timeTo', 'status'));
    }

    public function scanPhone()
    {
    	return view('admin.uid.scan_phone_from_uid');
    }

    public function startScanPhone(Request $request)
    {
    	$request->validate([
            'count_of_time' => 'required|numeric|min:1',
    		'time_range' => 'required|numeric|min:1'
    	]);
        $countOfTime = $request->count_of_time;
    	$timeRange = $request->time_range;
    	$uids = UidFacebook::where('scanned', false)
    						->orderBy('uid')
    						->take($countOfTime)
    						->get();
   		$newPhone = [];
   		$uidArr = [];
   		$timeNow = date('Y-m-d H:i:s', time());
   		$countPhone = 0;
    	foreach ($uids as $uid) {
    		$phone = $this->getPhoneFromUID($uid->uid);
    		if (!empty($phone)) {
                $has = PhoneNumber::where('uid', $uid->uid)
                                    ->where('phone', $phone)
                                    ->first();
                if (!$has) {
                    array_push($newPhone, [
                        'uid' => $uid->uid,
                        'phone' => $phone,
                        'created_at' => $timeNow
                    ]);
                    $countPhone++;
                }
            }
			array_push($uidArr, $uid->uid);
    	}
    	PhoneNumber::insert($newPhone);
    	UidFacebook::whereIn('uid', $uidArr)
    				->update([
    					'scanned' => true
    				]);
    	$count = UidFacebook::where('scanned', false)->count();
    	return view('admin.uid.scanning_phone', compact('count', 'countPhone', 'countOfTime', 'uids', 'timeRange'));
    }

    public function getAllPhone(Request $request)
    {
    	$keyword = $request->keyword;
    	$timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;
    	if ($keyword || $timeFrom || $timeTo) {
    		if ($keyword) {
    			$query = PhoneNumber::where('uid', $keyword);
    		}
    		if ($timeFrom && $timeTo) {
    			$timeFrom = date('Y-m-d H:i:s', strtotime($timeFrom));
        		$timeTo = date('Y-m-d H:i:s', strtotime($timeTo));
    			if ($keyword) {
    				$query = $query->where('created_at', '>=', $timeFrom)
    								->where('created_at', '<=', $timeTo);
    			} else {
    				$query = PhoneNumber::where('created_at', '>=', $timeFrom)
    									->where('created_at', '<=', $timeTo);
    			}
    		}
    		$phones = $query->paginate(20);
    	} else {
    		$phones = PhoneNumber::paginate(20);
    	}
    	return view('admin.uid.phone_all', compact('phones', 'keyword', 'timeFrom', 'timeTo'));
    }

    public function getImportPhone()
    {
        return view('admin.uid.import_phone');
    }

    public function importPhone(Request $request)
    {
        $request->validate([
            'UidPhoneList' => 'required|mimes:txt|max:2000',
            'look_id' => 'nullable|numeric|min:0'
        ]);
        $lookId = (binary) $request->look_id;
        $path = $request->file('UidPhoneList')->store('public/UID');
        GetImportFromFile::dispatch($path, $lookId, 'UID_PHONE')->onQueue('statistic');
        return redirect()->route('admin.import.phone')->with([
            'message' => 'Upload Success!',
            'status' => 'success'
        ]);
    }

    public function getImportTokenLord()
    {
        return view('admin.extractor.create_token');
    }

    public function importTokenLord(Request $request)
    {
        $request->validate([
            'access_token' => 'required|max:500'
        ]);
        $token = $request->access_token;
        $user = Auth::guard('admin')->user();
        AdminToken::updateOrCreate(
            ['admin_id' => $user->id],
            ['access_token_lord' => $token]
        );
        return redirect()->route('admin.extractor.createTokenLord')->with([
            'status' => 'success',
            'message' => 'Create success!'
        ]);
    }
}
