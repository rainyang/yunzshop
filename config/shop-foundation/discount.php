<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/29
 * Time: 3:10 PM
 */
return [
    'GoodsMemberLevelDiscountCalculator' => [
        [
            'priority'=>'1000',
            'key' => 'goods',
            'class' => function ($goods,$member) {
                return new \app\common\modules\discount\GoodsMemberLevelDiscountCalculator($goods,$member);
            }
        ],
        [
            'priority'=>'2000',
            'key' => 'shop',
            'class' => function ($goods,$member) {
                return new \app\common\modules\discount\ShopGoodsMemberLevelDiscountCalculator($goods,$member);

            }
        ],
    ]
];