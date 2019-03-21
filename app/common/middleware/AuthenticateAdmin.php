<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 下午5:08
 */

namespace app\common\middleware;

use app\common\traits\JsonTrait;
use app\platform\modules\application\models\AppUser;
use Closure;
use Illuminate\Support\Facades\Cookie;

class AuthenticateAdmin
{
    use JsonTrait;

    protected $except = [
        'admin/index',
    ];

    private $account = null;
    private $uniacid = 0;
    private $role    = ['role' => '', 'isfounder' => false];

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
        if (\Auth::guard('admin')->user()->uid == 1) {
            $this->role = ['role' => 'founder', 'isfounder' => true];
        } else {
            $cfg = \config::get('app.global');

            if (!empty($cfg['uniacid'])) {
                $this->uniacid = $cfg['uniacid'];
                $this->account = AppUser::getAccount(\Auth::guard('admin')->user()->uid, $cfg['uniacid']);

                if (!is_null($this->account)) {
                   $this->setRole();
                } else {
                    $this->relogin();
                }
            }
        }

        \config::set('app.global', array_merge($this->setConfigInfo(), $this->role));

        return $next($request);
    }

    /**
     * 获取全局参数
     *
     * @return array
     */
    private function setConfigInfo()
    {
        return \config::get('app.global');
    }

    /**
     * 获取用户身份
     *
     * @return array
     */
    private function setRole()
    {
        if (\Auth::guard('admin')->user()->uid === 1) {
            $this->role = ['role' => 'founder', 'isfounder' => true];
        } else {
            $this->role    = ['role' => $this->account->role, 'isfounder' => false];
        }
    }

    /**
     * 验证访问权限
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function relogin()
    {
        \Auth::guard('admin')->logout();
        request()->session()->flush();
        request()->session()->regenerate();

        Cookie::queue(Cookie::forget('uniacid'));

        return $this->errorJson('请重新登录', ['login_status' => 1, 'login_url' => '/#/login']);

    }
}