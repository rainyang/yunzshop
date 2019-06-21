<?php
return [
    'dealPrice' => [
        [
            'key' => 'marketPrice',
            'class' => function (\app\common\models\Goods $goods, $param = []) {
                return new \app\frontend\modules\order\discount\SingleEnoughReduce($preOrder);
            },
        ]
    ]
];