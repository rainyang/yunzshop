<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/2
 * Time: 上午7:37
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;

use Illuminate\Support\Facades\Cookie;

class LogoutController extends BaseController
{
    public function index()
    {
        $cookieid = "__cookie_sz_yi_userid_" . \YunShop::app()->uniacid;

        Cookie::unqueue($cookieid);
        Cookie::unqueue('member_mobile');

        session()->forget('member_id');

        $this->successJson();
    }
}