<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 上午11:53
 */

namespace app\common\middleware;


use app\common\exceptions\MemberNotLoginException;
use app\common\traits\JsonTrait;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    use JsonTrait;
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
                //return response('Unauthorized.', 401);
            } else {
                $login_path = [
                    'admin' => '/#/login',
                ];
                $url = empty($guard) ? '/login' : (isset($login_path[$guard]) ? $login_path[$guard] : '/login');

                return $this->successJson('请登录', ['login_status' => 1, 'login_url' => $url]);
            }
        }

        return $next($request);
    }
}