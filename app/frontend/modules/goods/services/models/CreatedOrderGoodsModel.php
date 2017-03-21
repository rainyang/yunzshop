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
    private $OrderGoods;

    public function __construct($OrderGoods, $total = 1)
    {
        $this->OrderGoods = $OrderGoods;
        $this->total = $this->OrderGoods->total;
        parent::__construct();
    }
    public function getOrderGoods(){
        return $this->OrderGoods;
    }
    protected function setGoodsDiscount()
    {
        $this->GoodsDiscount = DiscountService::getCreatedOrderGoodsDiscountModel($this->getOrderGoods());
    }

    protected function setGoodsDispatch()
    {
        $this->GoodsDispatch = DispatchService::getCreatedOrderGoodsDispatchModel($this->getOrderGoods());
    }

    public function getGoodsPrice()
    {
        dd($this->total);
        dd($this->OrderGoods->goods_price);
exit;
        return $this->total * $this->OrderGoods->goods_price;

    }

    public function update()
    {
        $data = array(
            'goods_price' => $this->getGoodsPrice(),
            'price' => $this->getPrice(),
            'total' => $this->getTotal(),
            'discount_details' => $this->discount_details,
            'dispatch_details' => $this->dispatch_details,
        );
        dd($data);exit;
        OrderGoods::save($data);
    }
}