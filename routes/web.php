<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

Route::get('/', 'client\AuthController@getLogin')->name('getLogin');
Route::get('login-facebook-sdk', 'Web\AccountController@loginFromFace')->name('loginFromFace');
Route::post('/login', 'client\AuthController@loginWithInfoFb')->name('login');
Route::get('/no-permission', function() {
	return view('no_permission');
})->name('no_permission');
Route::get('chat-support', function() {
	return view('supports.vchat_support');
})->name('supportChat');

Route::group(['prefix' => 'blog'], function() {
	Route::get('/', 'Blog\HomeController@index')->name('blog.home');
	Route::post('up-view/{id}', 'Blog\PostController@upViews')->name('upViewsBlog');
	Route::get('/{slug}', 'Blog\PostController@index');
	Route::get('category/{slug}', 'Blog\CategoryController@index');
	Route::get('not-found', 'BLog\HomeController@notFound');
});

// Route::get('check-acc/not-active/A061018', 'Web\AccountController@checkUserNotActive');
// Route::get('check-acc/not-active/check-redis/A061018', 'Web\AccountController@checkingRedisUser');
// Route::get('tranfer-data/conversation/2d3zA/{id}', 'Admin\TransferDataController@conversations');
// Route::get('tranfer-data-every/conversation/fasd12', 'Admin\TransferDataController@tranferEveryUser');
// Route::get('reset-redis/check-online-user/vzf221', 'Admin\TransferDataController@resetCheckOnlineUser');
// Route::get('reset-redis/check-online-user/reset', 'Admin\TransferDataController@resetRedis');

Route::get('roles/infomation', function() {
	return view('login.rolesInfo');
})->name('info.roles');
Route::get('detail/prices', function() {
	return view('login.prices_info');
})->name('info.prices');

Route::get('webhook/receive-data/acV123X', 'Facebook\WebhookController@webhook');
Route::post('webhook/receive-data/acV123X', 'Facebook\WebhookController@receiveWebhook');

Route::group(['middleware' => ['authen']], function() {

	Route::get('/logout', 'client\AuthController@logout')->name('logout');
	Route::group(['prefix' => 'errors-account'], function() {
		Route::get('blocked', 'Web\AccountController@blockedAccount')->name('client.account.blocked');
	});
	
	Route::group(['middleware' => ['checkHasBlocked']], function() {
		
		Route::group(['prefix' => 'setup'], function() {
			Route::get('pages', 'Web\AccountController@getSetInfo')->name('set.info');
			Route::post('pages/create', 'Web\AccountController@setInfo')->name('create.user.info');
			Route::get('accounts/confirm', 'Web\AccountController@getConfirmInvite')->name('confirm.role');
		});

		Route::group(['middleware' => 'checkRole', 'roles' => 'ADMINSTRATOR'], function () {

			Route::get('/conversations/getCountUnread', 'client\ConversationMongoController@getCountUnread');
			Route::get('/setup/accounts', 'Web\AccountController@getSetRoles')->name('set.role');
			Route::get('customers/export/{type}', 'client\CustomerController@exportFileCustomer');
			Route::get('filter-infomation/export/email/{type}', 'client\SettingController@exportEmailMarketting');
			Route::get('filter-infomation/export/phone/{type}', 'client\SettingController@exportPhoneMarketting');
			Route::get('transports/export/{type}', 'client\OrderController@exportTransportList');
			Route::get('products/export/{type}', 'client\ProductController@exportProductList');
			Route::get('imp-product/export/{type}', 'client\ProductController@exportImportList');
			Route::get('exp-product/export/{type}', 'client\ProductController@exportFileListExport');
			Route::get('campaigns/export/{type}', 'client\CampaignController@exportFileCampaign');
			Route::get('/plugins/fb-chat/demo', 'client\PluginController@fbChatDemo');
			Route::get('/plugins/box-chat/demo', 'client\PluginController@boxFbChatDemo');
		});
		Route::group(['middleware' => ['checkHasPage', 'checkPageLimit']], function() {
			Route::group(['middleware' => 'checkRole', 'roles' => ['ADMINSTRATOR', 'MANAGER', 'SALER']], function () {
				Route::get('/conversations', 'client\ConversationMongoController@showConversations')->name('conversations');
			});
		});
		Route::group(['middleware' => 'checkRole', 'roles' => ['ADMINSTRATOR', 'MANAGER', 'SALER', 'STORAGER']], function() {
			Route::get('/product', function() {
				return view('client.layout');
			})->name('products');
			Route::any('/{parent?}/{child1?}/{child2?}', function($any) {
				return view('client.layout');
			})->where('parent', 'customers|orders|statistic|setting');
		});
	});
});

/*
errors
 */
// 404 not found
Route::get('not-found', function() {
	return response()->view('errors.404', [], 404);
});
// redirect 302
Route::get('{any}', function ($any) {
	$link = base64_encode($any);
	$redirect = DB::table('redirect_links')->where('from_link', $link)->first();
	if ($redirect) {
		return redirect(base64_decode($redirect->to_link));
	}
	return response()->view('errors.404', [], 404);
});





