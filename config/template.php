<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/10
 * Time: 上午9:30
 */

return [
    'seller_pay' => [
        'title' => '卖家通知(会员付款通知)',
        'subtitle' => '会员付款通知',
        'value' => 'seller_pay',
        'param' => [
            '粉丝昵称', '订单号', '下单时间', '支付时间', '支付方式', '订单金额', '运费', '商品详情（含规格）', '收件人姓名', '收件人电话', '收件人地址'
        ]
    ],
    'seller_receipt' => [
        'title' => '卖家通知(会员确认收货通知)',
        'subtitle' => '会员确认收货通知',
        'value' => 'seller_receipt',
        'param' => [
            '粉丝昵称', '订单号', '确认收货时间', '运费', '商品详情（含规格）', '收件人姓名', '收件人电话', '收件人地址'
        ]
    ],
    'buyer_order_create_success' => [
        'title' => '买家通知(订单提交成功通知)',
        'subtitle' => '订单提交成功通知',
        'value' => 'buyer_order_create_success',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）'
        ]
    ],
    'buyer_order_pay_success' => [
        'title' => '买家通知(订单支付成功通知)',
        'subtitle' => '订单支付成功通知',
        'value' => 'buyer_order_pay_success',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '支付方式', '支付时间'
        ]
    ],
    'buyer_order_sending' => [
        'title' => '买家通知(订单发货通知)',
        'subtitle' => '订单发货通知',
        'value' => 'buyer_order_sending',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '发货时间', '快递公司', '快递单号'
        ]
    ],
    'buyer_order_receipt_success' => [
        'title' => '买家通知(订单确认收货通知)',
        'subtitle' => '订单确认收货通知',
        'value' => 'buyer_order_receipt_success',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '确认收货时间'
        ]
    ],
    'buyer_order_cancle_success' => [
        'title' => '买家通知(订单取消通知)',
        'subtitle' => '订单取消通知',
        'value' => 'buyer_order_cancle_success',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '支付方式', '订单取消时间'
        ]
    ],
    'buyer_refund_apply' => [
        'title' => '买家通知(退款申请通知)',
        'subtitle' => '退款申请通知',
        'value' => 'buyer_refund_apply',
        'param' => [
            '商城名称', '粉丝昵称', '退款单号', '退款申请时间', '退款方式', '退款金额', '退款原因'
        ]
    ],
    'buyer_refund_apply_success' => [
        'title' => '买家通知(退款成功通知)',
        'subtitle' => '退款成功通知',
        'value' => 'buyer_refund_apply_success',
        'param' => [
            '商城名称', '粉丝昵称', '退款单号', '退款申请时间', '退款成功时间', '退款方式', '退款金额', '退款原因'
        ]
    ],
    'buyer_refund_apply_reject' => [
        'title' => '买家通知(退款申请驳回通知)',
        'subtitle' => '退款申请驳回通知',
        'value' => 'buyer_refund_apply_reject',
        'param' => [
            '商城名称', '粉丝昵称', '退款单号', '退款申请时间', '退款方式', '退款金额', '退款原因', '驳回原因'
        ]
    ],
    'buyer_order_status_change' => [
        'title' => '买家通知(订单状态更新)',
        'subtitle' => '订单状态更新',
        'value' => 'buyer_order_status_change',
        'param' => [
            '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '支付方式', '原订单价格', '修改后订单价格', '修改时间'
        ]
    ],
    'order_parent_notice' => [
        'title' => '订单两级消息通知',
        'subtitle' => '下级订单通知',
        'value' => 'order_parent_notice',
        'param' => [
            '下级昵称', '下级层级', '订单状态', '订单号', '订单金额',
        ]
    ],
    'member_upgrade' => [
        'title' => '会员(会员升级)',
        'subtitle' => '会员升级',
        'value' => 'member_upgrade',
        'param' => [
            '粉丝昵称', '旧等级', '新等级', '时间',
        ]
    ],
    'member_agent' => [
        'title' => '会员(获得推广权限通知)',
        'subtitle' => '获得推广权限通知',
        'value' => 'member_agent',
        'param' => [
            '昵称', '时间'
        ]
    ],
    'member_new_lower' => [
        'title' => '会员(新增下线通知)',
        'subtitle' => '新增下线通知',
        'value' => 'member_new_lower',
        'param' => [
            '昵称', '时间', '下级昵称'
        ]
    ],
    'finance_balance_change' => [
        'title' => '财务(余额变动通知)',
        'subtitle' => '余额变动通知',
        'value' => 'finance_balance_change',
        'param' => [
            '商城名称', '昵称', '时间', '余额变动金额', '余额变动类型', '变动后余额数值'
        ]
    ],
    'finance_point_change' => [
        'title' => '财务(积分变动通知)',
        'subtitle' => '积分变动通知',
        'value' => 'finance_point_change',
        'param' => [
            '商城名称', '昵称', '时间', '积分变动金额', '积分变动类型', '变动后积分数值'
        ]
    ],
    'finance_income_withdraw' => [
        'title' => '财务(提现申请通知)',
        'subtitle' => '提现申请通知',
        'value' => 'finance_income_withdraw',
        'param' => [
            '昵称', '时间', '收入类型', '金额', '手续费', '提现方式'
        ]
    ],
    'finance_income_withdraw_check' => [
        'title' => '财务(提现审核通知)',
        'subtitle' => '提现审核通知',
        'value' => 'finance_income_withdraw_check',
        'param' => [
            '昵称', '时间', '收入类型', '状态', '金额', '手续费', '审核通过金额', '提现方式'
        ]
    ],
    'finance_income_withdraw_pay' => [
        'title' => '财务(提现打款通知)',
        'subtitle' => '提现打款通知',
        'value' => 'finance_income_withdraw_pay',
        'param' => [
            '昵称', '时间', '收入类型', '状态', '金额', '提现方式'
        ]
    ],
    'finance_income_withdraw_arrival' => [
        'title' => '财务(提现到账通知)',
        'subtitle' => '提现到账通知',
        'value' => 'finance_income_withdraw_arrival',
        'param' => [
            '昵称', '时间', '收入类型', '状态', '金额', '提现方式'
        ]
    ],

    'finance_balance_withdraw_submit' => [
        'title' => '余额(提现提交通知)',
        'subtitle' => '余额提现提交通知',
        'value' => 'finance_balance_withdraw_submit',
        'param' => [
            '时间', '金额', '手续费'
        ]
    ],


    'finance_balance_withdraw_success' => [
        'title' => '余额(提现成功通知)',
        'subtitle' => '余额提现成功通知',
        'value' => 'finance_balance_withdraw_success',
        'param' => [
            '时间', '金额', '手续费'
        ]
    ],

    'finance_balance_withdraw_fail' => [
        'title' => '余额(提现失败通知)',
        'subtitle' => '余额提现失败通知',
        'value' => 'finance_balance_withdraw_fail',
        'param' => [
            '时间', '金额', '手续费'
        ]
    ],
    'coupon_expire' => [
        'title' => '优惠券(优惠券过期提醒)',
        'subtitle' => '优惠券过期提醒',
        'value' => 'coupon_expire',
        'param' => [
            '优惠券名称', '优惠券使用范围', '过期时间'
        ]
    ],
    'buy_goods_message' => [
        'title' => '购买商品通知',
        'subtitle' => '购买商品通知',
        'value' => 'buy_goods_message',
        'param' => [
            '订单编号', '商品名称（含规格）', '会员昵称', '商品金额', '商品数量', '订单状态', '时间'
        ]
    ],
    'coupon_obtain' => [
        'title' => '优惠券(获得优惠券通知)',
        'subtitle' => '获得优惠券通知',
        'value' => 'coupon_obtain',
        'param' => [
            '优惠券名称', '优惠券使用范围', '优惠券使用条件','优惠方式', '过期时间'
        ]

    ]


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

