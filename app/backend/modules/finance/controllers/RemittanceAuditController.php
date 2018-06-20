<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/19
 * Time: 下午7:22
 */

namespace app\backend\modules\finance\controllers;


use app\common\components\BaseController;
use app\common\models\Flow;
use app\common\modules\payType\remittance\models\flows\RemittanceAuditFlow;

class RemittanceAuditController extends BaseController
{
    public function index()
    {
        /**
         * @var RemittanceAuditFlow $remittanceAuditFlow
         */
        $remittanceAuditFlow = Flow::where('code',RemittanceAuditFlow::class)->first();
        $processList = $remittanceAuditFlow->process;
        dd($processList);
        exit;

    }
}