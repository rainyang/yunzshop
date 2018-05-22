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
        'data' => '亲爱的[昵称]，您的积分账户有新的变动，具体内容如下：',
        'remark' => '感谢您的支持！',
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
    ]
];