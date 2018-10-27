<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\models\Role;
use App\models\TempRole;
use App\models\Page;
use App\Respositories\Conversation\ConverRespository;
use Auth;
use App\Http\Requests\Api\User\UpdateProfileRequest;
use App\Http\Requests\Api\Roles\CreateRoleForStaffRequest;
use App\Http\Requests\CreateUserInfoRequest;
use App\Http\Controllers\client\ConversationMongoController;
use Hash;
use App\Http\Controllers\client\SettingController;
use App\Jobs\Dashboard\SubcribeApp;
use App\models\Admin\PackageAndPayment\Package;
use App\Http\Requests\Api\User\CreateUserPaymentRequest;
use App\models\Admin\PackageAndPayment\UserPackage;
use App\models\Admin\PackageAndPayment\UserPayment;
use App\Traits\UserTrait;
use App\Traits\FacebookApiTrait;

class UserController extends Controller
{    
    use UserTrait, FacebookApiTrait;

    public function getProfile()
    {
    	$user = User::select('name', 'user_phone', 'user_email')->find(Auth::id());
    	return response()->json($user);
    }

    public function updateProfile($id, UpdateProfileRequest $request)
    {
    	$user = User::find(Auth::id())->update([
    		'name' => $request->name,
    		'user_phone' => $request->user_phone,
    		'user_email' => $request->user_email
    	]);
    	if ($user) {
    		return response()->json([
    			'success' => 1
    		]);
    	}
    	return response()->json([
    		'message' => 'Cannot find object'
    	], ERR_INTER_CODE);
    }

