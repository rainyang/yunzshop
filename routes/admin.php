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
    Route::group(['prefix' => 'system'], function (){
        // 站点设置
        Route::get('site', ['as' => 'admin.system.site', 'uses' => '\app\platform\models\system\controllers\SiteController@index']);
    });

    Route::group(['namespace' => 'platform\modules\application\controllers'], function () {
		// 平台管理
		Route::get('application/', 'ApplicationController@index');
		Route::post('application/{id}', 'ApplicationController@update');
		Route::get('application/switchStatus/{id}', 'ApplicationController@switchStatus');
		Route::post('application/', 'ApplicationController@add');
		Route::delete('application/{id}', 'ApplicationController@delete');

		//平台用户管理
		Route::get('appuser/', 'AppuserController@index');
		Route::post('appuser/{id}', 'AppuserController@update');
		Route::post('appuser/', 'AppuserController@add');
		Route::get('appuser/{id}', 'AppuserController@delete');
	});
});

Route::get('/', function () {
    return redirect('/admin/index');
});