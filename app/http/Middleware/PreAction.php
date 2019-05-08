<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/15
 * Time: 2:20 PM
 */

namespace app\http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * 所有controller
 * Class PreAction
 * @package app\http\Middleware
 */
class PreAction
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Route::current()->controller->preAction();
        return $next($request);

    }
}