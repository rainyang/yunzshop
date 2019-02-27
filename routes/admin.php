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
    
    //权限管理路由
    Route::get('permission/{parentId}/create', ['as' => 'admin.permission.create', 'uses' => 'PermissionController@create']);
    Route::get('permission/manage', ['as' => 'admin.permission.manage', 'uses' => 'PermissionController@index']);
    Route::get('permission/{parentId?}', ['as' => 'admin.permission.index', 'uses' => 'PermissionController@index']);
    Route::post('permission/index', ['as' => 'admin.permission.index', 'uses' => 'PermissionController@index']); //查询
    Route::resource('permission', 'PermissionController', ['names' => ['update' => 'admin.permission.edit', 'store' => 'admin.permission.create']]);
    //角色管理路由
    Route::get('role/index', ['as' => 'admin.role.index', 'uses' => 'RoleController@index']);
    Route::post('role/index', ['as' => 'admin.role.index', 'uses' => 'RoleController@index']);
    Route::resource('role', 'RoleController', ['names' => ['update' => 'admin.role.edit', 'store' => 'admin.role.create']]);
    //用户管理路由
    Route::get('user/index', ['as' => 'admin.user.index', 'uses' => 'UserController@index']);  //用户管理
    Route::post('user/index', ['as' => 'admin.user.index', 'uses' => 'UserController@index']);
    Route::resource('user', 'UserController', ['names' => ['update' => 'admin.user.edit', 'store' => 'admin.user.create']]);
    //插件管理路由
    Route::get('plugin/index', ['as' => 'admin.plugin.index', 'uses' => 'PluginController@index']);
    Route::post('plugin/index', ['as' => 'admin.plugin.index', 'uses' => 'PluginController@index']);
    Route::resource('plugin', 'PluginController', ['names' => ['update' => 'admin.plugin.edit', 'store' => 'admin.plugin.create']]);
    //插件版本管理路由
    Route::get('plugin-version/index', ['as' => 'admin.plugin-version.index', 'uses' => 'PluginVersionController@index']);
    Route::post('plugin-version/index', ['as' => 'admin.plugin-version.index', 'uses' => 'PluginVersionController@index']);
    Route::resource('plugin-version', 'PluginVersionController', ['names' => ['update' => 'admin.plugin-version.edit', 'store' => 'admin.plugin-version.create']]);

    //客户
    Route::get('customer/index', ['as' => 'admin.customer.index', 'uses' => 'CustomerController@index']);
    Route::post('customer/index', ['as' => 'admin.customer.index', 'uses' => 'CustomerController@index']);
    Route::post('customer/gray', ['as' => 'admin.customer.gray', 'uses' => 'CustomerController@gray']);
    Route::post('customer/black', ['as' => 'admin.customer.black', 'uses' => 'CustomerController@black']);

    Route::resource('customer', 'CustomerController', ['names' => ['update' => 'admin.customer.edit', 'store' => 'admin.customer.create']]);
    //客户应用
    Route::get('customer-apps/index', ['as' => 'admin.customer-apps.index', 'uses' => 'CustomerAppsController@index']);
    Route::post('customer-apps/index', ['as' => 'admin.customer-apps.index', 'uses' => 'CustomerAppsController@index']);
    Route::post('customer-apps/general', ['as' => 'admin.customer-apps.general', 'uses' => 'CustomerAppsController@general']);
    Route::resource('customer-apps', 'CustomerAppsController', ['names' => ['update' => 'admin.customer-apps.edit', 'store' => 'admin.customer-apps.create']]);
    //客户插件
    Route::get('customer-plugin/index', ['as' => 'admin.customer-plugin.index', 'uses' => 'CustomerPluginController@index']);
    Route::post('customer-plugin/index', ['as' => 'admin.customer-plugin.index', 'uses' => 'CustomerPluginController@index']);
    Route::post('customer-plugin/general', ['as' => 'admin.customer-plugin.general', 'uses' => 'CustomerPluginController@general']);
    Route::resource('customer-plugin', 'CustomerPluginController', ['names' => ['update' => 'admin.customer-plugin.edit', 'store' => 'admin.customer-plugin.create']]);

    //应用
    Route::get('apps/index', ['as' => 'admin.apps.index', 'uses' => 'AppsController@index']);
    Route::post('apps/index', ['as' => 'admin.apps.index', 'uses' => 'AppsController@index']);
    Route::resource('apps', 'AppsController', ['names' => ['update' => 'admin.apps.edit', 'store' => 'admin.apps.create']]);
    //应用版本管理
    Route::get('apps-version/index', ['as' => 'admin.apps-version.index', 'uses' => 'AppsVersionController@index']);
    Route::post('apps-version/index', ['as' => 'admin.apps-version.index', 'uses' => 'AppsVersionController@index']);
    Route::resource('apps-version', 'AppsVersionController', ['names' => ['update' => 'admin.apps-version.edit', 'store' => 'admin.apps-version.create']]);
    //订单
    Route::get('order/index', ['as' => 'admin.order.index', 'uses' => 'OrderController@index']);
    Route::post('order/index', ['as' => 'admin.order.index', 'uses' => 'OrderController@index']);
    Route::resource('order', 'OrderController', ['names' => ['update' => 'admin.order.edit', 'store' => 'admin.order.create']]);
    //产品
    Route::get('product/index', ['as' => 'admin.product.index', 'uses' => 'ProductController@index']);
    Route::post('product/index', ['as' => 'admin.product.index', 'uses' => 'ProductController@index']);
    Route::resource('product', 'ProductController', ['names' => ['update' => 'admin.product.edit', 'store' => 'admin.product.create']]);
    //会员
    Route::get('member/index', ['as' => 'admin.member.index', 'uses' => 'MemberController@index']);
    Route::post('member/index', ['as' => 'admin.member.index', 'uses' => 'MemberController@index']);
    Route::resource('member', 'MemberController', ['names' => ['update' => 'admin.member.edit', 'store' => 'admin.member.create']]);
    // After the line that reads
    Route::get('upload', ['as' => 'admin.upload.index', 'uses' =>'UploadController@index']);
    // Add the following routes
    Route::post('upload/file', ['as' => 'admin.upload.uploadFile', 'uses' => 'UploadController@uploadFile']);
    Route::delete('upload/file',['as' => 'admin.upload.deleteFile', 'uses' =>  'UploadController@deleteFile']);
    Route::post('upload/folder', ['as' => 'admin.upload.createFolder', 'uses' => 'UploadController@createFolder']);
    Route::delete('upload/folder',['as' => 'admin.upload.deleteFolder', 'uses' =>  'UploadController@deleteFolder']);

    //灰度测试
    Route::get('customer-gray/index', ['as' => 'admin.customer-gray.index', 'uses' => 'CustomerGrayController@index']);
    Route::post('customer-gray/index', ['as' => 'admin.customer-gray.index', 'uses' => 'CustomerGrayController@index']);
    Route::resource('customer-gray', 'CustomerGrayController', ['names' => ['update' => 'admin.customer-gray.edit', 'store' => 'admin.customer-gray.create']]);

    //黑名单
    Route::get('customer-black/index', ['as' => 'admin.customer-black.index', 'uses' => 'CustomerBlackController@index']);
    Route::post('customer-black/index', ['as' => 'admin.customer-black.index', 'uses' => 'CustomerBlackController@index']);
    Route::resource('customer-black', 'CustomerBlackController', ['names' => ['update' => 'admin.customer-black.edit', 'store' => 'admin.customer-black.create']]);

    //免费客户
    Route::get('customer-free/index', ['as' => 'admin.customer-free.index', 'uses' => 'CustomerFreeController@index']);
    Route::post('customer-free/index', ['as' => 'admin.customer-free.index', 'uses' => 'CustomerFreeController@index']);
    Route::post('customer-free/export', ['as' => 'admin.customer-free.export', 'uses' => 'CustomerFreeController@export']);

});

Route::get('/', function () {
    return redirect('/admin/index');
});