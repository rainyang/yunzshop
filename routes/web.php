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

Route::any('/', function () {
    //如未设置当前公众号则加到选择公众号列表
    if(!YunShop::app()->uniacid){
        return redirect('?c=account&a=display');
    }
    //解析商城路由
    YunShop::parseRoute();
});

