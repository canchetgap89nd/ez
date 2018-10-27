<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\TempRole;
use Auth;
use App\User;
use App\Http\Requests\CreateUserInfoRequest;
use App\models\Page;
use App\models\Role;
use App\Http\Controllers\Api\UserController;
use App\Traits\UserTrait;
use App\Traits\FacebookApiTrait;
use \Firebase\JWT\JWT;
use App\Http\Controllers\client\AuthController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
	use UserTrait, FacebookApiTrait;
	
    public function getConfirmInvite()
  	{
	    $user = Auth::user();
	    if ($user->hasInvite()) {
	      	$tempRole = TempRole::where('fb_user_staff', $user->user_fb_id)->get();
      		$accounts = [];
	      	if (count($tempRole)) {
	      		$userArr = [];
	      		foreach ($tempRole as $item) {
	      			array_push($userArr, $item->user_parent);
	      		}
	      		$userArr = array_unique($userArr);
	      		foreach ($userArr as $key => $acc) {
	      			$arrTg = [];
	      			foreach ($tempRole as $k => $item) {
	      				if ($item->user_parent == $acc) {
	      					array_push($arrTg, $item);
	      				}
	      				$lastInd = $k;
	      			}
	      			$accounts[$key]['pages'] = $arrTg;
	      			$accounts[$key]['role'] = optional(Role::find($arrTg[0]->role_id))->display_name;
	      		}
	      	}
      		return view('login.confirm_role', compact('accounts'));
	    }

	    if (!$user->isWare()) {
	      return redirect()->route('conversations');
	    }

	    return redirect()->route('products');
  	}

  	/**
   * get setup pages
   * @return [type] [description]
   */
	public function getSetInfo()
	{
	    $user = Auth::user();
	    $useAccessToken = $user->user_access_token;
	    $pages = $user->pages()->get();
	    // get update pages if user is admin
	    if ($user->isAdmin()) {
	      	$pagesFbIds = [];
	      	foreach ($pages as $page) {
	       		array_push($pagesFbIds, $page->fb_page_id);
	      	}
	      	$pagesFb = $this->getAllPages($useAccessToken);
	      	$pagesNotHas = [];
	      	$timeNow = date('Y-m-d H:i:s', time());
	      	foreach ($pagesFb as $item) {
	      		$page_token = isset($item['access_token']) ? $item['access_token'] : '';
		        if (!in_array($item['id'], $pagesFbIds)) {
		          	$newPage = [
		            	'user_id' => $user->id,
		            	'fb_page_id' => $item['id'],
		            	'page_name' => $item['name'],
		            	'page_token' => $page_token,
		            	'page_category' => $item['category'],
		            	'active' => 0,
		            	'created_at' => $timeNow
		          	];
		          	array_push($pagesNotHas, $newPage);
		        }
	      	}
	      	// update more pages
	      	Page::insert($pagesNotHas);
	      	$pages = $user->pages()->get();
	    }
    	return view('login.setup_pages', compact('pages', 'user', 'useAccessToken'));
	}

	public function setInfo(CreateUserInfoRequest $request)
  	{
	    $user = User::find(Auth::id());
	    $pages = $request->pages;
	    $phone = convertPhoneNumber($request->phone);
	    if (count($pages) <= $this->getPageLimit($user)) {
		    (new UserController)->setPageAndInfoUser($user, $request->pages, $request->name, $request->email, $phone);
		    $childs = $user->accounts()->count();
		    if ($childs == 0 && $user->isAdmin()) {
		      	return redirect()->route('set.role');
		    }
		    if (!$user->isWare()) {
		    	return redirect()->route('conversations');
		    }
		    return redirect()->route('products');
	    }
	    return redirect()->route('set.info')->with([
            'status' => 'danger',
            'message' => 'Tài khoản của bạn chỉ có thể kích hoạt tối đa ' . $this->getPageLimit($request->user()) . ' Fanpage. Vui lòng nâng cấp tài khoản để có thể kích hoạt không giới hạn Fanpage.'
        ]);
  	}

  	public function getSetRoles()
  	{
	    $user = Auth::user();
	    $pages = $user->pages()->where('active', 1)->get();
	    return view('login.set_role', compact('pages'));
  	}

  	public function blockedAccount()
  	{
	    return view('blocked.account_blocked');
  	}

  	public function loginFromFace()
  	{
  		$fb = setupApi();
  		$helpers = $fb->getRedirectLoginHelper();
  		if (isset($_GET['state'])) {
		    $helpers->getPersistentDataHandler()->set('state', $_GET['state']);
		}
  		try {
  			$accessToken = $helpers->getAccessToken();
  		} catch (\Facebook\Exceptions\FacebookResponseException $e) {
  			throw new \Exception('Login error Graph returned an error: ' . $e->getMessage(), 1);
  		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			throw new \Exception('Login error Facebook SDK returned an error: ' . $e->getMessage(), 1);
		}

		if (! isset($accessToken)) {
			if ($helpers->getError()) {
				header('HTTP/1.0 401 Unauthorized');
			    echo "Error: " . $helpers->getError() . "\n";
			    echo "Error Code: " . $helpers->getErrorCode() . "\n";
			    echo "Error Reason: " . $helpers->getErrorReason() . "\n";
			    echo "Error Description: " . $helpers->getErrorDescription() . "\n";
			}	else {
			    header('HTTP/1.0 400 Bad Request');
			    echo 'Bad request';
		  	}
		  	exit;
		}

		$oAuth2Client = $fb->getOAuth2Client();
		if (! $accessToken->isLongLived()) {
		  	try {
		    	$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		  	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		  		throw new \Exception('Error getting long-lived access token: ' . $e->getMessage(), 1);
		  	}
		}
		$tokenFb = $accessToken->getValue();
		$infoFb = $this->getInfoFb($tokenFb);
		if (isset($infoFb['id'])) {
            $str = $this->convertStrWithId($infoFb['id']);
            if ((new AuthController)->coreLogin($str['username'], $str['password'])) {
                $user = User::find(Auth::id());
                $this->saveHistoryActivity($user, 'LOGIN', 'WEB');
                $token = $user->createToken('TokenUserBrowser')->accessToken;
                //get token for socket
                $privateKey = env('JWT_SECRET');
                $tokenSocket = array(
                    "iss" => md5($user->id),
                    "email" => $user->user_email,
                    'uid' => $user->id
                );
                $jwt = JWT::encode($tokenSocket, $privateKey);
                $this->updateWhenLogin($user, $infoFb, $tokenFb);
                createCookie('tk_cs', $token, 30);
                createCookie('uid', $user->id, 30);
                createCookie('sk_token', $jwt, 30);
                // redirect cofirm view with role for account childs
                if ($user->hasInvite()) {
                    return redirect()->route('confirm.role');
                }
                if (!$user->isWare()) {
                	return redirect()->route('conversations');
                }
                return redirect()->route('products');
            }

            // create new account
            if (!(new UserController)->hasAccountFb($infoFb['id'])) {
                $infoFb['email'] = isset($infoFb['email']) ? $infoFb['email'] : '';
                $user = (new UserController)->createAccountWithFb($infoFb['id'], $accessToken, $infoFb['name'], '', $infoFb['email']);
                $this->saveHistoryActivity($user, 'REGISTERED', 'WEB');
                if ($user && (new AuthController)->coreLogin($str['username'], $str['password'])) {
                    $token = $user->createToken('TokenUserBrowser')->accessToken;
                    //get token for socket
                    $privateKey = env('JWT_SECRET');
                    $tokenSocket = array(
                        "iss" => md5($user->id),
                        "email" => $user->user_email,
                        'uid' => $user->id
                    );
                    $jwt = JWT::encode($tokenSocket, $privateKey);
                    createCookie('tk_cs', $token, 30);
	                createCookie('uid', $user->id, 30);
	                createCookie('sk_token', $jwt, 30);
	                // redirect cofirm view with role for account childs
	                if ($user->hasInvite()) {
	                    return redirect()->route('confirm.role');
	                }
                    return redirect()->route('set.info');
                }
            }
        }
        // if authentication false
    	return redirect()->route('getLogin')->with([
    		'status' => 'error',
    		'message' => 'Đăng nhập facebook không thành công'
    	]);
  	}

  	public function checkUserNotActive()
  	{
        // Redis::set('userPriviousCheck', 0);
  		$index = Redis::get('userPriviousCheck');
  		$index = $index ? $index : 0;
  		$users = DB::table('users')->orderBy('id')->get();
  		$accToDo = null;
  		if ($index) {
  			for ($i=0; $i < count($users); $i++) {
  				if ($users[$i]->id == $index) {
  					$accToDo = isset($users[$i + 1]) ? $users[$i + 1] : null;
  					if ($accToDo) {
  						Redis::set('userPriviousCheck', $users[$i + 1]->id);
  					}
  					break;
  				}
  			}
  		} else {
  			$accToDo = isset($users[0]) ? $users[0] : null;
  			if ($accToDo) {
  				Redis::set('userPriviousCheck', $users[0]->id);
  			}
  		}
  		if ($accToDo) {
  			$ltCurrent = strtotime('+ 15 days', strtotime($accToDo->updated_at)) < time();
  			if ($ltCurrent) {
  				$pages = DB::table('pages')->where('user_id', $accToDo->id)->get();
  				foreach ($pages as $page) {
  					$res = $this->unsubcribeApp($page->fb_page_id, env('FACE_APP_TOKEN'));
  					if (isset($res['success']) && !$res['success']) {
  						\Log::error('Khong the unsubcribe fanpage cua nguoi dung ' . $accToDo->id);
  					}
  				}
  				DB::table('pages')->where('user_id', $accToDo->id)->update([
  					'active' => 0
  				]);
  			}
  		}
  	}

  	public function checkingRedisUser()
  	{
  		echo Redis::get('userPriviousCheck');
  	}
}
