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

    public $primaryKey = 'member_id';

    public static function getMemberShopInfo($memberId)
    {
        return self::select('*')->where('member_id', $memberId)
            ->uniacid()
            ->first(1);
    }
}
