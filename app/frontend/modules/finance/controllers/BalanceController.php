<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/2
 * Time: 下午5:37
 */

namespace app\frontend\modules\finance\controllers;


use app\backend\modules\member\models\Member;
use app\common\components\ApiController;
use app\common\models\finance\BalanceRecharge;
use app\common\models\finance\BalanceTransfer;
use app\common\services\fiance\Balance;
use app\common\services\PayFactory;

class BalanceController extends ApiController
{
    /*
     * 充值接口
     *
     * */
    public function recharge()
    {
        $memberId = \YunShop::app()->getMemberId();
        $rechargeMoney = trim(\YunShop::request()->recharge_money);
        $payType = \YunShop::request()->pay_type;


        $rechargeMoney = 100;
        $memberId = 55;

        $balanceModel = new Balance();
        $resultId = $balanceModel->memberBalanceRecharge($memberId, $rechargeMoney);
        if (is_numeric($resultId)) {
            //todo 调取支付接口
            $pay = PayFactory::create($payType);

            $result = $pay->doPay([]);

            //$result 返给前端
            // todo 监听支付后 回掉




            if ($balanceModel->updateBalance($memberId, $rechargeMoney) === true) {
                $result = $balanceModel->updateRecordStatus($resultId, 1);
            }
            return $this->errorJson('更新会员余额失败');
        }

        return $this->errorJson($resultId ?: '数据有误');
    }
    /*
     * 转让接口
     *
     * @Author yitian */
    public function transfer()
    {
// todo 增加消息通知
        $transfer = \YunShop::app()->getMemberId();
        $recipient = \YunShop::request()->recipient;
        $transferMoney = trim(\YunShop::request()->transfer_money);

        //$transferMoney = '1.11';
        //$transfer = 55;
        //$recipient = 57;

        $recipientModel = Member::getMemberById($recipient);
        $transferModel = Member::getMemberById($transfer);
        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $transferMoney) || $transferModel->credit2 < $transferMoney) {
            return $this->errorJson('转让金额必须是大于0且大于您的余额，允许两位小数');
        }
        if ($transfer == $recipient) {
            return $this->errorJson('受让人不可以是您自己');
        }
        if (!$recipientModel || !$recipientModel) {
            return $this->errorJson('未获取到被转让者ID或被转让者不存在');
        }
        if ($transferMoney && $transfer && $recipient) {
            $data = array(
                'member_id'     => $transfer,
                'change_money'  => $transferMoney,
                'operator'      => BalanceRecharge::PAY_TYPE_MEMBER,
                'operator_id'   => $transfer,
                'remark'        => '会员ID' . $transfer . '转让会员ID' . $recipient . '余额'. $transferMoney .'元',
                'service_type'  => \app\common\models\finance\Balance::BALANCE_TRANSFER,

                'recipient_id' => $recipient
            );

            $result = (new Balance())->changeBalance($data);
            if ($result == true) {
                return $this->successJson('余额转让成功');
            }
            return $this->errorJson($result);
        }
        return $this->errorJson('请求数据错误，未进行余额转让操作');
    }

    /*
     * 会员充值记录
     *
     * @Author yitian */
    public function rechargeRecord()
    {
        $memberId = \YunShop::app()->getMemberId();
        //$memberId= '55';
        if ($memberId) {
            $rechargeRecord = BalanceRecharge::getMemberRechargeRecord($memberId);
            return $this->successJson('充值记录获取成功', $rechargeRecord);
        }
        return $this->errorJson('未获取到会员ID');
    }

    /*
     * 会员余额转让记录
     *
     * @Author yitian */
    public function transferRecord()
    {
        $memberId = \YunShop::app()->getMemberId();
        //$memberId= '55';
        if ($memberId) {
            $transferRecord = BalanceTransfer::getMemberTransferRecord($memberId);
            return $this->successJson('转让记录获取成功', $transferRecord);
        }
        return $this->errorJson('未获取到会员ID');
    }

    /*
     * 会员余额被转让记录
     *
     * @Author yitian */
    public function recipientRecord()
    {
        $memberId = \YunShop::app()->getMemberId();
        //$memberId= '55';
        if ($memberId) {
            $recipientRecord = BalanceTransfer::getMemberRecipientRecord($memberId);
            return $this->successJson('被转让记录获取成功', $recipientRecord);
        }
        return $this->errorJson('未获取到会员ID');
    }

}