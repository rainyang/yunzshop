<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/28
 * Time: 上午5:16
 */

namespace app\frontend\modules\member\services;
use app\common\models\Member;


class MemberService
{
    private static $_current_member;
    public static function getCurrentMemberModel(){
        if(isset(self::$_current_member)){
            return self::$_current_member;
        }
        //todo 根据情况改写
        self::setCurrentMemberModel(1);
        return self::$_current_member;
    }

    public static function setCurrentMemberModel($member_id)
    {
        $member = Member::first();
        if(!isset($member)){
            return '用户id不存在';exit;
        }
        self::$_current_member = $member;
    }
}