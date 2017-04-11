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
use app\common\models\Withdraw;
use app\common\services\finance\Balance;
use app\common\services\finance\BalanceSet;
use app\common\services\PayFactory;

class BalanceController extends ApiController
{
    /**
     * 会员余额充值接口
     * @return \Illuminate\Http\JsonResponse
     * @Author yitian */
    public function recharge()
    {
        $memberId = \YunShop::app()->getMemberId();
        $rechargeMoney = trim(\YunShop::request()->recharge_money);
        $payType = \YunShop::request()->pay_type;

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
                return $this->successJson('支付接口对接成功',$this->payOrder($data));
            }
            return $this->errorJson($result);
        }
        return $this->errorJson('数据有误，请刷新重试');
    }

    public function withdraw()
    {
        //$test = (new BalanceSet())->getWithdrawSet();
        //echo '<pre>'; print_r($test); exit;
        $memberId = \YunShop::app()->getMemberId();
        $withdrawMoney = trim(\YunShop::request()->withdraw_money);
        $withdrawType = \YunShop::request()->withdraw_type;

        //$memberId = '55';
        //$withdrawMoney = 100;
        //$withdrawType = 1;


        $memberInfo = Member::getMemberInfoById($memberId);
        if (!$memberInfo) {
            return $this->errorJson('会员不存在');
        }
        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $withdrawMoney)|| $withdrawMoney > $memberInfo->credit2) {
            return $this->errorJson('提现金额必须是大于0且小于您的余额，允许两位小数');
        }

        if ($memberId && $withdrawMoney && $withdrawType) {
            $withdrawModel = new Withdraw();
            $withdrawData = array(
                'withdraw_sn'      => '',
                'uniacid'       => \YunShop::app()->uniacid,
                'member_id'     => $memberId,
                'type'          => 'balance',
                'type_id'       => '',
                'type_name'     => '余额提现',
                'amounts'       => $withdrawMoney,      //提现金额
                'poundage'      => '0',                  //提现手续费
                'poundage_rate' => '0',                  //手续费比例
                'pay_way'       => $withdrawType,                  //打款方式
                'status'        => '0',                  //0未审核，1未打款，2已打款， -1无效
                'actual_amounts'=> '0',
                'actual_poundage' => '0'

            );
            $withdrawModel->fill($withdrawData);
            $validator = $withdrawModel->validator();
            if ($validator->fails()) {
                return $this->errorJson($validator->messages());
            } else {
                return $this->successJson('余额提现申请已经提交，等待审核中');
            }
        }
        return $this->errorJson('请正确提交数据');
    }

    /**
     * 会员余额转让接口
     * @return \Illuminate\Http\JsonResponse
     * @Author yitian */
    public function transfer()
    {
// todo 增加消息通知
        $transfer = \YunShop::app()->getMemberId();
        $recipient = \YunShop::request()->recipient;
        $transferMoney = trim(\YunShop::request()->transfer_money);

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

    /**
     * 余额变动明细记录
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetailRecord()
    {
        $memberId = \YunShop::app()->getMemberId();
        $type = \YunShop::request()->type;
        //$memberId = '55';
        if ($memberId) {
            $recordList = \app\common\models\finance\Balance::getMemberDeatilRecord($memberId, $type);
            return $this->successJson('获取记录成功',$this->attachedServiceType($recordList->toArray()));
        }
        return $this->errorJson('未获取到会员ID');
    }

    /**
     * 获取会员充值记录接口
     * @return \Illuminate\Http\JsonResponse
     * Author yitian */
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

    /**
     * 余额转让记录
     * @return \Illuminate\Http\JsonResponse
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

    /**
     * 会员余额被转让记录
     * @return \Illuminate\Http\JsonResponse
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
     * 会员余额充值支付接口
     *
     * @param $data
     * @return array|string|
     * @Author yitian */
    private function payOrder($data)
    {
        $pay = PayFactory::create($data['recharge_type']);

        $result = $pay->doPay($this->payData($data));
        $result['js'] = json_decode($result['js'], 1);

        return $result;
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

    private function attachedServiceType($data = [])
    {
        if ($data) {
            $i = 0;
            foreach ($data as $key) {
                switch ($key['service_type']) {
                    case \app\common\models\finance\Balance::BALANCE_RECHARGE:
                        $data[$i]['service_type'] = "充值";
                        break;
                    case \app\common\models\finance\Balance::BALANCE_CONSUME:
                        $data[$i]['service_type'] = "消费";
                        break;
                    case \app\common\models\finance\Balance::BALANCE_TRANSFER:
                        $data[$i]['service_type'] = "转让";
                        break;
                    case \app\common\models\finance\Balance::BALANCE_DEDUCTION:
                        $data[$i]['service_type'] = "抵扣";
                        break;
                    case \app\common\models\finance\Balance::BALANCE_AWARD:
                        $data[$i]['service_type'] = "奖励";
                        break;
                    case \app\common\models\finance\Balance::BALANCE_WITHDRAWAL:
                        $data[$i]['service_type'] = "余额提现";
                        break;
                    case \app\common\models\finance\Balance::BALANCE_INCOME:
                        $data[$i]['service_type'] = "提现至余额";
                        break;
                    case \app\common\models\finance\Balance::CANCEL_DEDUCTION:
                        $data[$i]['service_type'] = "抵扣取消返回";
                        break;
                    case \app\common\models\finance\Balance::CANCEL_AWARD:
                        $data[$i]['service_type'] = "奖励取消扣除";
                        break;
                    default:
                        $data[$i]['service_type'] = "未知来源";
                }
                $data[$i]['created_at'] = date('Y-m-d H:i:s', $key['created_at']);
                $i++;
            }
        }
        return $data;
    }


}
