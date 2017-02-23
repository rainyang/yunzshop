<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:44
 */

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\McMemberService;
use app\frontend\modules\member\models\McMappingFansModel;

class OfficeAccountMemberService extends McMemberService
{
    public function __construct()
    {
    }

    public function getUserInfo()
    {
        return  McMappingFansModel::first();
    }
}