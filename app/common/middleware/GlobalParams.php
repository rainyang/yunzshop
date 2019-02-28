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
        \config::set('app.sys_global', array_merge($request->input(), \Cookie::get()));
        \config::set('app.global', array_merge($request->input(), \Cookie::get()));

        return $next($request);
    }
}