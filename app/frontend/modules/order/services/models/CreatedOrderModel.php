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
    protected $orderGoodsModels = [];

    public function __construct($Order, $OrderGoodsModels)
    {
        $this->_Order = $Order;
        parent::__construct($OrderGoodsModels);
    }

    public function setOrderGoodsModels(array $orderGoodsModels)
    {
        $this->orderGoodsModels = $orderGoodsModels;

    }

    public function getOrder()
    {
        return $this->_Order;
    }

    protected function setDiscount()
    {
        $this->orderDiscount = DiscountService::getCreatedOrderDiscountModel($this->getOrder());
    }

    public function addChangePriceInfo($price)
    {
        $change_price = $price - $this->_Order->price;

        $detail = [
            'name' => '订单改价',
            'value' => "{$this->_Order->price}->{$price}",
            'price' => (string)$change_price,
            'plugin' => '0',
        ];
        $this->orderDiscount->addDiscountDetail($detail);
    }

    public function addChangeDispatchPriceInfo($dispatch_price)
    {
        //dd($this->_Order);
        $change_dispatch_price = $dispatch_price - $this->_Order->dispatch_price;
        $dispatch_price = [
            'name' => '运费改价',
            'value' => "{$this->_Order->dispatch_price}->{$dispatch_price}",
            'price' => (string)$change_dispatch_price,
            'plugin' => '0',
        ];
        $this->orderDispatch->addDispatchDetail($dispatch_price);
    }

    protected function setDispatch()
    {
        $this->orderDispatch = DispatchService::getCreatedOrderDispatchModel($this->getOrder());
    }

    public function update()
    {
        $data = [
            //配送类获取订单配送信息
            'dispatch_details' => $this->orderDispatch->getDispatchDetails(),
            //优惠类记录订单配送信息
            'discount_details' => $this->orderDiscount->getDiscountDetails(),
            'discount_price' => $this->getDiscountPrice(),
            'dispatch_price' => $this->getDispatchPrice(),
            'price' => $this->getPrice(),
            'goods_price' => $this->getVipPrice(),
        ];
        dump('订单改价信息:');
        dump($data);
        $this->_updateOrderGoods();
    }
    private function _updateOrderGoods(){
        foreach ($this->orderGoodsModels as $_orderGoodsModel){
            $_orderGoodsModel->update();
        }
    }
}