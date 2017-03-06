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
    public static function getMemberlist()
    {
        $memberList = Member::where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
        return $memberList;
    }

    public function yzMember()
    {
        return $this->hasOne('app\backend\modules\member\models\MemberShopInfo','member_id','uid');
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public static function getGoodsByName($keyword)
    {
        return static::where('realname', 'like', $keyword.'%')
            ->orWhere('nick_name', 'like', $keyword.'%')
            ->orWhere('mobile', 'like', $keyword.'%')
            ->get();
    }
}