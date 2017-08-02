<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/2
 * Time: 下午4:29
 */

namespace app\common\models;


class MemberMiniAppModel extends BaseModel
{
    public $table = 'yz_member_mini_app';

    public static function getFansById($memberId)
    {
        return self::uniacid()
            ->where('member_id', $memberId)
            ->first();
    }
}