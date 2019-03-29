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
}