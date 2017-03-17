<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\goods\services\models;

use app\common\events\order\OrderGoodsDispatchWasCalculated;

class GoodsDispatch
{
    private $_order_goods_model;
    private $_dispatch_details = [];
    public function __construct(PreGeneratedOrderGoodsModel $order_goods_model)
    {
        $this->_order_goods_model = $order_goods_model;
        event(new OrderGoodsDispatchWasCalculated($this));
    }
    public function getOrderGoodsModel(){
        return $this->_order_goods_model;
    }
    // 获取商品配送方式 todo 从商品中获取
    public function getDispatchType(){
        return 1;
    }
    //为订单商品提供 获取商品的运费信息
    public function getDispatchDetails(){
        return $this->_dispatch_details;
    }
    public function saveDispatchDetails($_order_goods_model){
        $_order_goods_model->dispatch_details = $this->getDispatchDetails();
        $_order_goods_model->save();
    }
    //为监听者提供 添加运费信息
    public function addDispatchDetail($dispatch_detail){
        $this->_dispatch_details[] = $dispatch_detail;
    }

}