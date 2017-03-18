<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/17
 * Time: 上午9:36
 */

namespace app\common\events\order;


use app\common\events\Event;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;

abstract class OrderGoodsEvent extends Event
{
    private $_order_goods_model;

    public function __construct(PreGeneratedOrderGoodsModel $order_goods_model)
    {
        $this->_order_goods_model = $order_goods_model;
    }
    public function getOrderGoodsModel(){
        return $this->_order_goods_model;
    }
}