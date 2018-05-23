<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\coupon\listeners;

use app\common\events\discount\OnDiscountInfoDisplayEvent;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\facades\Setting;
use app\common\services\finance\PointService;
use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\coupon\services\CouponService;

class CouponDiscount
{
    private $event;


    public function deductionAwardPoint(AfterOrderReceivedEvent $event)
    {
        if (!Setting::get('coupon.award_point')) {
            return null;
        }
        $orderModel = $event->getOrderModel();

        $orderDiscount = $orderModel->orderDiscount;

        $point = 0;
        if ($orderDiscount) {
            foreach ($orderDiscount as $key => $deduction) {

                if ($deduction['discount_code'] == 'coupon') {
                    $point = $deduction['amount'];
                    break;
                }
            }
        }
        if ($point <= 0) {
            return null;
        }
        $data = [
            'point_income_type' => PointService::POINT_INCOME_GET,
            'point_mode'        => PointService::POINT_MODE_COUPON_DEDUCTION_AWARD,
            'member_id'         => $orderModel->uid,
            'point'             => $point,
            'remark'            => '订单：'.$orderModel->order_sn.'优惠券抵扣奖励积分'.$point,
        ];
        return (new PointService($data))->changePoint();

    }

    /**
     * @param OnDiscountInfoDisplayEvent $event
     * 监听订单显示优惠券选项事件
     */
    public function onDisplay(OnDiscountInfoDisplayEvent $event)
    {
        $this->event = $event;
        $orderModel = $this->event->getOrderModel();

        $couponService = new CouponService($orderModel);
        $coupons = $couponService->getOptionalCoupons();

        $data = $coupons->map(function ($coupon) {
            /**
             * @var $coupon Coupon
             */
            $coupon->getMemberCoupon()->belongsToCoupon->setDateFormat('Y-m-d');
            return $coupon->getMemberCoupon();
        });
        $event->addMap('coupon', $data);
    }

    //订单生成后销毁优惠券 todo 重复查询了,需要使用计算优惠券价格时获取的优惠券列表
    public function onOrderCreated(AfterOrderCreatedEvent $event)
    {
        $this->event = $event;
        $orderModel = $this->event->getOrderModel();
        $couponService = new CouponService($orderModel);
        $couponService->destroyUsedCoupons();
    }

    /*
     * 监听订单完成事件
     */
    public function onOrderReceived(AfterOrderReceivedEvent $event)
    {
        $this->event = $event;
        $orderModel = $this->event->getOrderModel();
        $orderGoods = $orderModel->hasManyOrderGoods;//订单商品
        $couponService = new CouponService($orderModel, null, $orderGoods);
        $couponService->sendCoupon();
    }

    /**
     * @param $events
     * 监听多个事件
     */
    public function subscribe($events)
    {
        $events->listen(
            OnDiscountInfoDisplayEvent::class,
            CouponDiscount::class . '@onDisplay'
        );
        $events->listen(
            AfterOrderCreatedEvent::class,
            CouponDiscount::class . '@onOrderCreated'
        );
        $events->listen(
            AfterOrderReceivedEvent::class,
            CouponDiscount::class . '@onOrderReceived'
        );
        $events->listen(
            AfterOrderReceivedEvent::class,
            CouponDiscount::class . '@deductionAwardPoint'
        );

    }
}