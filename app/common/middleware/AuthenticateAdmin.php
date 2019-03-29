<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 下午5:08
 */

namespace app\common\middleware;

use app\common\services\Utils;
use app\common\traits\JsonTrait;
use app\platform\modules\application\models\AppUser;
use app\platform\modules\application\models\UniacidApp;
use Closure;

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
        $cfg = \config::get('app.global');

        if (\Route::getCurrentRoute()->getUri() == 'admin/shop' && isset($cfg['uniacid'])) {
            $msg = '';
            $sys_app = UniacidApp::getApplicationByid($cfg['uniacid']);

            if (is_null($sys_app)) {
                $msg = '非法请求';

                if (strpos($_SERVER['REQUEST_URI'], '/admin/shop') !== false) {
                    return $this->redirectToHome();
                }
            }

            if (!is_null($sys_app->deleted_at)) {
                $msg = '平台已停用';

                if (strpos($_SERVER['REQUEST_URI'], '/admin/shop') !== false) {
                    return $this->redirectToHome();
                }
            }

            if ($msg) {
                Utils::removeUniacid();

                return $this->errorJson($msg, ['status'=> -1]);
            }
        }

        if (\Auth::guard('admin')->user()->uid == 1) {
            $this->role = ['role' => 'founder', 'isfounder' => true];
        } else {
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

        Utils::removeUniacid();

        return $this->errorJson('请重新登录', ['login_status' => 1, 'login_url' => '/#/login']);

    }

    /**
     * 链接跳转
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectToHome()
    {
        return redirect()->guest();
    }
}