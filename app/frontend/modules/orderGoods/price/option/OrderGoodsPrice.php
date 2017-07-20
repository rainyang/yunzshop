<?php

namespace app\frontend\modules\orderGoods\price\option;

use app\frontend\models\Goods;
use app\frontend\modules\orderGoods\price\OrderGoodsPriceCalculator;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/19
 * Time: 下午6:05
 */
abstract class OrderGoodsPrice
{
    protected $orderGoodsPriceCalculator;
    /**
     * @var \app\frontend\modules\orderGoods\models\PreGeneratedOrderGoods
     */
    public $orderGoods;

    public function __construct(OrderGoodsPriceCalculator $orderGoodsPriceCalculator)
    {
        $this->orderGoodsPriceCalculator = $orderGoodsPriceCalculator;
        $this->orderGoods = $orderGoodsPriceCalculator->getOrderGoods();
    }

    /**
     * @var Goods
     */
    protected $goods;

    /**
     * 计算成交价格
     * @return int
     */
    abstract public function getPrice();

    /**
     * 计算商品销售价格
     * @return int
     */
    abstract public function getGoodsPrice();

    /**
     * 计算商品优惠价格
     * @return number
     */
    abstract public function getDiscountPrice();
}