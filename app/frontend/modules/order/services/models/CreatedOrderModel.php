<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/21
 * Time: 上午9:31
 */

namespace app\frontend\modules\order\services\models;


use app\frontend\modules\discount\services\DiscountService;
use app\frontend\modules\dispatch\services\DispatchService;

class CreatedOrderModel extends OrderModel
{
    private $_Order;
    protected $_OrderGoodsModels = [];
    public function __construct($Order,$OrderGoodsModels)
    {
        $this->_Order = $Order;
        parent::__construct($OrderGoodsModels);
    }
    protected function setOrderGoodsModels($OrderGoodsModels){
        $this->_OrderGoodsModels = $OrderGoodsModels;

    }
    public function getOrder()
    {
        return $this->_Order;
    }

    protected function setDiscount()
    {
        $this->_OrderDiscount = DiscountService::getCreatedOrderDiscountModel($this->getOrder());
    }

    protected function setDispatch()
    {
        $this->_OrderDispatch = DispatchService::getCreatedOrderDispatchModel($this->getOrder());
    }

    public function update()
    {
        $data = [
            //配送类获取订单配送信息
            'dispatch_details' => $this->_OrderDispatch->getDispatchDetails(),
            //优惠类记录订单配送信息
            'discount_details' => $this->_OrderDiscount->getDiscountDetails(),
            'price' => $this->getPrice(),
            'goods_price' => $this->getGoodsPrice(),


        ];
        dd($data);
    }
}