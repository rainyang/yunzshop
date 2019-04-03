<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/9/7
 * Time: 上午10:33
 */
return [
//    'remittance'=>\app\common\modules\payType\remittance\models\status\RemittanceStatus::class,
//    'remittanceAudit'=>\app\common\modules\payType\remittance\models\status\RemittanceAuditStatus::class,
    [
        'key' => 'remittance',
        'class' => \app\common\modules\payType\remittance\models\status\RemittanceStatus::class,
    ],
    [
        'key' => 'remittanceAudit',
        'class' => \app\common\modules\payType\remittance\models\status\RemittanceAuditStatus::class,
    ],
];