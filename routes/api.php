<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'crawl-phone'], function() {
	Route::get('get-uid', 'Api\Admin\ExtractorFacebookController@getScan');
	Route::post('import-uid-phone', 'Api\Admin\ExtractorFacebookController@createPhone');
});

Route::group(['middleware' => ['auth:api']], function() {

	Route::group(['middleware' => 'checkHasBlocked_Api'], function() {

		Route::get('/infomation/user', 'Api\UserController@getInfoUser');
		Route::post('/register/device/mobile', 'Api\UserController@registerDeviceMobile');
		Route::post('/unregister/device/mobile', 'Api\UserController@unregisterDeviceMobile');
		
		Route::get('infomation/sumary', 'client\ApiController@getInfoUser');
		Route::get('/provinces/get', 'client\ApiController@getProvinces');
		Route::get('/districts/get/{id}', 'client\ApiController@getDistricts');
		Route::get('/wards/get/{id}', 'client\ApiController@getWards');
		Route::get('address/all', 'client\ApiController@getAddress');
		/*
		for mobile
		 */
		// Page
		Route::group(['prefix' => 'pages'], function() {
			Route::get('hasRole', 'Api\UserController@getSetInfoApi');
			Route::post('active', 'Api\UserController@setInfoApi');
			Route::get('is-active', 'Api\UserController@getPagesActive');
			Route::post('active-item/{pageId}', 'Api\UserController@activeItemPage');
			Route::delete('deactive-item/{pageId}', 'Api\UserController@deactiveItemPage');
		});
		// User
		Route::group(['prefix' => 'profile'], function () {
			Route::get('/{id}', 'Api\UserController@getProfile');
			Route::post('/update/{id}', 'Api\UserController@updateProfile');
		});
		// Role user
		Route::group(['prefix' => 'roles'], function() {
			Route::get('invite/confirm', 'Api\UserController@getConfirmInvite');
			Route::post('invite/confirm/{id}', 'Api\UserController@confirmInvite');
			Route::delete('invite/deny/{id}', 'Api\UserController@denyInvite');
			Route::delete('invite/clear', 'Api\UserController@clearInvite');
		});
		Route::group(['prefix' => 'convert-uid'], function() {
			Route::get('psid-to-phone/{id}', 'Api\ExtratorFacebook\ConvertUidPhoneController@convertPSIDToPhone');
			Route::get('psid-to-link/{id}', 'Api\ExtratorFacebook\ConvertUidPhoneController@convertPSIDToLinkFacebook');
		});
		/*
		end for mobile
		 */
		Route::group(['middleware' => 'checkRoleApi', 'roles' => 'ADMINSTRATOR'], function() {
			Route::get('/pages/{fb_page_id}/staffs', 'Api\UserController@getPageAdmin');
			Route::get('/pages/{fb_page_id}/staffs/{fb_user_id}', 'Api\UserController@getRolesStaff');
			Route::post('/pages/roles/update/{fb_page_id}/{fb_user_id}', 'Api\UserController@setRoles');
			Route::post('pages/roles/destroy/{fb_page_id}/{fb_user_id}', 'Api\UserController@destroyRoleOfStaffWithPage');
			Route::post('pages/staffs/destroy/{fb_user_id}', 'Api\UserController@destroyStaff');
			Route::group(['prefix' => 'account'], function () {
				Route::get('upgrade', 'Api\UserController@getUpgradeAccount');
				Route::post('upgrade', 'Api\UserController@ugradeAccount');
				Route::get('upgrade/unpaid', 'Api\UserController@getUnpaid');
			});
		});

		Route::group(['middleware' => 'checkRoleApi', 'roles' => ['ADMINSTRATOR', 'MANAGER']], function() {
			Route::group(['prefix' => 'statistic'], function() {
				Route::get('summary','client\SummaryController@overview');
				Route::get('sales','client\SummaryController@getSaleData');
				Route::get('top10Post','client\SummaryController@getTop10Post');
				Route::get('top10Product','client\SummaryController@getTop10Products');
				Route::get('conversations','client\SummaryController@getConversationData');
				Route::get('staffData','client\SummaryController@getDataStaff');
			});

			Route::group(['prefix' => 'setting'], function() {
				Route::delete('black-list/destroy/{id}', 'client\SettingController@destroyReport');
				Route::post('general/update', 'client\SettingController@updateGeneralSetting');
				Route::get('mix/whitelisted-site/get/{token}', 'client\ApiController@getWhitelistedDomains');
				Route::post('mix/whitelisted-site', 'client\ApiController@saveWhitelistedSite');
				Route::get('filter-infomation/pages', 'client\SettingController@getListPages');
				Route::get('filter-infomation/infomation', 'client\SettingController@getInfoMarketting');
				Route::get('auto-reply', 'client\SettingController@getAutoSetting');
				Route::post('auto-reply', 'client\SettingController@updateAutoSetting');
			});

			Route::group(['prefix' => 'extensions'], function() {
				Route::post('publish/post', 'client\PublishContentFaceController@publishPost');
				Route::get('publish/list-post', 'client\PublishContentFaceController@getListPublishPost');
				Route::get('publish/list-schedule-post', 'client\PublishContentFaceController@getListSchedulePost');
				Route::get('publish/edit/{id}', 'client\PublishContentFaceController@editPost');
				Route::post('publish/update/{id}', 'client\PublishContentFaceController@updatePost');
				Route::delete('publish/destroy/{id}', 'client\PublishContentFaceController@destroyPost');
				Route::post('publish/publish-now/{id}', 'client\PublishContentFaceController@publishPostNow');
			});
		});

		Route::group(['middleware' => 'checkRoleApi', 'roles' => ['ADMINSTRATOR', 'SALER', 'MANAGER']], function() {

			Route::group(['prefix' => 'conversations', 'middleware' => 'checkPageLimit_Api'], function() {
				Route::get('list', 'client\ConversationMongoController@getListConversations');
				Route::get('/{id}', 'client\ConversationMongoController@loadConversation');
				Route::post('/reply/{id}', 'client\ConversationMongoController@replyConversation');
				Route::post('create/comment', 'client\ConversationMongoController@createCommentWithIdMedia');
				Route::post('create/message', 'client\ConversationMongoController@createMessageWithIdMedia');
				Route::post('delete/comment/{conversationId}/{id}', 'client\ConversationMongoController@deleteComment');
				Route::get('messages/private/{id}', 'client\ConversationMongoController@getMessagesWithIdFb');
				Route::post('messages/private/{id}', 'client\ConversationMongoController@sendMessagesWithPost');
				Route::post('comments/hide/{id}', 'client\ConversationMongoController@hideComment');
				Route::post('comments/like/{id}', 'client\ConversationMongoController@likeComment');
				Route::post('markRead/{id?}', 'client\ConversationMongoController@markRead');
				Route::get('/unread/totalCount', 'client\ConversationMongoController@getCountUnread');
			});

			Route::group(['prefix' => 'orders'], function() {
				Route::post('create/quick', 'client\OrderController@quickCreate');
				Route::post('create/confirm/quick', 'client\OrderController@quickCreateConfirm');
				Route::get('create', 'client\OrderController@getCreateOrder');
				Route::post('create', 'client\OrderController@createOrder');
				Route::post('update', 'client\OrderController@updateOrder');
				Route::post('destroy', 'client\OrderController@destroyOrder');
				Route::post('save', 'client\OrderController@saveOrder');
				Route::post('quick/update/{id}', 'client\OrderController@quickUpdateOrder');
			});

			Route::group(['prefix' => 'setting'], function() {
				Route::post('/groups-customer/create', 'client\SettingController@createGroups');
				Route::delete('/groups-customer/destroy/{id}', 'client\SettingController@destroyGroup');
				Route::post('/groups-customer/update/{id}', 'client\SettingController@updateGroup');
				Route::post('/quick-reply/create', 'client\SettingController@createQuickAnswer');
				Route::post('/quick-reply/update/{id}', 'client\SettingController@updateQuickAnswer');
				Route::delete('/quick-reply/destroy/{id}', 'client\SettingController@destroyAnswer');
			});
		});

		Route::group(['middleware' => 'checkRoleApi', 'roles' => ['ADMINSTRATOR', 'MANAGER', 'STORAGER']], function() {
			Route::group(['prefix' => 'category'], function() {
				Route::post('create', 'client\CategoryController@create');
				Route::post('update/{id}', 'client\CategoryController@updateCate');
				Route::delete('destroy/{id}', 'client\CategoryController@destroyCate');
			});
			Route::group(['prefix' => 'product'],function() {
				Route::get('properties', 'client\ProductController@getProperties');
				Route::post('properties/create', 'client\ProductController@createProp');
				Route::post('properties/update/{id}', 'client\ProductController@updateProp');
				Route::delete('properties/destroy/{id}', 'client\ProductController@destroyProperty');
				Route::get('properties/edit/{id}', 'client\ProductController@getEditProp');
				Route::delete('properties/values/destroy/{id}', 'client\ProductController@destroyValueProp');
				Route::get('add', 'client\ProductController@getAddProduct');
				Route::post('uploads/images', 'client\ProductController@doUploads');
				Route::delete('destroy/images', 'client\ProductController@destroyImage');
				Route::post('create', 'client\ProductController@createProduct');
				Route::get('get/list', 'client\ProductController@getListProducts');
				Route::get('{id}/childs', 'client\ProductController@getProductChilds');
				Route::get('edit/{id}', 'client\ProductController@getEditProduct');
				Route::post('update/{id}', 'client\ProductController@updateProduct');
				Route::post('destroy/{id}', 'client\ProductController@destroyProduct');
				Route::get('available', 'client\ProductController@getProductsTodo');
				Route::post('import', 'client\ProductController@importProducts');
				Route::get('import/list', 'client\ProductController@getListImports');
				Route::get('import/get/{id}', 'client\ProductController@getEntityImport');
				Route::post('import/cancel/{id}', 'client\ProductController@cancelImportion');
				Route::post('import/doing/{id}', 'client\ProductController@doImportion');
				Route::get('export/list', 'client\ProductController@getListExport');
				Route::post('export/create', 'client\ProductController@createExportBallot');
				Route::get('export/{id}', 'client\ProductController@getDetailExport');
				Route::post('pushToCate', 'client\ProductController@pushProductsToCate');
				Route::get('history/changes/{id}', 'client\ProductController@getChangeHistory');
			});

			Route::group(['prefix' => 'campaign'],function() {
				Route::post('create', 'client\CampaignController@createCampaign');
				Route::post('update/{id}', 'client\CampaignController@updateCampaign');
				Route::post('pause/{id}', 'client\CampaignController@pauseCampaign');
				Route::post('run/again/{id}', 'client\CampaignController@runAgainCampaign');
			});
		});

		Route::group(['prefix' => 'product'], function() {
			Route::get('search', 'client\ProductController@searchProducts');
			Route::get('/sumary/info', 'client\ProductController@getInfoSummaryProduct');
		});

		Route::group(['prefix' => 'category'], function() {
			Route::get('get/all', 'client\CategoryController@getAll');
			Route::get('get/list', 'client\CategoryController@getListCates');
			Route::get('products/quantity/{id}', 'client\CategoryController@getQuantityProduct');
			Route::get('{id}', 'client\CategoryController@getCate');
		});

		Route::group(['prefix' => 'campaign'],function() {
			Route::get('list', 'client\CampaignController@getListCampaign');
			Route::get('{id}', 'client\CampaignController@getCampaign');
		});

		Route::group(['prefix' => 'orders'], function() {
			Route::get('get', 'client\OrderController@getListOrder');
			Route::get('edit/{id}', 'client\OrderController@getEditOrder');
			Route::get('order-new/count', 'client\OrderController@countNewOrder');
		});

		Route::group(['prefix' => 'transports'], function() {
			Route::get('get', 'client\OrderController@getListTransports');
		});
		
		Route::group(['prefix' => 'customers'], function() {
			Route::get('infomation/{id}', 'client\CustomerController@getInfoCustomer');
			Route::get('infomation/fb/{id}', 'client\CustomerController@getInfoCustomerWithIdFb');
			Route::get('list', 'client\CustomerController@getListCustomer');
			Route::get('add', 'client\CustomerController@getAddCustomer');
			Route::post('add', 'client\CustomerController@addCustomer');
			Route::post('quick/add', 'client\CustomerController@quickAddCustomer');
			Route::post('notes/quick/add', 'client\CustomerController@quickAddNote');
			Route::delete('notes/destroy/{customerId}/{noteId}', 'client\CustomerController@destroyNote');
			Route::get('detail/{id}', 'client\CustomerController@detailCustomer');
			Route::post('update/{id}', 'client\CustomerController@updateDetailCustomer');
			Route::delete('destroy', 'client\CustomerController@destroyCustomers');
			Route::post('pinGroup/{id?}', 'client\CustomerController@pinGroupForCus');
			Route::post('outGroup/{id}/{groupId}', 'client\CustomerController@outGroupForCus');
			Route::get('search', 'client\CustomerController@searchCustomer');
		});

		Route::group(['prefix' => 'setting'], function() {
			Route::get('/groups-customer/all', 'client\SettingController@getGroups');
			Route::get('general/data', 'client\SettingController@getGeneralSetting');
			Route::get('/quick-reply/get', 'client\SettingController@getQuickAnswer');
			Route::get('black-list/all', 'client\SettingController@getBlackList');
			Route::post('black-list/create', 'client\SettingController@createCustomerBlack');
			Route::get('black-list/customer', 'client\SettingController@getReportsCustomer');
			Route::post('black-list/update', 'client\SettingController@updateReports');
		});
	});
});




