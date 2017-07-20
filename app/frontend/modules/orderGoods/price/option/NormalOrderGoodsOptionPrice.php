<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/25
 * Time: 下午3:32
 */

namespace app\frontend\modules\orderGoods\price\option;

class NormalOrderGoodsOptionPrice extends NormalOrderGoodsPrice
{
    public function getFinalPrice()
    {
        return $this->orderGoods->goodsOption->final_price * $this->orderGoods->total;
    }

    public function getGoodsPrice()
    {
        return $this->orderGoods->goodsOption->product_price * $this->orderGoods->total;
    }
}