    /**
     * get information for confirm role user when receive notification from user another
     * @return [type] [description]
     */
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
                    $ind = count($accounts);
                    $accounts[$ind]['pages'] = $arrTg;
                    $accounts[$ind]['role'] = optional(Role::find($arrTg[0]->role_id))->display_name;
                }
            }
            return response()->json([
                'accounts' => $accounts
            ]);
        }
        return response()->json([
            'message' => 'not has invite to confirm'
        ], ERR_CODE);
    }

    /**
     * confirm role of user
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function confirmInvite($userParent)
    {
        $user = User::find(Auth::id());
        $temps = TempRole::where('user_parent', $userParent)
                            ->where('fb_user_staff', $user->user_fb_id)
                            ->get();
        if (count($temps)) {
            $newPages = [];
            $roleId = null;
            $timeNow = date('Y-m-d H:i:s', time());
            $arrFbPages = [];
            foreach ($temps as $tem) {
                $pageHas = Page::where('user_id', $user->id)
                                ->where('fb_page_id', $tem->fb_page_id)
                                ->count();
                if (!$pageHas) {
                    array_push($newPages, [
                        'user_id' => $user->id,
                        'fb_page_id' => $tem->fb_page_id,
                        'page_name' => $tem->page_name,
                        'active' => 0,
                        'created_at' => $timeNow
                    ]);
                }
                array_push($arrFbPages, $tem->fb_page_id);
                $roleId = $tem->role_id;
            }
            Page::insert($newPages);
            Page::where('user_id', $user->id)
                ->whereNotIn('fb_page_id', $arrFbPages)
                ->delete();
            $user->roles()->detach();
            $user->roles()->attach($roleId);
            $user->update([
                'parent_user_id' => $userParent
            ]);
            TempRole::where('user_parent', $userParent)
                        ->where('user_staff', $user->id)
                        ->delete();
            $role = Role::select('name', 'display_name')->find($roleId);
            return response()->json([
                'success' => true,
                'role' => $role
            ]);
        }
        return response()->json([
            'message' => 'cannot find object'
        ], ERR_INTER_CODE);
    }

    /**
     * deny request invite to user
     * @param  [type] $userParent [user id send request invite to user]
     * @return [type]             [description]
     */
    public function denyInvite($userParent)
    {
        $user = Auth::user();
        $temps = TempRole::where('user_parent', $userParent)
                        ->where('fb_user_staff', $user->user_fb_id)
                        ->delete();
        if ($temps) {
            return response()->json([
                'success' => true
            ]);
        }
        return response()->json([
            'message' => 'cannot find object'
        ], ERR_INTER_CODE);
    }

    /**
     * clear all invite request to user
     * @return [type] [description]
     */
    public function clearInvite()
    {
        $user = User::find(Auth::id());
        TempRole::where('fb_user_staff', $user->user_fb_id)
                    ->delete();
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * create role for user
     * @param  User   $user     [description]
     * @param  [type] $roleName [description]
     * @return [type]           [description]
     */
    public function createRoleForUser(User $user, $roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->roles()->detach();
            $user->roles()->attach($role->id);
            return true;
        }
        return false;
    }

    /**
     * remove role of user with page
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function destroyRoleOfStaffWithPage($fbPageId, $fbIdStaff)
    {
        $user = Auth::user();
        $staff = $user->accounts()
                        ->where('user_fb_id', $fbIdStaff)
                        ->first();
        TempRole::where('user_parent', $user->id)
                    ->where('fb_user_staff', $fbIdStaff)
                    ->where('fb_page_id', $fbPageId)
                    ->delete();
        if ($staff) {
            $staff->pages()
                    ->where('fb_page_id', $fbPageId)
                    ->delete();
        }
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * remove staff and remove all pages of staff is manager
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function destroyStaff($fbIdStaff)
    {
        $user = Auth::user();
        $staff = $user->accounts()
                        ->where('user_fb_id', $fbIdStaff)
                        ->first();
        TempRole::where('user_parent', $user->id)
                ->where('fb_user_staff', $fbIdStaff)
                ->delete();
        if ($staff) {
            $staff->pages()->delete();
            $staff->update([
                'parent_user_id' => 0
            ]);
            $this->createRoleForUser($staff, 'ADMINSTRATOR');
        }
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * send request invite to user another
     * @param CreateRoleForStaffRequest $request [description]
     */
    public function setRoles($fbPageId, $fbIdStaff, CreateRoleForStaffRequest $request)
    {
        $roleStaff = $request->role_staff;
        $user = Auth::user();
        $page = $user->pages()
                      ->where('fb_page_id', $fbPageId)
                      ->first();
        switch ($roleStaff) {
            case '1':
                $role = Role::where('name', "MANAGER")->first();
                break;
            case '2':
                $role = Role::where('name', "STORAGER")->first();
                break;
            case '3':
                $role = Role::where('name', "SALER")->first();
                break;
            default:
                return response()->json([
                  'message' => 'Vui lòng chọn quyền'
                ], 422);
            break;
        }
        if ($page && isset($role) && $role) {
            $staff = $user->accounts()
                            ->where('user_fb_id', $fbIdStaff)
                            ->first();
            // update more page for child if user has staff
            if ($staff) {
                $pageStaff = $staff->pages()->where('fb_page_id', $page->fb_page_id)->first();
                if (!$pageStaff) {
                    $pageHas = Page::where('user_id', $staff->id)
                                    ->where('fb_page_id', $page->fb_page_id)
                                    ->count();
                    if (!$pageHas) {
                        Page::create([
                            'user_id' => $staff->id,
                            'fb_page_id' => $page->fb_page_id,
                            'page_name' => $page->page_name,
                            'page_token' => $page->page_token,
                            'page_category' => $page->page_category,
                            'active' => 0
                        ]);
                    }
                }
                //remove all role
                $staff->roles()->detach();
                //update new roles
                $staff->roles()->attach($role->id);
            } else {
                // make notifications to staff user
                $userHas = User::where('user_fb_id', $fbIdStaff)->first();
                if ($userHas) {
                    $temps = TempRole::where('user_parent', $user->id)
                                        ->where('user_staff', $userHas->id)
                                        ->where('fb_page_id', $page->fb_page_id)
                                        ->count();
                    // update last role for all request to staff
                    TempRole::where('user_parent', $user->id)
                                ->where('user_staff', $userHas->id)
                                ->update([
                                    'role_id' => $role->id
                                ]);
                    if (!$temps) {
                        // make pages for role
                        TempRole::create([
                            'user_parent' => $user->id,
                            'fb_user_parent' => $user->user_fb_id,
                            'user_name_parent' => $user->name,
                            'user_staff' => $userHas->id,
                            'user_name_staff' => $userHas->name,
                            'fb_user_staff' => $userHas->user_fb_id,
                            'fb_page_id' => $page->fb_page_id,
                            'page_name' => $page->page_name,
                            'role_id' => $role->id
                        ]);
                    }
                } else {
                    $tempHas = TempRole::where('user_parent', $user->id)
                                        ->where('fb_user_staff', $fbIdStaff)
                                        ->where('fb_page_id', $page->fb_page_id)
                                        ->update([
                                            'role_id' => $role->id
                                        ]);
                    if (!$tempHas) {
                        TempRole::create([
                            'user_parent' => $user->id,
                            'user_name_parent' => $user->name,
                            'fb_user_parent' => $user->user_fb_id,
                            'fb_user_staff' => $fbIdStaff,
                            'fb_page_id' => $page->fb_page_id,
                            'page_name' => $page->page_name,
                            'role_id' => $role->id
                        ]);
                    }
                    TempRole::where('user_parent', $user->id)
                            ->where('fb_user_staff', $fbIdStaff)
                            ->update([
                                'role_id' => $role->id
                            ]);
                }
            }
            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
          'message' => 'Cannot find object'
        ], ERR_INTER_CODE);
    }

    /**
     * get all member on page manager
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getPageAdmin($fbPageId)
    {
        $user = Auth::user();
        $page = $user->pages()
                      ->where('fb_page_id', $fbPageId)
                      ->first();
        if ($page) {
            $access_token = $page->page_token;
            $user_fb_id = $user->user_fb_id;
            $data = $this->getRolesPage($page->fb_page_id, $access_token);
            $listMem = isset($data['data']) ? $data['data'] : [];
            $listStaffs = $user->accounts()->get();
            $listStaffsPend = TempRole::where('user_parent', $user->id)->get();
            $members = [];
            foreach ($listMem as $mem) {
                if ((in_array('CREATE_CONTENT', $mem['tasks'])) && ($mem['id'] != $user->user_fb_id) && $mem['is_active']) {
                    $mem['hasRole'] = [];
                    $mem['pages'] = [];
                    foreach ($listStaffs as $staff) {
                        if ($mem['id'] == $staff->user_fb_id) {
                            $mem['hasRole'] = $staff->roles()->select('name', 'display_name')->first();
                            $mem['pages'] = $staff->pages()->select('fb_page_id', 'page_name')->get();
                            break;
                        }
                    }
                    if (!count($mem['pages'])) {
                        foreach ($listStaffsPend as $pend) {
                            if ($pend->fb_user_staff == $mem['id']) {
                                $mem['hasRole'] = Role::select('name', 'display_name')
                                                        ->find($pend->role_id);
                                $mem['pages'] = TempRole::select('fb_page_id', 'page_name')
                                                        ->where('user_parent', $user->id)
                                                        ->where('fb_user_staff', $pend->fb_user_staff)
                                                        ->get();
                                $mem['pendding'] = true;
                                break;
                            }
                        }
                    }
                    array_push($members, $mem);
                }
            }
            return response()->json([
                'members' => $members
            ]);
        }
        return response()->json([
          'message' => 'Cannot find page'
        ], ERR_INTER_CODE);
    }

    /**
     * get role of user on page
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getRolesStaff($fbPageId, $userFbId)
    {
        $user = Auth::user();
        $page = $user->pages()->where('fb_page_id', $fbPageId)->first();
        $staff = $user->accounts()
                      ->where('user_fb_id', $userFbId)
                      ->first();
        if ($page) {
            if ($staff) {
                $pageOfStaff = $staff->pages()->where('fb_page_id', $page->fb_page_id)->first();
                if ($pageOfStaff) {
                    $role = $staff->roles()->select('name', 'display_name')->first();
                    return response()->json($role);
                } else {
                    $requestIsPendding = TempRole::where('user_parent', $user->id)
                                                    ->where('user_staff', $staff->id)
                                                    ->where('fb_page_id', $page->fb_page_id)
                                                    ->first();
                    if ($requestIsPendding) {
                        $role = Role::select('name', 'display_name')->find($requestIsPendding->role_id);
                        return response()->json($role);
                    }
                }
            } else {
                $requestIsPendding = TempRole::where('user_parent', $user->id)
                                                ->where('fb_user_staff', $userFbId)
                                                ->where('fb_page_id', $page->fb_page_id)
                                                ->first();
                if ($requestIsPendding) {
                    $role = Role::select('name', 'display_name')->find($requestIsPendding->role_id);
                    return response()->json($role);
                }
            }
        }
        return response()->json([]);
    }

    /**
     * register device for notifications mobile
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function registerDeviceMobile(Request $request)
    {
        $request->validate([
            'token_device' => 'required'
        ]);
        $token = $request->token_device;
        $user = User::find(Auth::id());
        $hasDevice = $user->device()->where('token', $token)->first();
        if (!$hasDevice && $token) {
            $user->device()->create([
                'token' => $token
            ]);
            return response()->json([
                'success' => true
            ]);
        }
        return response()->json([
            'message' => 'The device has registered'
        ], ERR_INTER_CODE);
    }

    /**
     * remove device mobile to don't receive notifications
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function unregisterDeviceMobile(Request $request)
    {
        $request->validate([
          'token_device' => 'required'
        ]);
        $token = $request->token_device;
        $user = User::find(Auth::id());
        $device = $user->device()->where('token', $token)->get();
        foreach ($device as $item) {
            $item->delete();
        }
        return response()->json([
            'success' => 1
        ]);
    }

    /**
     * get infomation of user 
     * @return [type] [description]
     */
    public function getInfoUser()
    {
        $infoUser = Auth::user()->select('name', 'user_email', 'user_phone');
        $role = Auth::user()->roles()->first();
        return response()->json([
            'infoUser' => $infoUser,
            'role' => $role
        ]);
    }

    /**
   * get for setup pages and update infomation for user
   * @return [type] [description]
   */
    public function getSetInfoApi()
    {
        $user = Auth::user();
        $useAccessToken = $user->user_access_token;
        $pages = $user->pages()->get();
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
        return response()->json([
            'pages' => $pages,
            'user' => $user
        ]);
    }

    /**
     * update infomation of user and setup active pages for user
     * @param CreateUserInfoRequest $request [description]
     */
    public function setInfoApi(CreateUserInfoRequest $request)
    {
        $user = User::find(Auth::id());
        $pages = json_decode($request->pages);
        $phone = convertPhoneNumber($request->phone);
        if (count($pages) <= $this->getPageLimit($user)) {
            $this->setPageAndInfoUser($user, $pages, $request->name, $request->email, $phone);
            return response()->json([
              'success' => true
            ]);
        }
        return response()->json([
            'message' => 'Page limit'
        ], ERR_CODE);
    }

    public function setPageAndInfoUser(User $user, array $pageIds, $name, $email, $phone)
    {
        $adminId = $user->adminId();
        if (!$user->isWare()) {
            $user->pages()->whereIn('id', $pageIds)->update(['active' => 1]);
            $user->pages()->whereNotIn('id', $pageIds)->update(['active' => 0]);
            if ($user->isAdmin()) {
                // register receive notification from page active
                $pagesActive = $user->pages()->whereIn('id', $pageIds)->get();
                foreach ($pagesActive as $page) {
                    $this->subcribeApp($page->fb_page_id, $page->page_token);
                }
            }
            // create some conversation for account new
            $countConver = (new ConverRespository($adminId))->getFirst([
                ['user_id', $adminId]
            ]);
            if (!$countConver) {
                (new ConversationMongoController)->createSomeConversationForAccountNew($user);
            }
            if ($user->isAdmin()) {
                // remove receive notification from page not active
                $pagesNotActive = $user->pages()->whereNotIn('id', $pageIds)->get();
                foreach ($pagesNotActive as $page) {
                    $this->unsubcribeApp($page->fb_page_id, $page->page_token);
                }
            }
        }
        $user->update([
            'name' => $name,
            'user_email' => $email,
            'user_phone' => $phone
        ]);
    }

    /**
     * create new user with facebook login
     * @param  [type]  $user_fb_id        [description]
     * @param  [type]  $user_access_token [description]
     * @param  [type]  $name              [description]
     * @param  [type]  $user_phone        [description]
     * @param  [type]  $user_fb_email     [description]
     * @param  integer $parent_user_id    [description]
     * @return [type]                     [description]
     */
    public function createAccountWithFb($user_fb_id, $user_access_token, $name, $user_phone, $user_fb_email, $parent_user_id = 0)
    {
        $str = $this->convertStrWithId($user_fb_id);
        $username = $str['username'];
        $password = Hash::make($str['password']);

        $newAccount = User::create([
            'name' => $name,
            'username' => $username,
            'password' => $password,
            'user_fb_id' => (binary) $user_fb_id,
            'user_access_token' => $user_access_token,
            'user_phone' => $user_phone,
            'user_fb_email' => $user_fb_email,
            'parent_user_id' => $parent_user_id
        ]);
        $newUser = User::find($newAccount->id);
        $this->createRoleForUser($newUser, 'ADMINSTRATOR');
        SettingController::createGeneralSetting($newUser);
        return $newUser;
    }

    /**
     * generate user with facebook ID
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function convertStrWithId($id)
    {
        //reverse id facebook
        $str = strval($id);
        $result['username'] = '';
        $result['password'] = '';
        $result['username'] = strrev($str);
        //get 3 character first of id facebook
        $a = substr($str, 0, 3);
        //get 4 character end of id facebook
        $b = substr($str, strlen($str) - 4, 4);
        $mid = strlen($str)%2;
        $c = substr($str, strlen($str) - 5, 3);
        $result['password'] = $b . $a . $mid . $c . '_&cs';
        return $result;
    }

    /**
     * check has account with Facebook ID
     * @param  [type]  $id [facebook ID]
     * @return boolean     [description]
     */
    public function hasAccountFb($id)
    {
        $id = (binary) $id;
        $user = User::where('user_fb_id', $id)->first();
        return empty($user) ? false : true;
    }

    /**
     * get all package account fee
     * @return [type] [description]
     */
    public function getUpgradeAccount()
    {
        $packages = Package::all('display_name', 'price', 'id');
        return response()->json([
            'data' => $packages
        ]);
    }

    /**
     * create order account fee
     * @param  CreateUserPaymentRequest $request [description]
     * @return [type]                            [description]
     */
    public function ugradeAccount(CreateUserPaymentRequest $request)
    {
        $package = $request->package;
        $monthTime = $request->time_expire;
        $pack = Package::find($package);
        if (!empty($pack)) {
            $user = Auth::user();
            $unpaid = UserPayment::where('user_id', $user->id)
                                    ->where('paid', false)
                                    ->first();
            if (!empty($unpaid)) {
                return response()->json([
                    'message' => 'you have a payment unpaid'
                ], ERR_CODE);
            }
            $amount = $pack->price * $monthTime;
            $payment = UserPayment::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'price' => $pack->price,
                'total_payable' => $amount,
                'total_after_discount' => $amount,
                'duration' => $monthTime,
                'package_id' => $pack->id,
                'pay_code' => $this->generatePayCode()
            ]);
            return response()->json([
                'success' => true,
                'data' => $payment
            ]);
        }
        return response()->json([
            'does not exist package'
        ], ERR_CODE);
    }

    /**
     * get all order of user unpaid
     * @return [type] [description]
     */
    public function getUnpaid()
    {
        $user = Auth::user();
        $unpaid = UserPayment::where('user_id', $user->id)
                                ->where('paid', false)
                                ->get();
        return response()->json([
            'data' => $unpaid
        ]);
    }

    public function getInfoSumary()
    {
        $user = User::select('name', 'user_phone', 'user_email', 'id', 'user_fb_id')->find(Auth::id());
        $role = $user->roles()->select('display_name', 'name')->first();
        $setting = $user->generalSetting()->first();
        if ($setting) {
            $setting->key_cmt_hide = json_decode(base64_decode($setting->key_cmt_hide));
            $setting->key_cmt_priority = json_decode(base64_decode($setting->key_cmt_priority));
        }
        $package = $user->packagesActive();
        $packageZip = null;
        if (!empty($package)) {
            $packageZip['name'] = $package->name;
            $packageZip['display_name'] = $package->display_name;
            $packageZip['expire_at'] = $package->pivot->expire_at;
        }
        return response()->json([
            'user' => $user,
            'setting' => $setting,
            'role' => $role,
            'package' => $packageZip
        ]);
    }

    public function getPagesActive()
    {
        $userId = Auth::id();
        $pages = Page::where('user_id', $userId)
                        ->where('active', true)
                        ->get();
        return response()->json([
            'pages' => $pages
        ]);
    }

    public function activeItemPage($fbPageId)
    {
        $user = Auth::user();
        $page = DB::table('pages')
                    ->leftJoin('users', 'users.id', '=', 'pages.user_id')
                    ->whereNull('users.parent_user_id')
                    ->where('pages.fb_page_id', $fbPageId)
                    ->where('pages.active', true)
                    ->where('pages.user_id', '<>', $user->id)
                    ->first();
        $response = [
            'message' => '',
            'success' => 0
        ];
        if (empty($page)) {
            $pageUser = Page::where('fb_page_id', $fbPageId)
                                ->where('user_id', $user->id)
                                ->first();
            if ($pageUser) {
                $pageUser->update(['active' => true]);
                SubcribeApp::dispatch($pageUser->fb_page_id, $pageUser->page_token, 'SUBCRIBE')->onQueue('actionWithFace');
                $response['success'] = 1;
            } else {
                $response['message'] = 'Can not find page';
            }
        } else {
            $response['message'] = 'page is actived by another user';
        }
        return response()->json($response);
    }

    public function deactiveItemPage($fbPageId)
    {
        $user = Auth::user();
        $page = Page::where('fb_page_id', $fbPageId)
                    ->where('user_id', $user->id)
                    ->first();
        $response = [
            'success' => 0,
            'message' => '',
            'status' => ERR_CODE
        ];
        if ($page) {
            $page->update(['active' => false]);
            SubcribeApp::dispatch($page->fb_page_id, $page->page_token, 'UNSUBCRIBE')->onQueue('actionWithFace');
            $response['success'] = 1;
            $response['status'] = SUCCESS_CODE;
        } else {
            $response['message'] = 'Can not find page';
        }
        return response()->json($response, $response['status']);
    }
}
