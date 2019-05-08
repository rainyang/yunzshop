<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/27
 * Time: 下午4:56
 */

namespace app\common\middleware;



class GlobalParams
{
    public function handle($request, \Closure $next, $guard = null)
    {
        $this->setConfigInfo();

        $this->checkClear();

        return $next($request);
    }

    /**
     * 获取全局参数
     *
     * @return array
     */
    private function setConfigInfo()
    {
        global $_W;
        
        $_W['uid'] = \Auth::guard('admin')->user()->uid;
        $_W['username'] = \Auth::guard('admin')->user()->username;
        \config::set('app.global.uid', \Auth::guard('admin')->user()->uid);
        \config::set('app.global.username', \Auth::guard('admin')->user()->username);
        \YunShop::app()->uid        = \Auth::guard('admin')->user()->uid;
        \YunShop::app()->username   = \Auth::guard('admin')->user()->username;
    }

    /**
     * 为了兼容 供应商、门店、分公司、酒店、区域代理 独立后台登录
     */
    public function checkClear()
    {
        if (app('plugins')->isEnabled('supplier')) {
            include base_path() . '/plugins/supplier/menu.php';
        }

        if (app('plugins')->isEnabled('store-cashier')) {
            include base_path() . '/plugins/store-cashier/storeMenu.php';
        }

        if (app('plugins')->isEnabled('subsidiary')) {
            include base_path() . '/plugins/subsidiary/subsidiaryMenu.php';
        }

        if (app('plugins')->isEnabled('hotel')) {
            include base_path() . '/plugins/hotel/hotelMenu.php';
        }

        if (app('plugins')->isEnabled('area-dividend')) {
            include base_path() . '/plugins/area-dividend/area_admin.php';
        }
    }
}