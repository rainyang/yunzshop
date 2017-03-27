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
use app\common\events\discount\OrderGoodsDiscountWasCalculated;
use app\common\models\Coupon;
use app\frontend\modules\coupon\services\CouponFactory;
use app\frontend\modules\coupon\services\CouponService;
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
        //$order_model = $even->getOrderModel();
        $OrderModel = $this->_event->getOrderModel();
        //dd($OrderModel->getOrderGoodsModels());
        $data = self::getValidCouponByPreOrder($OrderModel);
        //$OrderModel->getMemberModel();
        //$OrderModel->getOrderGoodsModels();
        //dd($OrderModel);
        //$OrderModel->getOrderGoodsModels()->getGoods();

        $event->addMap('coupon',$data);
    }

    /**
     * @param OrderDiscountWasCalculated $event
     * 计算订单优惠
     */
    public function onOrderCalculated(OrderDiscountWasCalculated $event){
        $this->_event = $event;
        //$order_model = $even->getOrderModel();
        $OrderModel = $this->_event->getOrderModel();
        $OrderModel->getMemberModel();
        $OrderModel->getOrderGoodsModels();
        //$OrderModel->getOrderGoodsModels()->getGoods();
        //dd($OrderModel);
        $data = [
            'name' => '11sss会员等级折扣111',
            'value' => '85',
            'price' => '-30',
            'plugin' => '1',
        ];
        $event->addData($data);
    }

    /**
     * @param OrderGoodsDiscountWasCalculated $event
     * 计算订单商品优惠
     */
    public function onOrderGoodsCalculated(OrderGoodsDiscountWasCalculated $event)
    {
        //根据member_coupon_id获取coupon,判断类型,商品和分类类型的优惠券才走这里,其它的走订单计算的.
        $member_coupon_id = \YunShop::request()->member_coupon_id;
        $this->_event = $event;
        //$order_model = $even->getOrderModel();
        
        //一条商品
        $OrderModel = $this->_event->getOrderGoodsModel();
        //dd($OrderModel);
        //$OrderModel->getOrderGoodsModels()->getGoods();
        $data = [
            'name' => '22sss会员等级折扣111',
            'value' => '85',
            'price' => '-20',
            'plugin' => '1',
        ];
        $event->addData($data);
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

        $events->listen(
            OrderGoodsDiscountWasCalculated::class,
            CouponDiscount::class.'@onOrderGoodsCalculated'
        );
    }
}