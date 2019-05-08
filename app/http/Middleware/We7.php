<?php

namespace app\Http\Middleware;

use Closure;

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
