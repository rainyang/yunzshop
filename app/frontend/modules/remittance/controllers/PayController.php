<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/16
 * Time: 上午11:10
 */

namespace app\frontend\modules\remittance\controllers;


use app\common\components\BaseController;
use app\common\models\Process;
use app\frontend\modules\payType\remittance\process\RemittanceProcess;
use app\frontend\modules\payType\remittance\PreRemittanceRecord;
use app\frontend\modules\process\controllers\Operate;

class PayController extends BaseController
{
    use Operate;
    public $transactionActions = ['*'];

    /**
     * @return RemittanceProcess
     * @throws \app\common\exceptions\ShopException
     */
    protected function getProcess()
    {
        $this->validate([
            'process_id'=>'integer'
        ]);
        {
            if (!isset($this->process)) {
                $processId = request()->input('process_id');

                $this->process = RemittanceProcess::find($processId);
            }
            return $this->process;
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index()
    {
        // todo
        // 转账进程
        $transferRecord = new PreRemittanceRecord(['report_url' => 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1529411390405&di=74639b4b003720118befc445ade004cb&imgtype=0&src=http%3A%2F%2Fatt.bbs.duowan.com%2Fforum%2F201411%2F03%2F2200586dku8uouvv8frvvz.jpg']);

        $this->getProcess()->transferRecord()->save($transferRecord);

        $this->toNextState();
        return $this->successJson();
    }
}