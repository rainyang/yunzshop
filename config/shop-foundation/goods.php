<?php
return [
    'dealPrice' => [
        [
            'key' => 'goodsDealPrice',
            'class' => function (\app\common\models\Goods $goods, $param = []) {
                return new \app\common\modules\goods\dealPrice\GoodsDealPrice($goods);
            },
        ], [
            'key' => 'marketDealPrice',
            'class' => function (\app\common\models\Goods $goods, $param = []) {
                return new \app\common\modules\goods\dealPrice\MarketDealPrice($goods);
            },
        ]
    ]
];