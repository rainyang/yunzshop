<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 上午10:44
 */

/**
 * 小程序登录表
 */

namespace app\frontend\modules\member\models;

use app\backend\models\BackendModel;

class MemberMiniAppModel extends BackendModel
{
    public $table = 'yz_member_mini_app';

    public static function insertData($data)
    {
        self::insert($data);
    }

    public static function getUserInfo($openid)
    {
        return self::uniacid()
            ->where('openid', $openid)
            ->first();
    }

    public static function updateUserInfo($openid, $data)
    {
        return self::uniacid()
            ->where('openid', $openid)
            ->update($data);
    }
}