<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/14
 * Time: 下午5:31
 */

namespace app\common\modules\payType\remittance\models\flows;

use app\common\modules\audit\flow\models\AuditFlow;

class RemittanceAuditFlow extends AuditFlow
{
    const CODE = 'remittanceAudit';
    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            $query->where('code',self::CODE);
        });
    }
}