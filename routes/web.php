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
//@todo 接口api部份设置跨域
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

Route::any('/', function () {

    //支付回调
    if (strpos(request()->getRequestUri(), '/payment/') > 0) {
        preg_match('#(.*)/payment/(\w+)/(\w+).php(.*?)#', request()->getRequestUri(), $match);
        if (isset($match[2])) {
            $namespace = 'app\\payment\\controllers\\' . ucfirst($match[2]) . 'Controller';
            $modules = [];
            $controllerName = ucfirst($match[2]);
            $action = $match[3];
            $currentRoutes = [];
            Yunshop::run($namespace, $modules, $controllerName, $action, $currentRoutes);
        }
        return;
    }

    //api
    if (strpos(request()->getRequestUri(), '/api.php') > 0) {


        return;
    }
    //如未设置当前公众号则加到选择公众号列表
    if (!YunShop::app()->uniacid) {
        return redirect('?c=account&a=display');
    }

    //解析商城路由
    YunShop::parseRoute();
    return;
});

