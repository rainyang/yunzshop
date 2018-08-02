<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/2
 * Time: 上午11:23
 */

use app\frontend\models\order\member\Close;
use app\frontend\models\order\member\Pay;

return array(
    'member_order_operations' => [
        'waitPay' => [Pay::class, Close::class]
    ]
);