<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 上午11:17
 */

namespace app\frontend\modules\dispatch\discount;

use app\frontend\models\order\PreOrderDiscount;

class EnoughReduce
{
    private $order;
    private $price;
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * 全场满额包邮
     * @param $orderPrice
     * @param $freight
     * @return bool
     */
    public function getPrice($orderPrice, $freight)
    {
        if(!isset($this->price)){
            $enoughReduce = $this->_enoughReduce($orderPrice,$freight);
            if ($enoughReduce > 0) {
                $preOrderDiscount = new PreOrderDiscount([
                    'discount_code' => 'enoughReduce',
                    'amount' => $enoughReduce,
                    'name' => '全场满额包邮',

                ]);
                $preOrderDiscount->setOrder($this->order);
            }
            $this->price = $enoughReduce;
        }
        return $this->price;
    }

    private function _enoughReduce($orderPrice, $freight)
    {
        if (!\Setting::get('enoughReduce.freeFreight.open')) {
            return 0;
        }
        // 不参与包邮地区
        if (in_array($this->order->orderAddress->city_id, \Setting::get('enoughReduce.freeFreight.city_ids'))) {
            return 0;
        }
        // 设置为0 全额包邮
        if (\Setting::get('enoughReduce.freeFreight.enough') === 0 || \Setting::get('enoughReduce.freeFreight.enough') === '0') {
            return $freight;
        }
        // 订单金额满足满减金额
        if ($orderPrice >= \Setting::get('enoughReduce.freeFreight.enough')) {
            return $freight;
        }
        return 0;
    }
}