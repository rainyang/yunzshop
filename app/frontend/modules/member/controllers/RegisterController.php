<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;
use app\frontend\modules\member\services\OfficeAccountMemberService;

class RegisterController extends BaseController
{
    private $error = array();

    public function index()
    {
        $oa_wetcha = new OfficeAccountMemberService();

        $info = $oa_wetcha->getUserInfo();
    echo '<pre>';print_r($info);exit;
    }

    private function validate()
    {}
}