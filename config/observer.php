<?php
return [
    'goods'=>[
        'sale'=>[
            'class'=>'app\backend\modules\goods\models\Sale',
            //'function_validator'=>'relationSave',
            'function_save'=>'relationSave'
        ],
        'notice'=>[
            'class'=>'app\backend\modules\goods\models\Notice',
            'function'=>'relationSave'
        ],
        'share'=>[
            'class'=>'app\backend\modules\goods\models\Share',
            'function'=>'relationSave'
        ],
        'privilege'=>[
            'class'=>'app\backend\modules\goods\models\Privilege',
            'function'=>'relationSave'
        ],
        'discount'=>[
            'class'=>'app\backend\modules\goods\models\Discount',
            'function'=>'relationSave'
        ],
        'dispatch'=>[
            'class'=>'app\backend\modules\goods\models\GoodsDispatch',
            'function'=>'relationSave'
        ],
    ],
    'order' => [
        //订单操作记录
        'order_operation_log' => [
            'class'         => 'app\backend\modules\order\models\OrderOperationLog',
            'function_save' => 'insertOperationLog'
        ]
    ]
];