<?php
namespace app\frontend\modules\goods\model;
class GoodsModel
{
    private $_initial_data;
    private $_data;

    private $total;

    public function getInitialData(){
        return $this->_initial_data;
    }
    //private $_identity;
    public function __construct($db_goods, $total)
    {
        $this->_initial_data = $db_goods;
        
        $this->_data = $db_goods;
        //todo goods total
        $this->_data['total'] = $this->total;
        //todo goods discountprice
        $this->_data['discountprice'] = (new Price($this))->getDiscountPrice();
        $this->_data['dispatch_price'] = (new Dispatch($this))->_getDispatchPrice();
        $this->_decorateData();

        //$this->_setIdentityDiscount();
    }

    private function _decorateData(){
        $data = (new Decorator($this))->getData();
        $this->_data = array_merge($data,$this->_data);
    }

    private function setTotal($total)
    {
        if (empty($total) || intval($total) == "-1") {
            $this->total = 1;
        } else {
            $this->total = $total;
        }
    }

    //
    public function getDiscountPrice()
    {

        //获取优惠
        //todo goods discountway price

        if ($this->_data['discountway'] == 1) {
            $result = $this->_data['price'] * $this->getDiscount($this);
        } else {
            $result = $this->_data['price'] - $this->getMoneyOff($this);
        }
        return $result;
    }
    //注册所有插件中的 用户等级折扣
    /*private function _setIdentityDiscount(){
        foreach ($this->db_member['identity'] as $identity){
            if(class_exists($identity.'identity')){
                $_identity_discount[] = new ($identity.'identity');
            }
        }
    }*/


}