<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/3
 * Time: 上午11:46
 */

namespace app\frontend\models\goods;


use app\frontend\models\OrderGoods;

class Sale extends \app\common\models\Sale
{
    /**
     * 是否包邮
     * @param OrderGoods $orderGoods
     * @return bool
     */
    public function isFree(OrderGoods $orderGoods)
    {
        $this->setRelation('orderGoods', $orderGoods);

        if (!isset($this->orderGoods->order->orderAddress)) {
            //未选择地址时
            return false;
        }

        if (!$this->inFreeArea()) {
            //收货地址不在包邮区域
            return false;
        }

        return $this->enoughQuantity($this->orderGoods->total) || $this->enoughAmount($this->orderGoods->price);
    }

    /**
     * 收货地址不在包邮区域
     * @return bool
     */
    private function inFreeArea()
    {
        $ed_areaids = (explode(',', $this->ed_areaids));

        if (empty($ed_areaids)) {
            return true;
        }
        if (in_array($this->orderGoods->order->orderAddress->city_id, $ed_areaids)) {
            return false;
        }
        return true;
    }

    /**
     * 单商品购买数量
     * @param $total
     * @return bool
     */
    private function enoughQuantity($total)
    {
        if ($this->ed_num == false) {
            return false;
        }
        return $total >= $this->ed_num;
    }

    /**
     * 单商品价格
     * @param $price
     * @return bool
     */
    private function enoughAmount($price)
    {
        if ($this->ed_money == false) {
            return false;
        }

        return $price >= $this->ed_money;
    }
}