<?php

namespace app\Http\Middleware;

use Closure;

/**
 * 微擎后台跳转
 * Class We7
 * @package app\Http\Middleware
 */
class We7
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!\YunShop::app()->uniacid) {
            return redirect('?c=account&a=display');
        }
        return $next($request);
    }
}
