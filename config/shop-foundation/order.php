<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/2
 * Time: 上午11:23
 */

return array(
    'member_order_operations' => [
        'waitPay' => [app\frontend\models\order\member\Pay::class, app\frontend\models\order\member\Close::class],
        'waitSend' => [\app\frontend\models\order\member\ExpressInfo::class],
        'waitReceive' => [\app\frontend\models\order\member\Receive::class],
        'complete' => [\app\frontend\models\order\member\Delete::class],
    ],
    'status' => [
        0 => 'waitPay',
        1 => 'waitSend',
        2 => 'waitReceive',
        3 => 'complete',
        -1 => 'close',
    ]

);