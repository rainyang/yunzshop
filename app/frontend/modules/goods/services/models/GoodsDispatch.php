<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\goods\services\models;


use app\common\events\OrderGoodsDispatchWasCalculated;
use Illuminate\Support\Facades\Event;

class GoodsDispatch
{
    private $_order_goods_model;
    private $_dispatch_details = [];
    public function __construct(PreGeneratedOrderGoodsModel $order_goods_model)
    {
        $this->_order_goods_model = $order_goods_model;
        Event::fire(new OrderGoodsDispatchWasCalculated($this));
    }
    public function getOrderGoodsModel(){
        return $this->_order_goods_model;
    }
    //为订单商品提供 获取商品的运费信息
    public function getDispatchDetails(){
        return $this->_dispatch_details;

        /*$details[] = [
            'name'=>'运费模板2',
            'id'=>2,
            'value'=>'11',
            'price'=>'11',
            'plugin'=>'2',
        ];
        return $details;*/
    }
    //为监听者提供 添加运费信息
    public function addDispatchDetail($dispatch_detail){
        $this->_dispatch_details[] = $dispatch_detail;
    }

}