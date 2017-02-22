<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;
use app\frontend\modules\member\services\OfficeAccountMember;

class RegisterController extends BaseController
{
    private $error = array();

    public function index()
    {
        $oa_wetcha = new OfficeAccountMember();

        $info = $oa_wetcha->getUserInfo();
    echo '<pre>';print_r(\YunShop::request());exit;
        echo 1;exit;
    }

    private function validate()
    {}
}