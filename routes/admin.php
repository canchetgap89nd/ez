<?php 

Route::get('login', 'Admin\AuthController@prepareLogin')->name('login.admin');
Route::post('login', 'Admin\AuthController@login');

Route::group(['middleware' => ['authenAdmin']], function() {
	Route::get('logout', 'Admin\AuthController@logout');
	Route::get('dashboard', 'Admin\DashboardController@index')->name('admin.dashboard');

	Route::group(['prefix' => 'accounts'], function() {
		Route::get('all', 'Admin\AccountsController@index')->name('admin.accounts');
		Route::get('activities/{id}', 'Admin\AccountsController@getActivities')->name('admin.accounts.activities');
		Route::get('activities-every-day', 'Admin\AccountsController@getActivityEveryDays')->name('admin.accounts.activitiesDays');
		Route::get('detail/{id}', 'Admin\AccountsController@getDetailUser')->name('admin.accounts.detail');
	});
	Route::get('conversations', 'Admin\ConversationController@index')->name('admin.conversations');

	Route::group(['prefix' => 'redirect-links'], function() {
		Route::get("get", "Blog\RedirectController@index")->name('redirect302');
		Route::get("create", "Blog\RedirectController@getCreateRedirectLink")->name('getCreateRedirect302');
		Route::post("create", "Blog\RedirectController@createRedirectLink")->name('createRedirect302');
		Route::get("edit/{id}", "Blog\RedirectController@editRedirectLink")->name('editRedirect302');
		Route::post("update/{id}", "Blog\RedirectController@updateRedirectLink")->name('updateRedirect302');
		Route::delete("destroy/{id}", "Blog\RedirectController@destroyRedirectLink")->name('destroyRedirect302');
	});

	Route::group(['prefix' => 'import'], function() {
		Route::get('uid', 'Admin\ExtractorFacebookController@getImportUid')->name('admin.getImport.uid');
		Route::post('uid', 'Admin\ExtractorFacebookController@importUid')->name('admin.import.uid');
		Route::get('uid/all', 'Admin\ExtractorFacebookController@getAllUid')->name('admin.uid.all');
		Route::get('phone/all', 'Admin\ExtractorFacebookController@getAllPhone')->name('admin.phone.all');
		Route::get('scan/phone', 'Admin\ExtractorFacebookController@scanPhone')->name('admin.scan.phone');
		Route::get('scan/phone/begin', 'Admin\ExtractorFacebookController@startScanPhone')->name('admin.scan.phone.start');
		Route::get('phone', 'Admin\ExtractorFacebookController@getImportPhone')->name('admin.getImport.phone');
		Route::post('phone', 'Admin\ExtractorFacebookController@importPhone')->name('admin.import.phone');
	});

	Route::group(["prefix" => "blog"], function() {
		Route::get("generate/slug", "Admin\GeneralController@generateSlug");
		Route::group(["prefix" => "post"], function() {
			Route::get("/", "Admin\PostController@index")->name("blog.all.post");
			Route::get("create", "Admin\PostController@add")->name("blog.add.post");
			Route::post("create", "Admin\PostController@create")->name("blog.create.post");
			Route::get("edit/{id}", "Admin\PostController@edit")->name("blog.edit.post");
			Route::post("update/{id}", "Admin\PostController@update")->name("blog.update.post");
			Route::get("destroy/{id}", "Admin\PostController@destroy")->name('blog.delete.post');
			Route::post('active/{id}', "Admin\PostController@active");
			Route::post('deactivate/{id}', "Admin\PostController@deActivate");
		});
		Route::group(["prefix" => "category"], function() {
			Route::get("/", "Admin\CategoryController@index")->name("blog.all.category");
			Route::get("create", "Admin\CategoryController@add")->name("blog.add.category");
			Route::post("create", "Admin\CategoryController@create")->name("blog.create.category");
			Route::get("edit/{id}", "Admin\CategoryController@edit")->name("blog.edit.category");
			Route::post("update/{id}", "Admin\CategoryController@update")->name("blog.update.category");
			Route::get("destroy/{id}", "Admin\CategoryController@destroy")->name('blog.delete.category');
		});
	});

	Route::group(['prefix' => 'extractor'], function() {
		Route::get('createTokenLord', 'Admin\ExtractorFacebookController@getImportTokenLord')->name('admin.extractor.createTokenLord');
		Route::post('createTokenLord', 'Admin\ExtractorFacebookController@importTokenLord')->name('admin.extractor.storeTokenLord');
		Route::get('get-link-face/{id}', 'Admin\AccountsController@getLinkFace')->name('admin.getLinkFace');
	});

	Route::resource('package', 'Admin\PackageController');
	Route::resource('feature', 'Admin\FeatureController');
	Route::resource('payment', 'Admin\UserPaymentController');
	Route::resource('user-package', 'Admin\UserPackageController');
});

