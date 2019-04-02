<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/2
 * Time: 上午11:23
 */

return array(
    'member_order_operations' => [
        'waitPay' => [
            \app\frontend\modules\order\operations\member\Pay::class,
            \app\frontend\modules\order\operations\member\Close::class,

        ],
        'waitSend' => [
            \app\frontend\modules\order\operations\member\ApplyRefund::class,
            \app\frontend\modules\order\operations\member\ContactCustomerService::class,
            \app\frontend\modules\order\operations\member\Refunding::class,
            \app\frontend\modules\order\operations\member\Refunded::class,
            \app\frontend\modules\order\operations\member\Coupon::class, //分享优惠卷

        ],
        'waitReceive' => [
            \app\frontend\modules\order\operations\member\ExpressInfo::class,
            \app\frontend\modules\order\operations\member\Receive::class,
            \app\frontend\modules\order\operations\member\ApplyRefund::class,
            \app\frontend\modules\order\operations\member\ContactCustomerService::class,
            \app\frontend\modules\order\operations\member\Refunding::class,
            \app\frontend\modules\order\operations\member\Refunded::class,
            \app\frontend\modules\order\operations\member\Coupon::class, //分享优惠卷
        ],
        'complete' => [
            \app\frontend\modules\order\operations\member\ExpressInfo::class,
            \app\frontend\modules\order\operations\member\Delete::class,
            \app\frontend\modules\order\operations\member\ApplyRefund::class,
            \app\frontend\modules\order\operations\member\ContactCustomerService::class,
            \app\frontend\modules\order\operations\member\Refunding::class,
            \app\frontend\modules\order\operations\member\Refunded::class,
            \app\frontend\modules\order\operations\member\CheckInvoice::class,
            \app\frontend\modules\order\operations\member\Coupon::class, //分享优惠卷

        ],
        'close' => [
            \app\frontend\modules\order\operations\member\ExpressInfo::class,
            \app\frontend\modules\order\operations\member\Delete::class,
            \app\frontend\modules\order\operations\member\Refunded::class,
        ],

    ],
    'status' => [
        0 => 'waitPay',
        1 => 'waitSend',
        2 => 'waitReceive',
        3 => 'complete',
        -1 => 'close',
    ],

);