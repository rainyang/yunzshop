<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午4:18
 */

namespace app\common\models;


use app\backend\models\BackendModel;

class MemberShopInfo extends BackendModel
{
    protected $table = 'yz_member';

    public $timestamps = false;

    public static function getMemberShopInfo($memberId)
    {
        return static::where('member_id', $memberId)
            ->uniacid()
            ->first(1)
            ->toArray();
    }
}
