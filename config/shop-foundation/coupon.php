<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/28
 * Time: 7:00 PM
 */
return [
    'OrderCoupon' => [
        'scope' => [
            [
                'key' => \app\common\models\Coupon::COUPON_GOODS_USE,
                'class' => function ($coupon) {
                    return new \app\frontend\modules\coupon\services\models\UseScope\GoodsScope($coupon);
                },
            ],
            [
                'key' => \app\common\models\Coupon::COUPON_CATEGORY_USE,
                'class' => function ($coupon) {
                    return new \app\frontend\modules\coupon\services\models\UseScope\CategoryScope($coupon);
                },
            ],
            [
                'key' => \app\common\models\Coupon::COUPON_SHOP_USE,
                'class' => function ($coupon) {
                    return new \app\frontend\modules\coupon\services\models\UseScope\ShopScope($coupon);
                },
            ],
        ]
    ]
];