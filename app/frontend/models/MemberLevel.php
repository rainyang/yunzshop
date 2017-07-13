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
    public function getMemberLevelGoodsDiscountPrice($goodsPrice)
    {
        $this->discount = $this->discount == false ? 1 : $this->discount;
        return $this->discount * $goodsPrice;
    }
}