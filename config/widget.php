<?php

return [
    'goods' => [
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
    ]
];