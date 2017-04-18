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
    private $order;
    protected $orderGoodsModels = [];

    public function __construct($order, $orderGoodsModels)
    {
        $this->order = $order;
        parent::__construct($orderGoodsModels);
    }

    public function setOrderGoodsModels(array $orderGoodsModels)
    {
        $this->orderGoodsModels = $orderGoodsModels;

    }

    protected function getDiscountPrice()
    {
        return $this->discount_price;
    }

    protected function getDispatchPrice()
    {
        return $this->dispatch_price;

    }

    protected function getChangePrice()
    {
        return $this->hasManyOrderGoods->sum(function ($orderGoods) {
            return $orderGoods->orderGoodschangePriceLog->change_price;
        });
    }

    protected function getChangeVipPrice()
    {
        //todo
        return 0;
    }

    protected function getVipPrice()
    {
        return parent::getVipPrice() - $this->getChangeVipPrice();

    }

    protected function getPrice()
    {
        return parent::getPrice() - $this->getChangePrice();
    }

    public function changePrice()
    {
        $data = [
            'discount_price' => $this->getDiscountPrice(),
            'dispatch_price' => $this->getDispatchPrice(),
            'deduction_price' => $this->getDeductionPrice(),
            'price' => $this->getPrice(),
            'goods_price' => $this->getVipPrice(),
        ];
        dump('订单改价信息:');
        dump($data);
        $this->updateOrderGoods();
    }

    private function updateOrderGoods()
    {
        foreach ($this->orderGoodsModels as $orderGoodsModel) {
            //$orderGoodsModel->update();
        }
    }
}