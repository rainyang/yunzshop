<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 15:55
 */

namespace app\backend\modules\charts\modules\phone\controllers;


use app\backend\modules\charts\modules\phone\models\Member;
use app\common\components\BaseController;

class PhoneAttributionController extends BaseController
{

    public function index()
    {
        $member = $this->getPhone();
    }

    public function getPhone()
    {
        $member_model = Member::getMember()->get()->toArray();
        return $member_model;
    }
}