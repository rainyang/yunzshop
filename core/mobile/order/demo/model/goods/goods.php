<?php
namespace mobile\order\demo\model;
class Goods
{
    protected $goods;
//
//构造方法(订单db_model){
    public function __construct($db_goods)
    {
        $this->goods=$db_goods;
        //$this->setPriceCode($db_goods['discountway']);
    }

//方法:添加字段
    public function getPrice(){
        if($this->goods['discountway']){

        }else{

        }

    }

}