<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/1
 * Time: 下午4:39
 */

namespace app\frontend\modules\member\controllers;

use app\frontend\modules\member\models\Member;
use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberModel;

class MemberController extends BaseController
{
    public function index()
    {
        dd(Member::getNickNnme());
    }

    public function getUserInfo()
    {
        MemberModel::getUserInfos();
    }
}