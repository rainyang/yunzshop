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
    private $authRole = ['operator'];

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
        $this->saveUniacid();

        $this->account = AppUser::getAccount(\Auth::guard('admin')->user()->uid, $this->uniacid);

        $this->accessPermissions();

        \config::set('app.global', array_merge($this->setConfigInfo(), $this->setRole()));

        return $next($request);
    }

    /**
     * 获取全局参数
     *
     * @return array
     */
    private function setConfigInfo()
    {
        return array_merge(\config::get('app.global'), ['uniacid' => $this->uniacid]);
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

        return $this->role;
    }

    /**
     * 保存公众号
     *
     */
    private function saveUniacid()
    {
        if (!empty(request('uniacid')) && request('uniacid') > 0) {
            $this->uniacid = request('uniacid');
            setcookie('uniacid', request('uniacid'));
        }

        if (in_array($this->account->role, $this->authRole)) {
            $this->uniacid = $this->account->uniacid;
            setcookie('uniacid', $this->account->uniacid);
        }

        if (empty($this->uniacid) && isset($_COOKIE['uniacid'])) {
            $this->uniacid = $_COOKIE['uniacid'];
        }

    }

    /**
     * 验证访问权限
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function accessPermissions()
    {
        if (\Auth::guard('admin')->user()->uid !== 1) {
            if ($this->account->uniacid != $this->uniacid) {
                \Auth::guard('admin')->logout();
                request()->session()->flush();
                request()->session()->regenerate();

                Cookie::queue(Cookie::forget('uniacid'));

                return $this->errorJson('请重新登录', ['login_status' => 1, 'login_url' => '/#/login']);
            }
        }
    }
}