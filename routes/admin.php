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

Route::group(['middleware' => ['auth:admin', 'authAdmin']], function () {

    Route::get('index', ['as' => 'admin.index', 'uses' => '\app\platform\controllers\IndexController@index']);

    // 站点管理
    Route::group(['prefix' => 'system'], function (){
        // 站点设置
        Route::get('site', ['as' => 'admin.system.site', 'uses' => '\app\platform\models\system\controllers\SiteController@index']);
    });
});

Route::get('/', function () {
    return redirect('/admin/index');
});