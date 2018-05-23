<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 上午11:17
 */

namespace app\frontend\modules\dispatch\discount;


/**
 * 全场运费满额减
 * Class EnoughReduce
 * @package app\frontend\modules\dispatch\discount
 */
class EnoughReduce extends BaseFreightDiscount
{
    protected $name = '全场运费满额减';
    protected $code = 'EnoughReduce';

    protected function _getAmount()
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
            return $this->order->getDispatchPrice();
        }
        // 订单金额满足满减金额
        if ($this->order->price >= \Setting::get('enoughReduce.freeFreight.enough')) {
            return $this->order->getDispatchAmount();
        }
        return 0;
    }
}