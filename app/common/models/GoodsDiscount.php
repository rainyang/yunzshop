<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/27
 * Time: 下午1:58
 */

namespace app\common\models;

class GoodsDiscount extends BaseModel
{
    public $table = 'yz_goods_discount';
    public $guarded = [];
    const MONEY_OFF = 1;//立减
    const DISCOUNT = 2;//折扣

    /**
     * 开启商品独立优惠
     * @return bool
     */
    public function enable()
    {
        //设置了折扣方式 并且 设置了折扣值
        return $this->discount_method!=0 && $this->discount_value != 0;
    }

    /**
     * 获取等级优惠价格
     * @param $price
     * @return int|mixed
     */
    public function getPrice($price)
    {
        switch ($this->discount_method) {
            case self::DISCOUNT:
                $result = $this->getMoneyPrice($price);
                break;
            case self::MONEY_OFF:
                $result = $this->getDiscountPrice($price);
                break;
            default:
                $result = $price;
                break;
        }
        return $result ? $result : 0;
    }

    /**
     * 商品独立等级立减后价格
     * @param $price
     * @return mixed
     */
    private function getMoneyPrice($price)
    {
        if ($this->discount_value == 0) {

            return $price;
        }
        return $price - $this->discount_value;
    }

    /**
     * 商品独立等级折扣后价格
     * @param $price
     * @return mixed
     */
    private function getDiscountPrice($price)
    {

        if ($this->discount_value == 0) {

            return $price;
        }
        return $price * ($this->discount_value / 10);
    }

    public function goods()
    {
        $this->belongsTo(Goods::class);
    }
}