<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/2
 * Time: 上午7:37
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;

use app\common\services\Session;
use Illuminate\Support\Facades\Cookie;

class LogoutController extends ApiController
{
    public function index()
    {
        $cookieid = "__cookie_yun_shop_userid_" . \YunShop::app()->uniacid;

        Cookie::unqueue($cookieid);
        Cookie::unqueue('member_mobile');

        Session::clear('member_id');

        $this->successJson();
    }
}