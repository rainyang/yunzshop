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
        $global = \config::get('app.global');
        //TODO 查询用户组表 确定用户role(manager,operator)
        $role = ['role' => 'operator', 'isfounder' => false];
        $set    = $this->setConfigInfo();

        \config::set('app.global', array_merge($request->input(), $role, $global, $set));
        \config::set('app.sys_global', array_merge($request->input(), \Cookie::get()));

        return $next($request);
    }

    private function setConfigInfo()
    {
        return [];
    }
}