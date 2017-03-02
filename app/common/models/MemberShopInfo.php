<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午4:18
 */

namespace app\common\models;


use Illuminate\Database\Eloquent\Model;

class MemberShopInfo extends Model
{
    protected $table = 'yz_member';

    public static function getMemberShopInfo($memberId)
    {
        return static::where('member_id', $memberId)
            ->where('uniacid', \YunShop::app()->uniacid)
            ->first(1)
            ->toArray();
    }


}