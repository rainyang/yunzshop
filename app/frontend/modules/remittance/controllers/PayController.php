<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/16
 * Time: 上午11:10
 */

namespace app\frontend\modules\remittance\controllers;


use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\modules\payType\remittance\models\flows\RemittanceFlow;
use app\frontend\modules\payType\remittance\process\RemittanceProcess;
use app\frontend\modules\process\controllers\Operate;

class PayController extends BaseController
{
    use Operate;
    public $transactionActions = ['*'];
    /**
     * @var RemittanceProcess
     */
    protected $process;
    protected $name = '确认支付';

    /**
     * @return RemittanceProcess
     * @throws AppException
     */
    protected function _getProcess()
    {
        $processId = request()->input('process_id');
        $process = RemittanceProcess::find($processId);
        if (!isset($process)) {
            throw new AppException("未找到该流程(id:{$processId})");
        }
        return $process;
    }

    protected function beforeStates()
    {
        return [RemittanceFlow::STATE_WAIT_REMITTANCE];
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index()
    {
        $this->toNextState();
        return $this->successJson();
    }
}