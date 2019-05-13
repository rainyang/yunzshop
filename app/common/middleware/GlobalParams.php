<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/27
 * Time: 下午4:56
 */

namespace app\common\middleware;



use app\platform\modules\system\models\SystemSetting;

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

        $att = $this->getRemoteServicerInfo();
        
        $_W['uid'] = \Auth::guard('admin')->user()->uid;
        $_W['username'] = \Auth::guard('admin')->user()->username;
        $_W['attachurl'] = $att['attachurl'];
        $_W['attachurl_remote'] = $att['attachurl_remote'];

        \config::set('app.global.uid', \Auth::guard('admin')->user()->uid);
        \config::set('app.global.username', \Auth::guard('admin')->user()->username);
        \config::set('app.global.attachurl', $att['attachurl']);
        \config::set('app.global.attachurl_remote', $att['attachurl_remote']);

        \YunShop::app()->uid        = \Auth::guard('admin')->user()->uid;
        \YunShop::app()->username   = \Auth::guard('admin')->user()->username;
        \YunShop::app()->attachurl = $att['attachurl'];
        \YunShop::app()->attachurl_remote = $att['attachurl_remote'];
    }

    private function getRemoteServicerInfo()
    {
        $remoteServicer = [
            '2' => 'alioss',
            '4' => 'cos'
        ];

        $systemSetting = new SystemSetting();

        if ($remote = $systemSetting->getKeyList('remote', 'system_remote', true)) {
            $setting[$remote['key']] = unserialize($remote['value']);
        }

        if ($setting['remote']['type'] != 0) {
            $server = $setting['remote'][$remoteServicer[$setting['remote']['type']]];
            $url = isset($server['url']) ? $server['url'] : '';

            $data = [
                'attachurl' => $url,
                'attachurl_remote' => $url
            ];
        } else {
            $data = [
                'attachurl' => request()->getSchemeAndHttpHost() . '/static/upload/',
                'attachurl_remote' => ''
            ];
        }

        return $data;
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

        if (app('plugins')->isEnabled('mryt')) {
            include base_path() . '/plugins/mryt/storeMenu.php';
        }
    }
}