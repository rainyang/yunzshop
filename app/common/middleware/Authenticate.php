<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 上午11:53
 */

namespace app\common\middleware;


use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, \Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                $login_path = [
                    'admin' => '/admin/login',
                ];
                $url = empty($guard) ? '/login' : (isset($login_path[$guard]) ? $login_path[$guard] : '/login');

                return redirect()->guest($url);
            }
        }

        \config::set('app.global', array_merge($request->input(), \Cookie::get()));

        return $next($request);
    }
}