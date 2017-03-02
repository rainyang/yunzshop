<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午2:28
 */

namespace app\backend\modules\member\services;




use app\backend\modules\member\models\Member;
use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\member\models\MemberLevel;

class MemberServices
{
    public static function getMemberList($uniacid)
    {
        $memberList = Member::getMemberlist($uniacid);
        $memberGroup = MemberGroup::getMemberGroupList($uniacid);
        $memberLevel = MemberLevel::getMemberLevelList($uniacid);

        //echo '<pre>'; print_r($getMemberGroup); exit;

        return static::attachedGroupToMember($memberList,$memberLevel, $memberGroup);
    }

    public static function attachedLevelAndGrouping($memberList, $memberLevel, $memberGroup)
    {
        foreach ($memberList as $key => $member) {
            unset($member['groupid'], $member['password'], $member['salt']);
            for ($i = '0'; $i <60; $i++)
            {
                unset($member[$i]);
            }
            /*foreach ($memberLevel)
            {

            }*/



            return $member;
        }
    }

    protected static function attachedGroupToMember($member = array(), $memberGroup)
    {
        foreach ($member as $user)
        {
            //$user['group_id'] =
            return $user;
        }
    }


}
