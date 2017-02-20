<?php
namespace plugins\modules\member\model;
use app\modules\member\common;
class Member extends common\model\Member
{
    private $db_member;
    private $_identity;
    public function __construct($db_member)
    {
        $this->db_member = $db_member;
        $this->_setIdentityDiscount();
    }
    //注册所有插件中的 用户等级折扣
    private function _setIdentityDiscount(){
        foreach ($this->db_member['identity'] as $identity){
            if(class_exists($identity.'identity')){
                $_identity_discount[] = new ($identity.'identity');
            }
        }
    }
    public function getLevel(){
        return m("member")->getLevel($this->db_member['openid']);
    }
    //获取用户等级折扣
    public function getDiscount($goods){
        $result = 1;
        foreach ($this->_identity as $_identity){
            $result *= $_identity->getDiscount($goods,$this);
        }
        return $result;
    }
    //获取用户等级立减
    public function getMoneyOff(){
        $result = 1;
        foreach ($this->_identity as $_identity){
            $result -= $_identity->getMoneyOff();
        }
        return $result;
    }
}