<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 下午1:49
 */

namespace app\frontend\modules\member\service;


use app\frontend\modules\member\model\factory\MemberModelFactory;

class MemberService
{
    private static $_current_member;
    private static $_instance;

    public static function getCurrentMemberModel(){
        if(isset(self::$_instance)){
            self::setCurrentMember('67');
            return self::$_current_member;
        }
        self::$_instance = new MemberService();
        return self::$_instance->getCurrentMemberModel();
    }

    public static function setCurrentMember($member_id)
    {
        self::$_current_member = (new MemberModelFactory())->getMemberModel($member_id);
    }
    public function getMember(){

    }
}