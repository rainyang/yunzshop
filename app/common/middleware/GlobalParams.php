<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/27
 * Time: 下午4:56
 */

namespace app\common\middleware;


use app\common\models\AccountWechats;

class GlobalParams
{
    public function handle($request, \Closure $next, $guard = null)
    {
        $base_config = $this->setConfigInfo();

        \config::set('app.global', $base_config);
        \config::set('app.sys_global', array_merge($request->input(), $_COOKIE));

        return $next($request);
    }

    /**
     * 获取全局参数
     *
     * @return array
     */
    private function setConfigInfo()
    {
        $cfg     = \config::get('app.global');
        $account = AccountWechats::getAccountByUniacid($cfg['uniacid']);
        $cfg['account'] = $account ? $account->toArray() : '';

        if (request('uniacid')) {

            return $cfg;
        }

        return array_merge($cfg, $_COOKIE);
    }
}