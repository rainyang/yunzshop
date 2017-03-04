<?php
namespace mobile\order\demo;
class demo
{
    //获取未生成的订单model
    function index(){
        $db_goods = DB_Goods::find(1);
        $goods = new ModelGoods($db_goods);

        $order = new ModelOrder();

        $order->addGoods($goods);
        $order_group = $order->group('suppliers')->get();

        return $order_group;
    }
    //获取已生成的订单model
    function getOne(){

    }
}
