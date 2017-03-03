<?php
namespace mobile\order\demo\model;
class Order
{
    protected $order;
    protected $goods_list;
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
    public function addFields()
    {

    }

    public function getGoodsList()
    {
        return $this->goods_list;
    }

    public function getPrice()
    {
        $result = 0;
        foreach ($this->getGoodsList() as $goods) {
            $result += $goods->getPrice();
        }
        return $result;
    }
//方法:改变字段
//方法:添加统计字段
//方法:计算统计字段
//方法:添加商品
    public function addGoods($goods_model)
    {
        $this->goods_list[] = $goods_model;
    }
//方法:输出{
//    订单总价格计算
//     运费计算
//     计算统计字段
//}
//方法:分组输出{
//}
}