<?php

namespace app\frontend\modules\orderGoods\price\option;

use app\frontend\models\Goods;
use app\frontend\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\orderGoods\price\OrderGoodsPriceCalculator;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/19
 * Time: 下午6:05
 */
abstract class OrderGoodsPrice
{
    protected $goodsPrice;
    /**
     * @var \app\frontend\modules\orderGoods\models\PreOrderGoods
     */
    public $orderGoods;

    public function __construct(PreOrderGoods $preOrderGoods)
    {
        $this->orderGoods = $preOrderGoods;
    }

    /**
     * 计算成交价格
     * @return float
     */
    abstract public function getPrice();

    /**
     * 计算商品销售价格
     * @return float
     */
    abstract public function getGoodsPrice();

    /**
     * 计算商品市场价格
     * @return float
     */
    abstract public function getGoodsMarketPrice();

    /**
     * 计算商品市场价格
     * @return float
     */
    abstract public function getGoodsCostPrice();
    /**
     * 计算商品优惠价格
     * @return number
     */
    abstract public function getDiscountAmount();
}