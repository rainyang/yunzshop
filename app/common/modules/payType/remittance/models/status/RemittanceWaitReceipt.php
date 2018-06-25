<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午9:50
 */

namespace app\common\modules\payType\remittance\models\status;

use app\common\modules\payType\remittance\models\flows\RemittanceAuditFlow;
use app\common\modules\status\StatusObserver;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\payType\remittance\PreRemittanceRecord;
use app\frontend\modules\payType\remittance\process\RemittanceProcess;

class RemittanceWaitReceipt extends StatusObserver
{


    /**
     * @throws \Exception
     * @throws \app\common\exceptions\AppException
     */
    public function onCreated()
    {

        /**
         * @var RemittanceProcess $process
         */
        $process = RemittanceProcess::find($this->status->model_id);
        // todo 从参数中获取  验证参数是否存在
        $transferRecord = new PreRemittanceRecord(
            [
                'report_url' => 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1529411390405&di=74639b4b003720118befc445ade004cb&imgtype=0&src=http%3A%2F%2Fatt.bbs.duowan.com%2Fforum%2F201411%2F03%2F2200586dku8uouvv8frvvz.jpg',
                'note' => '汇款号:112333211,姓名:沈阳',
                'uid' => MemberService::getCurrentMemberModel()->uid,
                'order_pay_id' => $process->model_id
            ]
        );
        $transferRecord->save();

        $transferRecord->addProcess(RemittanceAuditFlow::first());

    }

}