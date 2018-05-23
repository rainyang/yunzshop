<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/5/22
 * Time: 16:39
 */

return [
    'point_change' => [
        'template_id_short' => 'OPENTM207509450',
        'title' => '积分变动通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '亲爱的[昵称]，您的积分账户有新的变动，具体内容如下：',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[变动时间]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[积分变动金额]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[积分变动类型]",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword4",
                "value" => "[变动后积分数值]",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持！',
    ],
    'buy_goods_msg' => [
        'template_id_short' => 'OPENTM205213550',
        'title' => '购买商品通知[卖家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有新的订单！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[时间]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[会员昵称][订单状态][商品名称（含规格]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[订单编号]",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword4",
                "value" => "[变动后积分数值]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请尽快处理您的订单！',

    ],
    'seller_order_create' => [
        'template_id_short' => 'OPENTM205213550',
        'title' => '订单生成通知[卖家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有新的订单！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[时间]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[会员昵称][订单状态][商品名称（含规格]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[订单编号]",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword4",
                "value" => "[变动后积分数值]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请尽快处理您的订单！',

    ],
    'seller_order_pay' => [
        'template_id_short' => 'OPENTM207525131',
        'title' => '订单支付通知[卖家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您商城有新的付款订单！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[粉丝昵称]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[商品详情（含规格）]通过[支付方式]于[支付时间]支付了[订单金额]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[订单号][下单时间]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请尽快处理！',

    ],
    'seller_order_finish' => [
        'template_id_short' => 'OPENTM413711838',
        'title' => '订单完成通知[卖家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您商城有新的付款订单！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[订单号][商品详情（含规格)]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[粉丝昵称]于[确认收货时间]已成功确认收货",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[收件人姓名][收货人电话][收件人地址]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请确认已完成订单！',

    ],
    'other_toggle_temp' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '订单两级消息通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '恭喜您获得了推广权益！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "订单通知",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "两级会员订单",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您的[下级层级]会员[下级昵称]下单购买了商品，订单编号：[订单编号]，订单金额：[]订单金额]，订单状态为：[订单状态]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',

    ],
    'order_submit_success' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '订单提交成功通知[买家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的订单已提交成功',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[商城名称]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[下单时间]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[商品详情（含规格)]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',

    ],
    'balance_change' => [
        'template_id_short' => 'OPENTM401833445',
        'title' => '余额变动通知！',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '尊敬的用户,你的账户发生变动',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[变动时间]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[余额变动类型]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[余额变动金额]",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword4",
                "value" => "[变动后余额数值]",
                "color" => "#000000",
            ],
        ],
        'remark' => '详情请点击此消息进入会员中心-余额-明细进行查询!',

    ],
];