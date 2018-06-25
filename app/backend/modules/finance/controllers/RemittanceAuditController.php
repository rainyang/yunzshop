<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/19
 * Time: 下午7:22
 */

namespace app\backend\modules\finance\controllers;


use app\common\components\BaseController;
use app\common\models\Process;
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
        $searchParams = request()->input('searchParams');
        $remittanceAuditFlow = RemittanceAuditFlow::first();
        $processBuilder = RemittanceAuditProcess::where('flow_id', $remittanceAuditFlow->id)->with(['member', 'status', 'remittanceRecord' => function (Builder $query) {
            $query->with('orderPay');
        }]);
        if(isset($searchParams['state'])){
            $processBuilder->where('state');
        }
        $processList = $processBuilder->get();
        $allState = (new Process())->all_state;
        $data = [
            'remittanceAudits' => $processList,
            'allState' => $allState,
            'searchParams' => $searchParams,
        ];
        return view('finance.remittance.audits', ['data' => json_encode($data)])->render();

    }
}