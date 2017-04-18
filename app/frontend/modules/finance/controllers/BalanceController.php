<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/2
 * Time: 下午5:37
 */

namespace app\frontend\modules\finance\controllers;



use app\common\services\finance\Balance;
use app\common\services\PayFactory;
use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\finance\Balance as BalanceCommon;

use app\frontend\modules\finance\models\Withdraw;
use app\frontend\modules\finance\models\BalanceRecharge;
use app\frontend\modules\finance\services\BalanceService;

use app\backend\modules\member\models\Member;

class BalanceController extends ApiController
{
    public function test()
    {
        $data = array('order_sn' => 'RV20170418180852899391');
        $test = (new BalanceService())->payResult($data);
        echo '<pre>'; print_r($test); exit;
    }

    private $memberInfo;

    private $model;

    private $money;


    //转让（是否做独立文件）

    //提现+提现限制+提现手续费
    public function withdraw()
    {
        $result = (new BalanceService())->withdrawSet() ? $this->withdrawStart() : '未开启余额提现';

        return $result === true ? $this->successJson('提现申请提交成功') : $this->errorJson($result);
    }


    //余额设置+会员余额值
    public function balance()
    {
        $memberInfo = $this->getMemberInfo();
        if ($memberInfo) {
            $result = (new BalanceService())->getBalanceSet();
            $result['member_credit2'] = $memberInfo->credit2;

            return $this->successJson('获取数据成功', $result);
        }
        return $this->errorJson('未获取到会员数据');

    }

    //余额充值+充值优惠 todo 充值支付回调 完成余额赠送事件 消息通知
    public function recharge()
    {
        $result = (new BalanceService())->rechargeSet() ? $this->rechargeStart() : '未开启余额充值';

        return $result === true ? $this->successJson('支付接口对接成功', $this->payOrder()) : $this->errorJson($result);
    }

    //记录【全部、收入、支出】
    public function record()
    {
        if ($this->getMemberInfo()) {
            $type = \YunShop::request()->record_type;
            $recordList = BalanceCommon::getMemberDetailRecord($this->memberInfo->uid, $type);

            return $this->successJson('获取记录成功', $recordList->toArray());
        }
        return $this->errorJson('未获取到会员信息');
    }

    //获取会员信息
    private function getMemberInfo()
    {
        $member_id = \YunShop::app()->getMemberId();
        $member_id = \YunShop::app()->getMemberId() ?: \YunShop::request()->member_id;
        return $this->memberInfo = Member::getMemberInfoById($member_id) ?: false;
    }

    //提现开始
    private function withdrawStart()
    {
        if (!$this->getMemberInfo()) {
            return '未获取到会员数据,请重试！';
        }

        $this->model = new Withdraw();

        $this->model->fill($this->getWithdrawData());
        $validator = $this->model->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }

        if ($this->model->save()) {
            //调取余额修改接口
            return $this->balanceChange();
        }
        return '提现写入失败，请联系管理员';
    }

    private function balanceChange()
    {
        //todo 可以增加余额提现申请通知
        //todo 修改会员余额，增加余额明细记录
        //echo '<pre>'; print_r($this->getChangeBalanceData()); exit;
        return (new BalanceService())->balanceChange($this->getChangeBalanceData());
    }

    private function getChangeBalanceData()
    {
        return array(
            'serial_number' => $this->model->withdraw_sn,
            'money' => $this->model->amounts,
            'remark' => '会员余额提现' . $this->model->amounts,
            'service_type' => BalanceCommon::BALANCE_WITHDRAWAL,
            'operator' => BalanceCommon::OPERRTOR_MEMBER,
            'operator_id' => $this->model->member_id
        );
    }


    //余额提现记录 data 数据
    private function getWithdrawData()
    {
        return array(
            'withdraw_sn' => $this->getWithdrawOrderSN(),
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $this->memberInfo->uid,
            'type' => 'balance',
            'type_id' => '',
            'type_name' => '余额提现',
            'amounts' => \YunShop::request()->withdraw_money,         //提现金额
            'poundage' => '0',                                         //提现手续费
            'poundage_rate' => '0',                                         //手续费比例
            'pay_way' => trim(\YunShop::request()->withdraw_type),    //打款方式
            'status' => '0',                                         //0未审核，1未打款，2已打款， -1无效
            'actual_amounts' => '0',
            'actual_poundage' => '0'
        );
    }

    //生成充值订单号
    private function getWithdrawOrderSN()
    {
        $withdraw_sn = createNo('WS', true);
        while (1) {
            if (!Withdraw::validatorOrderSn($withdraw_sn)) {
                break;
            }
            $withdraw_sn = createNo('WS', true);
        }
        return $withdraw_sn;
    }

    //充值开始
    private function rechargeStart()
    {
        if (!$this->getMemberInfo()) {
            return '未获取到会员数据,请重试！';
        }
        $this->model = new BalanceRecharge();

        $this->model->fill($this->getRechargeData());
        $validator = $this->model->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        if ($this->model->save()) {
            return true;
        }
        return '充值写入失败，请联系管理员';
    }

    //充值记录表data数据
    private function getRechargeData()
    {
        //$change_money = substr(\YunShop::request()->recharge_money, 0, strpos(\YunShop::request()->recharge_money, '.')+3);
        $change_money = \YunShop::request()->recharge_money;
        return array(
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $this->memberInfo->uid,
            'old_money' => $this->memberInfo->credit2 ?: 0,
            'money' => floatval($change_money),
            'new_money' => $change_money + $this->memberInfo->credit2,
            'ordersn' => $this->getRechargeOrderSN(),
            'type' => intval(\YunShop::request()->pay_type),
            'status' => BalanceRecharge::PAY_STATUS_ERROR
        );
    }

    //生成充值订单号
    private function getRechargeOrderSN()
    {
        $ordersn = createNo('RV', true);
        while (1) {
            if (!BalanceRecharge::validatorOrderSn($ordersn)) {
                break;
            }
            $ordersn = createNo('RV', true);
        }
        return $ordersn;
    }

    /**
     * 会员余额充值支付接口
     *
     * @param $data
     * @return array|string|
     * @Author yitian
     */
    private function payOrder()
    {
        $pay = PayFactory::create($this->model->type);

        $result = $pay->doPay($this->payData());
        if ($this->model->type == 1) {
            $result['js'] = json_decode($result['js'], 1);
        }
        return $result;
    }

    /**
     * 支付请求数据
     *
     * @return array
     * @Author yitian
     */
    private function payData()
    {
        return array(
            'subject' => '会员充值',
            'body' => '会员充值金额' . $this->model->money . '元',
            'amount' => $this->model->money,
            'order_no' => $this->model->ordersn,
            'extra' => ['type' => 1]
        );
    }

















    /**
     * 会员余额转让接口
     * @return \Illuminate\Http\JsonResponse
     * @Author yitian
     */
    public function transfer()
    {
// todo 增加消息通知
        $financeSet = Setting::get('finance.balance');
        if ($financeSet['transfer'] == 1) {
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
                    'member_id' => $transfer,
                    'change_money' => $transferMoney,
                    'operator' => BalanceRecharge::PAY_TYPE_MEMBER,
                    'operator_id' => $transfer,
                    'remark' => '会员ID' . $transfer . '转让会员ID' . $recipient . '余额' . $transferMoney . '元',
                    'service_type' => \app\common\models\finance\Balance::BALANCE_TRANSFER,

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
        return $this->errorJson('未开启余额转让功能');

    }
}
