<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/19
 * Time: 下午7:22
 */

namespace app\backend\modules\finance\controllers;


use app\common\components\BaseController;
use app\common\modules\payType\remittance\models\flows\RemittanceAuditFlow;
use app\common\modules\payType\remittance\models\process\RemittanceAuditProcess;
use Illuminate\Database\Eloquent\Builder;


class RemittanceAuditController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        /**
         * @var RemittanceAuditFlow $remittanceAuditFlow
         */
        $remittanceAuditFlow = RemittanceAuditFlow::first();
        $processList = RemittanceAuditProcess::where('flow_id',$remittanceAuditFlow->id)->with(['member','status','remittanceRecord'=> function (Builder $query) {
            $query->with('orderPay');
        }])->get();

        return view('finance.remittance.audits', [
            'remittanceAudits' => json_encode($processList)
        ])->render();

    }
}