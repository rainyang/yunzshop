<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/30
 * Time: 上午10:16
 */

namespace app\frontend\models;


class MemberLevel extends \app\common\models\MemberLevel
{
    /**
     * 商品全局等级折扣后价格
     * @param $goodsPrice
     * @return float|int
     */
    public function getMemberLevelGoodsDiscountAmount($goodsPrice)
    {
        // 商品折扣 默认 10折
        $this->discount = trim($this->discount);
        $this->discount = $this->discount == false ? 10 : $this->discount;
        // 折扣/10 得到折扣百分比
        return (1 - $this->discount / 10) * $goodsPrice;
    }
}