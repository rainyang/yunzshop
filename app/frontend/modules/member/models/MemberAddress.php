<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午8:34
 */

namespace app\frontend\modules\member\models;


class MemberAddress extends \app\common\models\MemberAddress
{
    public static function getMemberAddressByMemberId($memberId)
    {
        $uniacid = '8';
        return static::where('uniacid', $uniacid)
            ->where('uid', $memberId)
            ->get()
            ->toArray();
    }
}