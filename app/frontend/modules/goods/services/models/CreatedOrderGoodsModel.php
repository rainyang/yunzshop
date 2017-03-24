<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/20
 * Time: 下午3:35
 */

namespace app\frontend\modules\goods\services\models;


use app\frontend\modules\discount\services\DiscountService;
use app\frontend\modules\dispatch\services\DispatchService;

class CreatedOrderGoodsModel extends OrderGoodsModel
{
    private $_OrderGoods;

    public function __construct($OrderGoods, $total = 1)
    {
        $this->_OrderGoods = $OrderGoods;
        $this->total = $this->_OrderGoods->total;
        parent::__construct();
    }
    public function getOrderGoods(){
        return $this->_OrderGoods;
    }
    protected function setGoodsDiscount()
    {
        $this->_GoodsDiscount = DiscountService::getCreatedOrderGoodsDiscountModel($this->getOrderGoods());
    }

    protected function setGoodsDispatch()
    {
        $this->_GoodsDispatch = DispatchService::getCreatedOrderGoodsDispatchModel($this->getOrderGoods());
    }
    protected function getTotal(){
        return $this->_OrderGoods->total;
    }
    public function addChangePriceInfo($price)
    {
        $change_price = $price - $this->_OrderGoods->price;

        $detail = [
            'name' => '订单商品改价',
            'value' => "{$this->_OrderGoods->price}->{$price}",
            'price' => (string)$change_price,
            'plugin' => '0',
        ];
        $this->_GoodsDiscount->addDiscountDetail($detail);
    }

    public function getGoodsPrice()
    {

        return $this->total * $this->_OrderGoods->goods_price;

    }

    public function update()
    {
        $data = array(
            'goods_price' => $this->getGoodsPrice(),
            'price' => $this->getPrice(),
            'total' => $this->getTotal(),
            'discount_details' => $this->_GoodsDiscount->getDiscountDetails(),
            'dispatch_details' => $this->_GoodsDispatch->getDispatchDetails(),
        );
        dump('订单商品改价信息:');
        dump($data);
        return;
        OrderGoods::save($data);
    }
}