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

        //$memberId = 55;
        //$rechargeMoney = 100;
        //$payType = 2;

        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $rechargeMoney)) {
            return $this->errorJson('请输入有效的充值金额，允许两位小数');
        }
        if ($memberId && $rechargeMoney && $payType) {
            $data = array(
                'member_id'     => $memberId,
                'change_money'  => $rechargeMoney,
                'serial_number' => '',
                'operator'      => BalanceRecharge::PAY_TYPE_MEMBER,
                'operator_id'   => $memberId,
                'remark'        => '会员充值余额'. $rechargeMoney. '元',
                'service_type'  => \app\common\models\finance\Balance::BALANCE_RECHARGE,
                'recharge_type' => $payType,
            );

            $result = (new Balance())->changeBalance($data);
            if (is_numeric($result)) {
                $rechargeModel = BalanceRecharge::getRechargeRecordByid($result);
                $data['serial_number'] = $rechargeModel->ordersn;
                //支付返回数据直接反给前端
                //return $this->payOrder($data);
                //echo '<pre>'; print_r($this->payData($data)); exit;
                return $this->successJson('支付接口对接成功',$this->payOrder($data));
            }
            return $this->errorJson($result);
        }
        return $this->errorJson('数据有误，请刷新重试');
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

    /**
     * 调取支付接口
     * @return array|mixed|string|void
     * @Author yitian */
    private function payOrder($data)
    {
        $pay = PayFactory::create($data['recharge_type']);

        return $pay->doPay($this->payData($data));
    }

    /**
     * 支付请求数据
     *
     * @return array
     * @Author yitian */
    private function payData($data)
    {
        return array(
            'subject'   => '会员充值',
            'body'      => '会员充值金额'. $data['change_money']. '元',
            'amount'    => $data['change_money'],
            'order_no'  => $data['serial_number'],
            'extra'     => ['type'=> 1]
        );
    }



}