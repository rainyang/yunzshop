<?php
namespace mobile\order\demo\model;
class Order
{
    protected $order;
//属性:统计字段[]
    protected $statistics_fields;
//
//构造方法(订单db_model){
    public function __construct($db_order)
    {

    }
//}
//
//方法:添加字段
    public function addFields(){
        
    }
//方法:改变字段
    public function editFields(){

    }
//方法:添加统计字段
    public function addStatisticsFields(){

    }
//方法:计算统计字段
    public function getStatisticsFields(){

    }
//方法:添加商品
    public function addGoods(){

    }
    private function calculate(){

    }
//方法:输出{
//    订单总价格计算
//     运费计算
//     计算统计字段
//}
    public function get(){
        return $this->order;
    }
//方法:分组输出{
//}
}