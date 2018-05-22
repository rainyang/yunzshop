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
                "keywords" => "keyword1",
                "value" => "[积分变动金额]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword1",
                "value" => "[积分变动类型]",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword1",
                "value" => "[变动后积分数值]",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持！',
    ],
    'buy_goods_msg' => [
        'template_id_short' => 'OPENTM205213550',
        'title' => '购买商品通知！',
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
                "keywords" => "keyword1",
                "value" => "[会员昵称][订单状态][商品名称（含规格]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword1",
                "value" => "[订单编号]",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword1",
                "value" => "[变动后积分数值]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请尽快处理您的订单！',

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
                "keywords" => "keyword1",
                "value" => "[余额变动类型]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword1",
                "value" => "[余额变动金额]",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword1",
                "value" => "[变动后余额数值]",
                "color" => "#000000",
            ],
        ],
        'remark' => '详情请点击此消息进入会员中心-余额-明细进行查询!',

    ],
];