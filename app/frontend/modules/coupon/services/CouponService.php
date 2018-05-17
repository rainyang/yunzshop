<?php

namespace app\frontend\modules\coupon\services;

use app\common\helpers\ArrayHelper;
use app\common\models\goods\GoodsCoupon;
use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\order\models\PreOrder;
use app\Jobs\addGoodsCouponQueueJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;
use app\backend\modules\coupon\services\MessageNotice;

class CouponService
{
    use DispatchesJobs;
    private $order;
    private $orderGoods;
    private $coupon_method;
    private $selectedMemberCoupon;
    public function __construct( $order, $coupon_method = null, $orderGoods = [])
    {

        $this->order = $order;
        $this->orderGoods = $orderGoods;
        $this->coupon_method = $coupon_method;
    }

    /**
     * 获取订单优惠金额
     * @return int
     */
    public function getOrderDiscountPrice()
    {
        return $this->getAllValidCoupons()->sum(function ($coupon) {
            if(!$coupon->valid()){
                return 0;
            }
            /**
             * @var $coupon Coupon
             */
            $coupon->activate();
            return $coupon->getDiscountAmount();
        });
    }

//    /**
//     * 激活订单优惠券
//     */
//    public function activate()
//    {
//        return $this->getAllValidCoupons()->each(function ($coupon) {
//            /**
//             * @var $coupon Coupon
//             */
//            dump(1);
//            $coupon->activate();
//        });
//    }

    /**
     * 获取订单可算的优惠券
     * @return Collection
     */
    public function getOptionalCoupons()
    {
        //dd(MemberCouponService::getCurrentMemberCouponCache($this->order->belongsToMember));
        //dd($this->getMemberCoupon());
        $coupons = $this->getMemberCoupon()->map(function ($memberCoupon) {
            return new Coupon($memberCoupon, $this->order);
        });
        $result = $coupons->filter(function ($coupon) {
            /**
             * @var $coupon Coupon
             */
            //不可选
            if (!$coupon->isOptional()) {
                return false;
            }
            $coupon->getMemberCoupon()->valid = $coupon->valid();//界面标蓝
            $coupon->getMemberCoupon()->checked = false;//界面选中

            return true;
        })->values();

        $result = collect($this->order->orderCoupons)->map(function($orderCoupon){
            // 已参与订单价格计算的优惠券
            $orderCoupon->coupon->getMemberCoupon()->valid = true;
            $orderCoupon->coupon->getMemberCoupon()->checked = true;
            return $orderCoupon->coupon;
        })->merge($result);

        return $result;
    }

    /**
     * 记录使用过的优惠券
     */
    public function destroyUsedCoupons()
    {
        $this->getSelectedMemberCoupon()->each(function ($memberCoupon) {

            return (new Coupon($memberCoupon, $this->order))->destroy();
        });
    }

    /**
     * 获取所有选中并有效的优惠券
     * @return Collection
     */
    public function getAllValidCoupons()
    {
        $coupon = $this->getSelectedMemberCoupon()->map(function ($memberCoupon) {
            return new Coupon($memberCoupon, $this->order);
        });
        $result = $coupon->filter(function ($coupon) {
            /**
             * @var $coupon Coupon
             */
            return $coupon->valid();
        });

        return $result;
    }

    /**
     * 用户拥有的优惠券
     * @return Collection
     */
    private function getMemberCoupon()
    {
        $coupon_method = $this->coupon_method;
        $result = MemberCouponService::getCurrentMemberCouponCache($this->order->belongsToMember);
        if (isset($coupon_method)) {// 折扣/立减
            $result = $result->filter(function ($memberCoupon) use ($coupon_method) {
                return $memberCoupon->belongsToCoupon->coupon_method == $coupon_method;
            });
        }
        //dd($result->toArray());exit;
        return $result;

    }

    /**
     * 用户拥有并选中的优惠券
     * @return Collection
     */
    private function getSelectedMemberCoupon()
    {
        if(!isset($this->selectedMemberCoupon)){
            $member_coupon_ids = ArrayHelper::unreliableDataToArray(\Request::input('member_coupon_ids'));
            
            if(\Setting::get('coupon.is_singleton')){
                $member_coupon_ids = array_slice($member_coupon_ids,0,1);
            }
            $this->selectedMemberCoupon = $this->getMemberCoupon()->filter(function ($memberCoupon) use ($member_coupon_ids) {
                return in_array($memberCoupon->id, $member_coupon_ids);
            });
        }

        return $this->selectedMemberCoupon;
    }

    public function sendCoupon()
    {
        $orderGoods = $this->orderGoods;
        foreach ($orderGoods as $goods) {
            $goodsCoupon = GoodsCoupon::ofGoodsId($goods->goods_id)->first();

            //dump($goodsCoupon);
            //未开启 或 已关闭 或 未设置优惠券
            if(!$goodsCoupon || !$goodsCoupon->is_give || !$goodsCoupon->coupon){
                continue;
            }

            //每月发送时，发送月数 为空 或为 0
            if ($goodsCoupon->send_type == '0' && empty($goodsCoupon->send_num)) {
                continue;
            }

            for ($i = 1; $i <= $goods->total; $i++) {

                switch ($goodsCoupon->send_type)
                {
                    //订单完成立即发送
                    case '1':
                        $this->promptlySendCoupon($goodsCoupon);
                        break;
                    //每月发送
                    case '0':
                        $this->everyMonthSendCoupon($goodsCoupon);
                        break;
                }
            }
        }
    }

    public function promptlySendCoupon($goodsCoupon)
    {
        $coupon_ids = [];
        foreach ($goodsCoupon->coupon as $key => $item) {
            if ($item['coupon_several'] > 1) {
                for ($i = 1; $i <= $item['coupon_several']; $i++) {
                    $coupon_ids[] = $item['coupon_id'];
                }
            } else {
                $coupon_ids[] = $item['coupon_id'];
            }
        }
        //发送获取优惠券通知
        MessageNotice::couponNotice($coupon_ids,$this->order->uid);

        (new CouponSendService())->sendCouponsToMember($this->order->uid,$coupon_ids,4,$this->order->order_sn);
    }

    public function everyMonthSendCoupon($goodsCoupon)
    {
        foreach ($goodsCoupon->coupon as $key => $item) {
            if ($item['coupon_several'] > 1) {
                for ($i = 1; $i <= $item['coupon_several']; $i++) {
                    $this->addSendCouponQueue($goodsCoupon);
                }
            } else {
                $this->addSendCouponQueue($goodsCoupon);
            }
        }
    }

    public function addSendCouponQueue($goodsCoupon)
    {
        $queueData = [
            'uniacid'       => \YunShop::app()->uniacid,
            'goods_id'      => $goodsCoupon->goods_id,
            'uid'           => $this->order->uid,
            'coupon_id'     => $goodsCoupon->coupon['coupon_id'],
            'send_num'      => $goodsCoupon->send_num,
            'end_send_num'  => 0,
            'status'        => 0,
            'created_at'    => time()
        ];
        $this->dispatch((new addGoodsCouponQueueJob($queueData)));
    }

}