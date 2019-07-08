<?php
return [
    'dealPrice' => [
        [
            'key' => 'goodsDealPrice',
            'class' => function (\app\common\models\GoodsOption $goodsOption, $param = []) {
                return new \app\common\modules\goodsOption\dealPrice\GoodsDealPrice($goodsOption);
            },
        ], [
            'key' => 'marketDealPrice',
            'class' => function (\app\common\models\GoodsOption $goodsOption, $param = []) {
                return new \app\common\modules\goodsOption\dealPrice\MarketDealPrice($goodsOption);
            },
        ]
    ]
];