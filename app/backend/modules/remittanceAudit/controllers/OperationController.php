<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/22
 * Time: 上午11:22
 */

namespace app\backend\modules\remittanceAudit\controllers;

use app\backend\modules\process\controllers\Operate;
use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\modules\payType\remittance\models\process\RemittanceAuditProcess;

class OperationController extends BaseController
{
    use Operate;
    public $transactionActions = ['*'];
    protected $process;

    protected function getProcess()
    {
        if (!isset($this->process)) {
            $processId = request()->input('process_id');

            $this->process = RemittanceAuditProcess::find($processId);
            if (!isset($this->process)) {
                throw new AppException("我找到id为{$processId}的审核进程记录");
            }
        }
        return $this->process;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function pass()
    {
        $this->toNextState();
        return $this->successJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function reject()
    {
        $this->toClosedState();
        return $this->successJson();

    }
}