<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午1:55
 */

namespace app\backend\modules\member\models;


class Member extends \app\common\models\Member
{
    public static function getMemberlist($uniacid)
    {
        $memberList = Member::where('uniacid', $uniacid)->get()->toArray();
        return $memberList;
    }
}