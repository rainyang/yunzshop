<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/3
 * Time: 上午11:46
 */

namespace app\frontend\modules\goods\models\goods;


use app\frontend\modules\order\models\OrderGoods;

class Sale extends \app\common\models\Sale
{
    public function isFree(OrderGoods $orderGoods)
    {
        $this->setRelation('orderGoods', $orderGoods);

        if ($this->goods->hasOneSale->ed_areaids) {
        }
        $this->orderGoods->order->orderAddress->city_id;
        return $this->enoughQuantity($this->orderGoods->goods_total) || $this->enoughAmount($this->orderGoods->price);
    }

    public function getEdAreaidsAttribute()
    {
        return explode(',', $this->ed_areaids);
    }

    private function enoughQuantity($total)
    {
        if ($this->ed_num == false) {
            return false;
        }
        return $total >= $this->ed_num;
    }

    private function enoughAmount($price)
    {
        if ($this->ed_money == false) {
            return false;
        }
        return $price >= $this->ed_money;
    }
}