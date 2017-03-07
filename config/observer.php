<?php
return [
    'goods'=>[
        /*'sale'=>[
            'class'=>'app\backend\modules\goods\models\Sale',
            'function'=>'relationSave'
        ],
        'notice'=>[
            'class'=>'app\backend\modules\goods\models\Notice',
            'function'=>'relationSave'
        ],*/
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
            'class'=>'app\backend\modules\goods\models\Dispatch',
            'function'=>'relationSave'
        ],


    ]
];