<?php

namespace app\Http\Middleware;

use app\common\services\Check;
use Closure;

/**
 * 后台检测
 * Class CheckMiddleware
 * @package app\Http\Middleware
 */
class CheckMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        strpos(request()->getBaseUrl(), '/web/index.php') === 0 && Check::setKey();
        return $next($request);

    }
}
