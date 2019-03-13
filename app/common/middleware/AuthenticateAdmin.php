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
        if (Auth::guard('admin')->user()->id === 1) {
            $set  = \config::get('app.global');
            $role = ['role' => 'founder', 'isfounder' => true];

            \config::set('app.global', array_merge($set, $role));
        } else {
            //TODO 验证公众号和用户是否匹配
        }

        return $next($request);
    }
}