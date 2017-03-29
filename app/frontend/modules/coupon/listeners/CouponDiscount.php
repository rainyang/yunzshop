<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\coupon\listeners;


use app\common\events\discount\OnDiscountInfoDisplayEvent;
use app\common\events\discount\OrderDiscountWasCalculated;
use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\coupon\services\CouponFactory;
use app\frontend\modules\coupon\services\TestService;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

class CouponDiscount
{
    private $_event;

    public function getDiscountDetails(){
        $detail = [
            'name'=>'会11员等级折扣111',
            'value'=>'85',
            'price'=>'50',
            'plugin'=>'0',
        ];

        return $detail;
    }

    /**
     * 获得用户可使用的优惠券,预下单页
     * 传递过来一个预下单model
     * 返回可使用优惠券列表
     */
    public static function getValidCouponByPreOrder(PreGeneratedOrderModel $OrderModel)
    {
        $memberCouponsCollection = Coupon::getValidCoupon($OrderModel->getMemberModel())->get();
        //dump($memberCouponsCollection);exit;
        $memberValidCouponsCollection = collect();
        $memberCouponsCollection->each(function ($memberCoupon) use ($OrderModel, $memberValidCouponsCollection){
            /*return self::validUseType($memberCoupon, $OrderModel) &&
            self::validEnoughMoney($memberCoupon, $OrderModel) &&
            self::validEnoughTime($memberCoupon, $OrderModel);*/
            $couponModel = new CouponFactory();
            $couponModel = $couponModel->createCoupon($OrderModel, $memberCoupon);
            if ($couponModel->getValidCoupon())
            {
                $memberValidCouponsCollection->push($couponModel);
            }
        });

        //dd($memberValidCouponsCollection);
        return $memberValidCouponsCollection;
        //dd(self::calCoupon($memberCouponsCollection, $OrderModel));
        /*$data = [
            ['name' => 'sss会员等级折扣111',
                'value' => '85',
                'price' => '-50',
                'plugin' => '0',
                'coupon_id' => '1'
                'goods' => [
                    [商品1优惠详情]
                    [商品2优惠详情]
                    [商品3优惠详情]
                ]
            ],
            ['name' => 'sss会员等级折扣111',
                'value' => '85',
                'price' => '-50',
                'plugin' => '1',
            ]
        ];*/
    }

    /**
     * @param OnDiscountInfoDisplayEvent $event
     * 显示优惠券信息
     */
    public function onDisplay(OnDiscountInfoDisplayEvent $event){
        $this->_event = $event;
        $OrderModel = $this->_event->getOrderModel();
        $couponService = new TestService($OrderModel);
        $coupons = $couponService->getOptionalCoupons();
        $data = [];
        foreach ($coupons as $coupon){
            /**
             * @var $coupon Coupon
             */
            $data[] = [
                'name' => $coupon->getMemberCoupon()->belongsToCoupon->name,
                'id' => $coupon->getMemberCoupon()->belongsToCoupon->id,
            ];
        }
        $event->addMap('coupon',$data);
    }


    /**
     * @param $events
     * 监听多个事件
     */
    public function subscribe($events)
    {
        $events->listen(
            OnDiscountInfoDisplayEvent::class,
            CouponDiscount::class.'@onDisplay'
        );

        $events->listen(
            OrderDiscountWasCalculated::class,
            CouponDiscount::class.'@onOrderCalculated'
        );

    }
}