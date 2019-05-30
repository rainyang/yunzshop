<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/3/2
 * Time: 上午7:37
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;

use app\common\services\Session;
use Illuminate\Support\Facades\Cookie;

class LogoutController extends BaseController
{
    public function index()
    {
        setcookie('Yz-Token', '', time() - 3600);
        setcookie('Yz-Uid', '',time() - 3600);

        session_destroy();
        return $this->successJson('退出成功');
    }
}