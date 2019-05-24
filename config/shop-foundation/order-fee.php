<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/18
 * Time: 5:49 PM
 */
return array(
    [
        'key' => 'goods-fee',
        'class' => function (\app\frontend\modules\order\models\PreOrder $preOrder) {
            return new \app\frontend\modules\order\fee\GoodsFee($preOrder);
        }
    ]
);