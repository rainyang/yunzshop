<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 下午5:08
 */

namespace app\common\middleware;

use Auth;
use Closure;

class AuthenticateAdmin
{

    protected $except = [
        'admin/index',
    ];

    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $set        = \config::get('app.global');

        if (Auth::guard('admin')->user()->id === 1) {
            $app_global = ['role' => 'founder', 'isfounder' => true];
            \config::set('app.global', array_merge($set, $app_global));
        } else {
            // TODO 验证用户组 manager,operator
            $app_global = ['role' => 'operator', 'isfounder' => false];
            \config::set('app.global', array_merge($set, $app_global));
            // TODO 如果是操作台直接跳转到商城
            $url = 'shop?route=index.index';
            return redirect()->guest($url);
        }

        return $next($request);
    }
}