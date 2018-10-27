<?php

namespace App\Http\Controllers\Api\ExtratorFacebook;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\FacebookApiTrait;
use App\models\Admin\ExtractorFacebook\AdminToken;
use App\Traits\UserTrait;
use App\User;
use Auth;
use App\Jobs\Admin\ImportUidPhone;

class ConvertUidPhoneController extends Controller
{
	use FacebookApiTrait, UserTrait;

    public function convertPSIDToPhone($psid, Request $request)
    {
        $request->validate([
            'app' => 'nullable|in:MOBILE_ANDROID,WEB,VCHAT_PC',
        ]);
        $platform = $request->app;
    	$info = $this->getInfoFacebookFromPSID($psid);
        $user = User::find(Auth::id());
        $arr = json_decode($info, true);
        if (isset($arr['username'])) {
            $username = $arr['username'];
            $info2 = $this->getInfoFacebookFromPSID($username);
            $arr2 = json_decode($info2, true);
            if (isset($arr2['id'])) {
                $uid = $arr2['id'];
                $phone = $this->getPhoneFromUID($uid);
                if ($phone) {
                    $phone = convertPhoneNumber($phone);
                    ImportUidPhone::dispatch($uid, $phone)->onQueue('statistic');
                    if (!empty($phone)) {
                        $this->saveHistoryActivity($user, 'FIND_PHONE', $platform, 'SUCCESS');
                        return response()->json([
                            'id' => $psid,
                            'phone' => $phone
                        ]);
                    }
                }
            }
        }
        $this->saveHistoryActivity($user, 'FIND_PHONE', $platform, 'FAIL');
    	return response()->json([
    		'message' => 'not find uid'
    	], 400);
    }

    public function convertPSIDToLinkFacebook($psid, Request $request)
    {
        $request->validate([
            'app' => 'nullable|in:MOBILE_ANDROID,WEB,VCHAT_PC',
        ]);
        $platform = $request->app;
        $user = User::find(Auth::id());
    	$info = $this->getInfoFacebookFromPSID($psid);
    	$arr = json_decode($info, true);
    	if (isset($arr['username'])) {
    		$username = $arr['username'];
            $this->saveHistoryActivity($user, 'FIND_FB', $platform, 'SUCCESS');
	    	return response()->json([
	    		'link' => 'https://www.facebook.com/' . $username
	    	]);
    	}
        $this->saveHistoryActivity($user, 'FIND_FB', $platform, 'FAIL');
    	return response()->json([
    		'message' => 'not found link'
    	], 400);
    }

    public function getInfoFacebookFromPSID($psid)
    {
        $userAgents = $this->generateUserAgents();
        $head = $this->listHeader;
        $adminToken = AdminToken::where('admin_id', ADMIN_ID_TOKEN_USE)->first();
        if ($adminToken) {
        	$accessToken = $adminToken->access_token_lord;
	    	$url = 'https://graph.facebook.com/' . $psid . '?access_token=' .$accessToken;
		  	$ch = curl_init ();
	        curl_setopt ( $ch, CURLOPT_URL, $url );
	        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
	        curl_setopt ( $ch, CURLOPT_USERAGENT, $userAgents );
	        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $head );
	        curl_setopt ( $ch, CURLOPT_TIMEOUT, 120 );
	        curl_setopt ( $ch, CURLOPT_ENCODING, "" );
		  	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		  	$result = curl_exec ( $ch );
		  	curl_close ( $ch );
		  	return $result;
        }
        return null;
    }
}
