<?php
return [
    'goods'=>[
        'sale'=>[
            'class'=>'app\backend\modules\goods\models\Sale',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ],
        'notice'=>[
            'class'=>'app\backend\modules\goods\models\Notice',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ],
        'share'=>[
            'class'=>'app\backend\modules\goods\models\Share',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ],
        'privilege'=>[
            'class'=>'app\backend\modules\goods\models\Privilege',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ],
        'discount'=>[
            'class'=>'app\backend\modules\goods\models\Discount',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ],
        'dispatch'=>[
            'class'=>'app\backend\modules\goods\models\GoodsDispatch',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ],
        'coupon'=>[
            'class'=>'app\backend\modules\goods\models\Coupon',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ],
        'div_from'=>[
            'class'=>'app\backend\modules\goods\models\DivFrom',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ],
        'filtering'=>[
            'class'=>'app\backend\modules\goods\models\GoodsFiltering',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ],
        'limitbuy'=>[
            'class'=>'app\backend\modules\goods\models\LimitBuy',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ],
        'invite_page'=>[
            'class' => 'app\backend\modules\goods\models\InvitePage',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ],
        'service'=>[
            'class'=>'app\backend\modules\goods\models\GoodsService',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ],
        'video'=>[
            'class'=>'app\backend\modules\goods\models\GoodsVideo',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
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