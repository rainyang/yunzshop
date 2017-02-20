<?php
namespace app\modules\member\common\model;
class Member
{
    protected $db_member;
    public function __construct($db_member)
    {

    }
    public function getMemberId(){

    }
    /*protected $plugin_member_models;//不合理 但可以在重构过程中 过渡使用

    private $_identity;
    public function __construct($db_member)
    {
        $this->db_member = $db_member;
        $this->_setIdentityDiscount();
        $this->_setPluginMemberModel();
    }
    private function _getPluginMemberModelsFromSomewhere(){
        return [];
    }
    private function _setPluginMemberModel(){
        foreach ($this->_getPluginMemberModelsFromSomewhere() as $plugin_member_model){
            $this->plugin_member_model[] = $plugin_member_model;
        }
    }
    //注册所有插件中的 用户等级折扣
    private function _setIdentityDiscount(){
        foreach ($this->db_member['identity'] as $identity){
            if(class_exists($identity.'identity')){
                $_identity_discount[] = new ($identity.'identity')();
            }
        }
    }
    public function getOpenId(){
        return $this->db_member['openid'];
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
    }*/
}