<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/13
 * Time: 5:07 PM
 */

namespace app\common\modules\trade\models;

use app\common\models\BaseModel;
use app\common\models\MemberCart;
use app\common\modules\memberCart\MemberCartCollection;
use app\common\modules\order\OrderCollection;
use Illuminate\Support\Collection;

/**
 * Class Trade
 * @package app\common\modules\trade\models
 * @property OrderCollection orders
 */
class Trade extends BaseModel
{
//    /**
//     * @var MemberCartCollection
//     */
//    private $memberCarts;

    public function init(MemberCartCollection $memberCartCollection)
    {

        $this->setRelation('orders', $this->getOrderCollection($memberCartCollection));
        $this->initAttribute();

//        //将订单中的优惠券 合并摊平到数组外层
//        $data['discount']['coupon'] =
//            //将订单中的收获地址 拿到外层
//        $data['dispatch'] = $orders[0]['dispatch'];
//        //删掉内层的数据
//
//        $orders->map(function (Collection $order_data) {
//            $order_data->discount->forget('coupon');
//            return $order_data->forget('dispatch');
//        });

    }

    public function initAttribute()
    {
        $attributes = [
            'totalPrice' => $this->orders->sum('price'),
            'totalGoodsPrice' => $this->orders->sum('order_goods_price'),
            'totalDispatchPrice' => $this->orders->sum('dispatch_price'),
            'totalDiscountPrice' => $this->orders->sum('discount_price'),
            'totalDeductionPrice' => $this->orders->sum('deduction_price'),
        ];
//        $this->discount->coupon = $this->orders->map(function ($order) {
//            dd($order);
//            return $order['discount']['coupon'];
//        })->collapse();
//        dd($this->discount->coupon);

        $attributes = array_merge($this->getAttributes(), $attributes);
        return $attributes;
    }

    /**
     * 显示订单数据
     * @return array
     */
    public function toArray()
    {
        $attributes = parent::toArray();
        $attributes = $this->formatAmountAttributes($attributes);
        return $attributes;
    }

    public function getOrderCollection(MemberCartCollection $memberCartCollection)
    {
        // 按插件分组
        $groups = $memberCartCollection->groupByPlugin();
        // 分组下单
        $orderCollection = $groups->map(function (MemberCartCollection $memberCartCollection) {

            return $memberCartCollection->getOrder();
        });
        return new OrderCollection($orderCollection->all());
    }
}