<?php

namespace app\Http\Middleware;

use app\common\services\Check;
use Closure;

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
