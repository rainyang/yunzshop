<?php

namespace app\frontend\modules\orderGoods\price;

use app\frontend\modules\orderGoods\price\option\NormalOrderGoodsOptionPrice;
use app\frontend\modules\orderGoods\price\option\NormalOrderGoodsPrice;
use app\frontend\modules\orderGoods\models\PreGeneratedOrderGoods;
use Illuminate\Support\Collection;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/19
 * Time: 下午7:23
 */
class OrderGoodsPriceCalculator
{
    /**
     * @var PreGeneratedOrderGoods
     */
    protected $orderGoods;
    /**
     * @var NormalOrderGoodsPrice
     */
    protected $optionInstance;
    /**
     * @var Collection
     */
    protected $decorators;

    function __construct(PreGeneratedOrderGoods $orderGoods)
    {
        $this->orderGoods = $orderGoods;
        $this->optionInstance = $this->setTypeInstance();
        $this->decorators = collect();

        $this->decorator = $this->getDecorator();
    }

    /**
     * 添加装饰器
     * @param $callback
     */
    public function pushDecorator($callback){
        $this->decorators->push($callback);

        $this->decorator = $this->getDecorator();
    }

    /**
     * 获取装饰器
     * @return mixed
     */
    protected function getDecorator()
    {
        $decorator = $this->decorators->reduce(function ($result, $decorator) {
            //$decorator 是一个匿名函数
            return call_user_func($decorator, $result);
        },$this->optionInstance);
        return $decorator;
    }

    /**
     * 获取订单商品模型
     * @return PreGeneratedOrderGoods
     */
    public function getOrderGoods()
    {
        return $this->orderGoods;
    }

    /**
     * 设置类型实例
     * @return NormalOrderGoodsOptionPrice|NormalOrderGoodsPrice
     */
    private function setTypeInstance()
    {
        if ($this->orderGoods->isOption()) {
            $result = new NormalOrderGoodsOptionPrice($this);

        } else {
            $result = new NormalOrderGoodsPrice($this);

        }
        return $result;
    }

    /**
     * 成交价
     * @return mixed
     */
    public function getPrice()
    {
        return $this->decorator->getPrice();
    }

    /**
     * 商品销售价
     * @return mixed
     */
    public function getGoodsPrice()
    {
        return $this->decorator->getGoodsPrice();

    }

    /**
     * 最终价格
     * @return mixed
     */
    public function getFinalPrice()
    {
        return $this->decorator->getFinalPrice();

    }

    /**
     * 折扣价
     * @return mixed
     */
    public function getDiscountPrice()
    {
        return $this->decorator->getDiscountPrice();

    }

    /**
     * 优惠券优惠价
     * @return mixed
     */
    public function getCouponPrice()
    {
        return $this->decorator->getCouponPrice();

    }

    /**
     * 成本价
     * @return mixed
     */
    public function getGoodsCostPrice()
    {
        return $this->decorator->getGoodsCostPrice();
    }

    public function getGoodsMarketPrice()
    {
        return $this->decorator->getGoodsMarketPrice();
    }

}