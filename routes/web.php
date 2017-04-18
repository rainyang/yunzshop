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
header('Access-Control-Allow-Origin: http://localhost:8081' );
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

Route::any('/', function () {

    //支付回调
    if (strpos(request()->getRequestUri(), '/payment/') !== false) {
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
    if (strpos(request()->getRequestUri(), '/addons/') !== false &&
        strpos(request()->getRequestUri(), '/api.php') !== false
    ) {
        YunShop::parseRoute(request()->input('route'));
        return;
    }
    //shop.php
    if (strpos(request()->getRequestUri(), '/addons/') !== false &&
        strpos(request()->getRequestUri(), '/shop.php') !== false
    ) {
        YunShop::parseRoute(request()->input('route'));
        return;
    }
    //任务调度
    if (strpos(request()->getRequestUri(), '/addons/') !== false &&
        strpos(request()->getRequestUri(), '/cron.php') !== false
    ) {
        // Get security key from config
        $cronkeyConfig = \Config::get('liebigCron.cronKey');

        // If no security key is set in the config, this route is disabled
        if (empty($cronkeyConfig)) {
            \Log::error('Cron route call with no configured security key');
            \App::abort(404);
        }

        // Get security key from request
        $cronkeyRequest = request()->get('key');
        // Create validator for security key
        $validator = \Validator::make(
            array('cronkey' => $cronkeyRequest),
            array('cronkey' => 'required|alpha_num')
        );
        if ($validator->passes()) {
            if ($cronkeyConfig === $cronkeyRequest) {
                \Artisan::call('cron:run', array());
            } else {
                // Configured security key is not equals the sent security key
                \Log::error('Cron route call with wrong security key');
                \App::abort(404);
            }
        } else {
            // Validation not passed
            \Log::error('Cron route call with missing or no alphanumeric security key');
            \App::abort(404);
        }
        return;
    }

    //微信回调
    if (strpos(request()->getRequestUri(), '/addons/') === false &&
        strpos(request()->getRequestUri(), '/api.php') !== false
    ) {

        return;
    }
    if (strpos(request()->getRequestUri(), '/app/') !== false) {
        return redirect('/addons/yun_shop/#/home?i='.request()->get('i'));
    }
    //后台
    if (strpos(request()->getRequestUri(), '/web/') !== false) {
        //如未设置当前公众号则加到选择公众号列表
        if (!YunShop::app()->uniacid) {
            return redirect('?c=account&a=display');
        }

        //解析商城路由
        if(YunShop::request()->route){
            YunShop::parseRoute(YunShop::request()->route);
        }else{
            return redirect('?c=site&a=entry&do=shop&m=yun_shop&route=index.index');

        }
    }
    return;
});
