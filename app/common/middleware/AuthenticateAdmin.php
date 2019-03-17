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
        $this->account = AppUser::getAccount(\Auth::guard('admin')->user()->uid);

        $uniacid = $this->setUniacid();
        $this->accessPermissions();

        \config::set('app.global', array_merge(\config::get('app.global'), $this->setRole(), $uniacid));

        return $next($request);
    }


    private function setRole()
    {
        if (\Auth::guard('admin')->user()->uid === 1) {
            $this->role = ['role' => 'founder', 'isfounder' => true];
        } else {
            $this->role    = ['role' => $this->account->role, 'isfounder' => false];
        }

        return $this->role;
    }

    private function setUniacid()
    {
        if (!empty(request('uniacid')) && request('uniacid') > 0) {
            $this->uniacid = request('uniacid');
            Cookie::queue('uniacid', request('uniacid'));
        }

        if (in_array($this->account->role, $this->authRole)) {
            $this->uniacid = $this->account->uniacid;
            Cookie::queue('uniacid', $this->account->uniacid);
        }

        return ['uniacid' => $this->uniacid];
    }

    private function accessPermissions()
    {
        if (\Auth::guard('admin')->user()->uid !== 1) {
            if ($this->account->uniacid != $this->uniacid) {
                \Auth::guard('admin')->logout();
                request()->session()->flush();
                request()->session()->regenerate();

                Cookie::queue(Cookie::forget('uniacid'));

                return $this->errorJson('请登录', ['login_status' => 1, 'login_url' => '/#/login']);
            }
        }
    }
}