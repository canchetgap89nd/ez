<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Facebook\ApiFacebookController;
use Auth;
use App\User;
use App\models\Page;
use \Firebase\JWT\JWT;
use App\Traits\UserTrait;

class AuthController extends Controller
{
    use UserTrait;
    
    public function getLogin()
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::user();
            if (!$user->isWare()) {
                return redirect()->route('conversations');
            }
            return redirect()->route('products');
    	}
        $fb = setupApi();
        $helpers = $fb->getRedirectLoginHelper();
        $permissions = [
            'email',
            'public_profile', 
            'manage_pages', 
            'pages_messaging_subscriptions',
            'publish_pages',
            'read_page_mailboxes',
            'pages_messaging'
        ];
        $loginUrl = $helpers->getLoginUrl(route('loginFromFace'), $permissions);
    	return view('home', compact('loginUrl'));
    }

    public function loginWithInfoFb(Request $request)
    {
        $request->validate([
            'app' => 'nullable|in:MOBILE_ANDROID,WEB,VCHAT_PC',
        ]);
    	$fbId = $request->id;
    	$accessToken = $request->access_token;
        $fromPlatform = $request->app;
        $infoFb = (new ApiFacebookController)->getInfoFb($accessToken);
        if (isset($infoFb['id'])) {
            $pages = (new ApiFacebookController)->getAllPages($accessToken);
            $str = (new UserController)->convertStrWithId($infoFb['id']);
            if ($this->coreLogin($str['username'], $str['password'])) {
                $user = User::find(Auth::id());
                $this->saveHistoryActivity($user, 'LOGIN', $fromPlatform);
                $token = $user->createToken('TokenUserBrowser')->accessToken;
                //get token for socket
                $privateKey = env('JWT_SECRET');
                $tokenSocket = array(
                    "iss" => md5($user->id),
                    "email" => $user->user_email,
                    'uid' => $user->id
                );
                $jwt = JWT::encode($tokenSocket, $privateKey);
                //if user not setup name
                $updateEmail = isset($infoFb['email']) ? $infoFb['email'] : $user->user_fb_email;
                $updateName = !$user->name ? $infoFb['name'] : $user->name;
                if (!$user->name || !$user->user_fb_email) {
                    $user->update([
                        'name' => $updateName,
                        'user_fb_email' => $updateEmail,
                        'user_access_token' => $accessToken
                    ]);
                } else {
                    $user->update([
                        'user_fb_email' => $updateEmail,
                        'user_access_token' => $accessToken
                    ]);
                }
                $pagesOf = $user->pages()->get();
                $timeNow = date('Y-m-d H:i:s', time());
                $newPages = [];
                foreach ($pages as $page) {
                    $ch = false;
                    // fix error not has acccess_token from facebook
                    $page_token = isset($page['access_token']) ? $page['access_token'] : '';
                    foreach ($pagesOf as $entity) {
                        if ($page['id'] == $entity->fb_page_id) {
                            $entity->update([
                                'page_name' => $page['name'],
                                'page_token' => $page_token ? $page_token : $entity->page_token,
                                'page_category' => $page['category']
                            ]);
                            $ch = true;
                            break;
                        }
                    }
                    if (!$ch) {
                        array_push($newPages, [
                            'user_id' => $user->id,
                            'fb_page_id' => $page['id'],
                            'page_name' => $page['name'],
                            'page_token' => $page_token,
                            'active' => 0,
                            'page_category' => $page['category'],
                            'created_at' => $timeNow
                        ]);
                    }
                }
                //update more page if user is admin
                if ($user->isAdmin()) {
                    Page::insert($newPages);
                }
                // delete page not has role manager
                foreach ($pagesOf as $page) {
                    $has = false;
                    foreach ($pages as $item) {
                        if ($item['id'] == $page->fb_page_id) {
                            $has = true;
                            break;
                        }
                    }
                    if (!$has) {
                        $page->delete();
                    }
                }
                // redirect cofirm view with role for account childs
                if ($user->hasInvite()) {
                    return response()->json([
                        'authenticated' => true,
                        'uid' => $user->id,
                        'api_token' => $token, 
                        'router' => route('confirm.role'),
                        'jwt' => $jwt,
                        'confirm_role' => true
                    ]);
                }

                $router = !$user->isWare() ? route('conversations') : route('products');
                return response()->json([
                    'authenticated' => true,
                    'uid' => $user->id,
                    'api_token' => $token,
                    'router' => $router,
                    'jwt' => $jwt
                ]);
            }

            // create new account
            if (!(new UserController)->hasAccountFb($infoFb['id'])) {
                $infoFb['email'] = isset($infoFb['email']) ? $infoFb['email'] : '';
                $user = (new UserController)->createAccountWithFb($infoFb['id'], $accessToken, $infoFb['name'], '', $infoFb['email']);
                $this->saveHistoryActivity($user, 'REGISTERED', $fromPlatform);
                if ($user && $this->coreLogin($str['username'], $str['password'])) {
                    $token = $user->createToken('TokenUserBrowser')->accessToken;
                    //get token for socket
                    $privateKey = env('JWT_SECRET');
                    $tokenSocket = array(
                        "iss" => md5($user->id),
                        "email" => $user->user_email,
                        'uid' => $user->id
                    );
                    $jwt = JWT::encode($tokenSocket, $privateKey);
                    // redirect cofirm view with role for account childs
                    if ($user->hasInvite()) {
                        return response()->json([
                            'authenticated' => true,
                            'uid' => $user->id,
                            'api_token' => $token, 
                            'router' => route('confirm.role'),
                            'jwt' => $jwt,
                            'confirm_role' => true
                        ]);
                    }
                    return response()->json([
                        'authenticated' => true,
                        'uid' => $user->id,
                        'api_token' => $token,
                        'router' => route('set.info'),
                        'jwt' => $jwt,
                        'account_new' => true
                    ]);
                }
            }
        }
        // if authentication false
    	return response()->json([
            'authenticated' => false, 
            'router' => route('getLogin')
        ]);
    }

    public function coreLogin($username, $password)
    {
        return Auth::guard('web')->attempt(['username' => $username, 'password' => $password]);
    }

    public function login($username, $password)
    {
        if ($this->coreLogin()) {
            $user = Auth::user();
            if (!$user->isWare()) {
                return redirect()->intended('dashboard');
            }
            return redirect()->intended('products.index');
        }
        return redirect()->route('getLogin');
    }

    public function logout(Request $request)
    {
        $request->validate([
            'app' => 'nullable|in:MOBILE_ANDROID,WEB,VCHAT_PC',
        ]);
        $fromPlatform = $request->app;
        $user = User::find(Auth::guard('web')->id());
        $this->saveHistoryActivity($user, 'LOGOUT', $fromPlatform);
        Auth::guard('web')->logout();
        deleteCookie('tk_cs');
        deleteCookie('uid');
        deleteCookie('sk_token');
        return redirect()->route('getLogin');
    }
}
