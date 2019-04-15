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
    private $remoteServicer = [
        '2' => 'alioss',
        '4' => 'cos'
    ];

    public function handle($request, \Closure $next, $guard = null)
    {
        $base_config = $this->setConfigInfo();

        \config::set('app.global', $base_config);

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
        $cfg = \config::get('app.global');

        $att = $this->getRemoteServicerInfo();

        $params = [
            'acid'             => $cfg['uniacid'],
            'openid'           => '',
            'uid'              => \Auth::guard('admin')->user()->uid,
            'siteroot'         => request()->getSchemeAndHttpHost() . '/',
            'siteurl'          => request()->getUri(),
            'attachurl'        => $att['attachurl'],
            'attachurl_local'  => request()->getSchemeAndHttpHost() . '/static/upload/',
            'attachurl_remote' => $att['attachurl_remote']
        ];

        return array_merge($cfg, $params);
    }

    private function getRemoteServicerInfo()
    {
        $systemSetting = new SystemSetting();

        if ($remote = $systemSetting->getKeyList('remote', 'system_remote', true)) {
            $setting[$remote['key']] = unserialize($remote['value']);
        }

        if ($setting['remote']['type'] != 0) {
            $server = $setting['remote'][$this->remoteServicer[$setting['remote']['type']]];
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
     * 为了兼容 供应商、门店、分公司、酒店 独立后台登录
     */
    public function checkClear()
    {
        if (app('plugins')->isEnabled('supplier')){
            include base_path().'/plugins/supplier/menu.php';
        }

        if (app('plugins')->isEnabled('store-cashier')) {
            include base_path().'/plugins/store-cashier/storeMenu.php';
        }

        if (app('plugins')->isEnabled('subsidiary')) {
            $subsidiary =  include base_path().'/plugins/subsidiary/bootstrap.php';
            app()->call($subsidiary);
        }

        if (app('plugins')->isEnabled('hotel')) {
            $hotel = include base_path().'/plugins/hotel/bootstrap.php';
            app()->call($hotel);
        }
    }
}