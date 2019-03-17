<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/3/12
 * Time: 下午5:42
 */

namespace app\common\middleware;


use app\common\helpers\Url;

class ShopBootstrap
{
    private $authRole = ['operator'];

    public function handle($request, \Closure $next, $guard = null)
    {
        if (\Auth::guard('admin')->user()->uid !== 1) {
            $base_config = \config::get('app.global');

            if (in_array($base_config['role'], $this->authRole)) {
                return redirect()->guest(Url::absoluteWeb('index.index'));
            }
        }

        return $next($request);
    }
}