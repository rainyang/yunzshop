<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/20
 * Time: 下午3:36
 */

namespace app\frontend\modules\goods\services\models;


use app\frontend\models\OrderGoods;

abstract class OrderGoodsModel extends OrderGoods
{
    /**
     * 计算成交价格
     * @return int
     */
    public function getPrice()
    {
        //成交价格=商品销售价-优惠价格

        $result = max($this->getFinalPrice() - $this->getDiscountPrice(),0);
        return $result;
    }

    /**
     * 计算商品销售价格
     * @return int
     */
    abstract function getGoodsPrice();
    /**
     * 计算商品优惠价格
     * @return number
     */
    abstract protected function getDiscountPrice();
    abstract public function getGoodsId();
}