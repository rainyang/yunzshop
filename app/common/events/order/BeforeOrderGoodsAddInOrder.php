<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/9
 * Time: 下午1:53
 */

namespace app\common\events\order;
use app\common\events\Event;


class BeforeOrderGoodsAddInOrder extends Event
{
    private $_order_goods_model;

    public function __construct($order_goods_model)
    {

        $this->_order_goods_model = $order_goods_model;
        /*echo '事件中的';
        dd($this);*/
    }

    public function getOrderGoodsModel(){

        return $this->_order_goods_model;
    }
}