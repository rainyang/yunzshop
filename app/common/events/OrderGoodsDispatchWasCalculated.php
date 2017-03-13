<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/9
 * Time: 下午1:53
 */

namespace app\common\events;


use app\frontend\modules\goods\services\models\GoodsDispatch;

class OrderGoodsDispatchWasCalculated extends Event
{
    private $_order_goods_model;
    public $goods_dispatch_obj;


    public function __construct(GoodsDispatch $goods_dispatch_obj)
    {
        $this->goods_dispatch_obj = $goods_dispatch_obj;
        $this->_order_goods_model = $goods_dispatch_obj->getOrderGoodsModel();
    }
    public function getOrderGoodsModel(){
        return $this->_order_goods_model;
    }
}