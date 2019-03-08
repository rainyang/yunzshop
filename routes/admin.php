<?php
Route::group(['namespace' => 'platform\controllers'], function () {
    Route::get('login', 'LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout');
    Route::post('logout', 'LoginController@logout');

    Route::get('register', 'RegisterController@showRegistrationForm')->name('admin.register');
    Route::post('register', 'RegisterController@register');

    Route::get('/', 'IndexController@index');
});

Route::group(['middleware' => ['auth:admin', 'authAdmin', 'globalparams']], function () {

    Route::get('index', ['as' => 'admin.index', 'uses' => '\app\platform\controllers\IndexController@index']);

    // 站点管理
    Route::group(['prefix' => 'system', 'namespace' => 'platform\modules\system\controllers'], function (){
        // 站点设置
        Route::any('site', 'SiteController@index');
        // 附件设置-全局设置
        Route::any('attachment', 'AttachmentController@index');
        // 系统升级
        Route::any('update/index', 'UpdateController@index');
        // 检查更新
        Route::get('update/verifyCheck', 'UpdateController@verifyCheck');
        // 更新
        Route::any('update/fileDownload', 'UpdateController@fileDownload');
        // 版权
        Route::any('update/pirate', 'UpdateController@pirate');
        // 初始程序
        Route::any('update/startDownload', 'UpdateController@startDownload');
        /* 上传 */
        // 图片
        Route::any('upload/image', 'UploadController@image');
    });

    // 用户管理
    Route::group(['prefix' => 'user', 'namespace' => 'platform\modules\user\controllers'], function (){
        // 用户列表
        Route::get('list', 'AdminUserController@index');
        // 添加用户
        Route::any('add', 'AdminUserController@add');
        // 用户编辑
        Route::any('edit', 'AdminUserController@edit');
        // 用户修改状态
        Route::any('status', 'AdminUserController@status');


    });


    Route::group(['namespace' => 'platform\modules\application\controllers'], function () {
		// 平台管理
		Route::get('application/', 'ApplicationController@index');
		//修改应用
		Route::post('application/{id}', 'ApplicationController@update');
		//启用禁用或恢复应用及跳转链接
		Route::get('application/switchStatus/{id}', 'ApplicationController@switchStatus');
		//添加应用
		Route::post('application/', 'ApplicationController@add');
		//删除 加入回收站
		Route::delete('application/{id}', 'ApplicationController@delete');
		//回收站
		Route::get('application/recycle/', 'ApplicationController@recycle');

		//平台用户管理
		// Route::get('appuser/', 'AppuserController@index');
		Route::post('appuser/{id}', 'AppuserController@update');
		Route::get('appuser/', 'AppuserController@add');
		Route::get('appuser/{id}', 'AppuserController@delete');
	});
});

Route::get('/', function () {
    return redirect('/admin/index');
});