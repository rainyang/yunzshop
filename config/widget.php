<?php

return [
    'goods' => [
        'div_from' => [
            'title' => '表单',
            'class' => 'app\backend\widgets\goods\DivFromWidget'
        ],
        'tab_sale' => [
            'title' => '营销',
            'class' => 'app\backend\widgets\goods\SaleWidget',
        ],
        'tab_notice' => [
            'title' => '消息通知',
            'class' => 'app\backend\widgets\goods\NoticeWidget',
        ],
        'tab_share' => [
            'title' => '分享关注',
            'class' => 'app\backend\widgets\goods\ShareWidget',
        ],
        'tab_privilege' => [
            'title' => '权限',
            'class' => 'app\backend\widgets\goods\PrivilegeWidget',
        ],
        'tab_discount' => [
            'title' => '折扣',
            'class' => 'app\backend\widgets\goods\DiscountWidget',
        ],
        'tab_dispatch' => [
            'title' => '配送',
            'class' => 'app\backend\widgets\goods\DispatchWidget'
        ],
        'tab_coupon' => [
            'title' => '优惠券',
            'class' => 'app\backend\widgets\goods\CouponWidget'
        ],
        'tb_limitbuy' => [
            'title' => '限时购',
            'class' => 'app\backend\widgets\goods\LimitBuyWidget'
        ],
        'tab_filtering' => [
            'title' => '商品标签',
            'class' => 'app\backend\widgets\goods\FilteringWidget'
        ],

    ],
    'withdraw' => [
        'income' => [
            'title' => '收入提现基础设置',
            'class' => 'app\backend\widgets\finance\IncomeWidget',
        ],
        'notice' => [
            'title' => '收入提现通知',
            'class' => 'app\backend\widgets\finance\WithdrawNoticeWidget',
        ]
    ]
];