<?php
namespace app\modules\goods\model\frontend;
class Goods
{
    private $db_goods;
    //private $_identity;
    public function __construct($db_goods)
    {
        echo 'goods_model';
        $this->db_goods = $db_goods;
        //$this->_setIdentityDiscount();
    }
    //注册所有插件中的 用户等级折扣
    /*private function _setIdentityDiscount(){
        foreach ($this->db_member['identity'] as $identity){
            if(class_exists($identity.'identity')){
                $_identity_discount[] = new ($identity.'identity');
            }
        }
    }*/
    //获取用户等级折扣
    public function getDiscount(){
        return json_decode($this->db_goods["discounts"], true);
    }
}