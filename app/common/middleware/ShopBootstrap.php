<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/3/12
 * Time: 下午5:42
 */

namespace app\common\middleware;


class ShopBootstrap
{
    public function handle($request, \Closure $next, $guard = null)
    {
        if (\Auth::guard('admin')->user()->id !== 1) {
            //TODO 查询用户组表 确定用户所属uniacid
            // TODO 如果是操作员直接跳转到商城
            $uniacid = 5;
            $url = 'shop?route=index.index&uniacid=' . $uniacid ;
            return redirect()->guest($url);
        }

        //TODO uniacid 是否需要存储在cookie中

        return $next($request);
    }
}