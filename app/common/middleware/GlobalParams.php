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
        $base_config    = $this->setConfigInfo();

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
        if (request('uniacid')) {
            return \config::get('app.global');
        }

        return array_merge(\config::get('app.global'), $_COOKIE);
    }
}