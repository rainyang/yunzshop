<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/27
 * Time: 下午1:58
 */

namespace app\common\models;


use function GuzzleHttp\default_user_agent;

class GoodsDiscount extends BaseModel
{
    public $table = 'yz_goods_discount';
    public $guarded = [];
    const MONEY_OFF = 1;//立减
    const DISCOUNT = 2;//折扣


    public function getPrice($price)
    {
        switch ($this->discount_method) {
            case self::MONEY_OFF:
                $result = $this->getMoneyPrice($price);
                break;
            case self::DISCOUNT:
                $result = $this->getDiscountPrice($price);
                break;
            default:
                $result = $price;
                break;
        }
        return $result ? $result : 0;
    }

    private function getMoneyPrice($price)
    {
        return $price - $this->discount_value;
    }

    private function getDiscountPrice($price)
    {
        return $price * ($this->discount_value / 10);
    }

    public function goods()
    {
        $this->belongsTo(Goods::class);
    }
}