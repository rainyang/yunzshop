<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/10
 * Time: 上午9:30
 */

return [
    'order_param' => [
        'title' => '订单类',
        'subtitle' => '订单信息',
        'value' => 'order',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '订单金额', '运费', '商品详情', '购买者姓名', '购买者电话', '收货地址', '下单时间', '支付时间', '发货时间', '收货时间', '备注信息'
        ]
    ],
    'member_upgrade' => [
        'title' => '会员升级',
        'subtitle' => '会员升级',
        'value' => 'member_upgrade',
        'param' => [
            '商城名称', '粉丝昵称', '旧等级', '新等级', '时间', '等级有效期'
        ]
    ],
    'member_agent' => [
        'title' => '会员推广',
        'subtitle' => '会员推广',
        'value' => 'member_agent',
        'param' => [
            '商城名称', '粉丝昵称', '时间', '上级昵称'
        ]
    ],
    'member_fans' => [
        'title' => '新增下线',
        'subtitle' => '新增下线',
        'value' => 'member_fans',
        'param' => [
            '粉丝昵称', '时间', '下级会员昵称'
        ]
    ],

    /*$data = [
        [
            'name' => '粉丝昵称',
            'value' => '杨洋'
        ],
        [
            'name' => '时间',
            'value' => '2017-11-10'
        ],
        [
            'name' => '下级会员昵称',
            'value' => '沈阳'
        ],
    ]*/
];

