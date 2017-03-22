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

    public function __construct($Order, $OrderGoodsModels)
    {
        $this->_Order = $Order;
        parent::__construct($OrderGoodsModels);
    }

    protected function setOrderGoodsModels(array $OrderGoodsModels)
    {
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

    public function changePrice($price)
    {
        $change_price = $price - $this->_Order->price;

        $detail = [
            'name' => '订单改价',
            'value' => "{$this->_Order->price}->{$price}",
            'price' => (string)$change_price,
            'plugin' => '0',
        ];
        $this->_OrderDiscount->addDiscountDetail($detail);
    }

    public function changeDispatchPrice($dispatch_price)
    {
        //dd($this->_Order);
        $change_dispatch_price = $dispatch_price - $this->_Order->dispatch_price;
        $dispatch_price = [
            'name' => '运费改价',
            'value' => "{$this->_Order->dispatch_price}->{$dispatch_price}",
            'price' => (string)$change_dispatch_price,
            'plugin' => '0',
        ];
        $this->_OrderDispatch->addDispatchDetail($dispatch_price);
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
            'diapatch_price' => $this->getDispatchPrice(),
            'price' => $this->getPrice(),
            'goods_price' => $this->getGoodsPrice(),
        ];
        echo '订单改价信息:';
        dd($data);
        $this->_updateOrderGoods();
    }
    private function _updateOrderGoods(){
        foreach ($this->_OrderGoodsModels as $_orderGoodsModel){
            $_orderGoodsModel->update();
        }
    }
}