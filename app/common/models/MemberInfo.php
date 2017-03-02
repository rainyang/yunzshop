<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午4:18
 */

namespace app\common\models;


use Illuminate\Database\Eloquent\Model;

class MemberInfo extends Model
{
    protected $table = 'yz_member';

    public static function getMemberShopInfo($memberId, $uniacid)
    {
        return static::where('member_id', $memberId)->where('uniacid', $uniacid)->first(1)->toArray();
    }

}