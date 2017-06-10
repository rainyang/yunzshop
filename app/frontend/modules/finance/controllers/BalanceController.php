<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/2
 * Time: 下午5:37
 */

namespace app\frontend\modules\finance\controllers;



use app\common\facades\Setting;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\PayFactory;
use app\common\components\ApiController;
use app\common\models\finance\Balance as BalanceCommon;

use app\frontend\modules\finance\models\BalanceTransfer;
use app\frontend\modules\finance\models\Withdraw;
use app\frontend\modules\finance\models\BalanceRecharge;
use app\frontend\modules\finance\services\BalanceService;

use app\backend\modules\member\models\Member;

class BalanceController extends ApiController
{
    protected $publicAction = ['alipay'];
    protected $ignoreAction = ['alipay'];

    private $memberInfo;

    private $model;

    private $money;


    //增加余额提现页面接口
    public function poundage()
    {
        if (!$this->getMemberInfo()) {
            return $this->errorJson('未获取到会员信息');
        }

        $balanceSet = new BalanceService();
        $data = [
            'balance'       => $this->memberInfo->credit2 ?: 0,
            'wechat'        => $balanceSet->withdrawWechat(),
            'alipay'        => $balanceSet->withdrawAlipay(),
            'poundage'      => $balanceSet->withdrawPoundage(),
        ];

        return $this->successJson('获取数据成功', $data);
    }

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

            $pay = \Setting::get('shop.pay');
            $result['weixin'] = $pay['weixin'];
            $result['alipay'] = $pay['alipay'];

            return $this->successJson('获取数据成功', $result);
        }
        return $this->errorJson('未获取到会员数据');

    }

    //余额充值+充值优惠 todo 消息通知
    public function recharge()
    {
        $result = (new BalanceService())->rechargeSet() ? $this->rechargeStart() : '未开启余额充值';

        if ($result === true) {
            if (intval(\YunShop::request()->pay_type) == PayFactory::PAY_ALIPAY) {
                return $this->successJson('支付接口对接成功', ['ordersn' => $this->model->order_sn]);
            }
            return  $this->successJson('支付接口对接成功', $this->payOrder());
        }
        //return $result === true ? $this->successJson('支付接口对接成功', $this->payOrder()) : $this->errorJson($result);
        return $this->errorJson($result);
    }

    //余额充值，如果是支付宝支付需要二次请求 alipay 支付接口
    public function alipay()
    {
        $orderSn = \YunShop::request()->order_sn;

        $this->model = BalanceRecharge::getRechargeRecordByOrdersn($orderSn);
        if ($this->model) {
            return  $this->successJson('支付接口对接成功', $this->payOrder());
        }

        return $this->errorJson('充值订单不存在');
    }

    //余额转让
    public function transfer()
    {
        $result = (new BalanceService())->transferSet() ? $this->transferStart() : '未开启余额转让';

        return $result === true ? $this->successJson('转让成功') : $this->errorJson($result);
    }


    //记录【全部、收入、支出】
    public function record()
    {
        $memberInfo = $this->getMemberInfo();
        if ($memberInfo) {
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
        //$member_id = \YunShop::app()->getMemberId() ?: \YunShop::request()->uid;
        return $this->memberInfo = Member::getMemberInfoById($member_id) ?: false;
    }

    //余额转让开始
    private function transferStart()
    {
        $recipient = \YunShop::request()->recipient;
        if (!$this->getMemberInfo()) {
            return '未获取到会员信息';
        }
        if (!Member::getMemberInfoById(\YunShop::request()->recipient)) {
            return '被转让者不存在';
        }
        if ($this->memberInfo->uid == $recipient) {
            return '转让者不能是自己';
        }
        if (\YunShop::request()->transfer_money <= 0){
            return '转让金额必须大于零';
        }
        if ($this->memberInfo->credit2 < \YunShop::request()->transfer_money) {
            return '转让余额不能大于您的余额';
        }
        $this->model = new BalanceTransfer();

        $this->model->fill($this->getTransferData());
        $validator = $this->model->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        if ($this->model->save()) {
            //$result = (new BalanceService())->balanceChange($this->getChangeBalanceDataToTransfer());
            $result = (new BalanceChange())->transfer($this->getChangeBalanceDataToTransfer());
            if ($result === true) {
                $this->model->status = BalanceTransfer::TRANSFER_STATUS_SUCCES;
                if ($this->model->save()) {
                    return true;
                }
            }
            return '修改转让状态失败';
        }
        return '转让写入出错，请联系管理员';
    }

    //提现开始
    private function withdrawStart()
    {
        if (!$this->getMemberInfo()) {
            return '未获取到会员数据,请重试！';
        }
        $result = $this->validatorWithdrawType();
        if ($result === true) {
            $this->model = new Withdraw();

            $this->model->fill($this->getWithdrawData());
            $validator = $this->model->validator();
            if ($validator->fails()) {
                return $validator->messages();
            }

            if ($this->model->save()) {
                //return (new BalanceService())->balanceChange($this->getChangeBalanceDataToWithdraw());
                return (new BalanceChange())->withdrawal($this->getChangeBalanceDataToWithdraw());
            }
            return '提现写入失败，请联系管理员';
        }
        return $result;
    }

    private function validatorWithdrawType()
    {
        if (!$this->getWithdrawType()) {
            return '未找到提现类型';
        }
        if (!(new BalanceService())->withdrawWechat() && $this->getWithdrawType() == 'wechat') {
            return '未开启提现到微信';
        }
        if (!(new BalanceService())->withdrawAlipay() && $this->getWithdrawType() == 'alipay') {
            return '未开启提现到支付宝';
        }
        $withdrawAstrict = (new BalanceService())->withdrawAstrict();
        if ($withdrawAstrict > trim(\YunShop::request()->withdraw_money)) {
            return '提现金额不能小于' . $withdrawAstrict;
        }
        if (!$this->withdrawPoundageMath()) {
            return '扣除手续费后的金额不能小于1元';
        }
        return true;
    }
    //获取提现手续费设置
    private function withdrawSet()
    {
        $withdrawSet = Setting::get('withdraw.balance');
        $this->withdrawPoundage = $withdrawSet['poundage'];
    }

    //余额提现手续费N元
    private function withdrawPoundageMath()
    {
        $withdrawMoney = trim(\YunShop::request()->withdraw_money);
        $withdrawProportion = (new BalanceService())->withdrawPoundage();
        if ($withdrawProportion) {
            $withdrawPoundage = round(floatval($withdrawMoney * $withdrawProportion / 100), 2);
            $result_money = $withdrawMoney - $withdrawPoundage;
            if ($result_money < 1) {
                return false;
            }
        }
        return true;
    }

    //余额转让详细记录数据
    private function getChangeBalanceDataToTransfer()
    {
        return array(
            'member_id'     =>  $this->model->transferor,
            'remark'        => '会员【ID:'.$this->model->transferor.'】余额转让会员【ID：'.$this->model->recipient. '】' . $this->model->money . '元',
            'source'        => ConstService::SOURCE_TRANSFER,
            'relation'      => '',
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $this->model->transferor,
            'change_value'  => $this->model->money,
            'recipient'     => $this->model->recipient,
        );
    }

    private function getChangeBalanceDataToWithdraw()
    {
        return array(
           /* 'serial_number'     => $this->model->withdraw_sn,
            'money'             => $this->model->amounts,
            'remark'            => '会员余额提现' . $this->model->amounts,
            'service_type'      => BalanceCommon::BALANCE_WITHDRAWAL,
            'operator'          => BalanceCommon::OPERATOR_MEMBER,
            'operator_id'       => $this->model->member_id*/

            'member_id'     => \YunShop::app()->getMemberId(),
            'remark'        => '会员余额提现' . $this->model->amounts,
            'source'        => ConstService::SOURCE_WITHDRAWAL,
            'relation'      => $this->model->withdraw_sn,
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $this->model->member_id,
            'change_value'  => $this->model->amounts
        );
    }

    private function getTransferData()
    {
        return array(
            'uniacid'       => \YunShop::app()->uniacid,
            'transferor'    => \YunShop::app()->getMemberId(),
            'recipient'     => \YunShop::request()->recipient,
            'money'         => trim(\YunShop::request()->transfer_money),
            'status'        => BalanceTransfer::TRANSFER_STATUS_ERROR
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
            'pay_way' => $this->getWithdrawType(),    //打款方式
            'status' => '0',                                         //0未审核，1未打款，2已打款， -1无效
            'actual_amounts' => '0',
            'actual_poundage' => '0'
        );
    }

    private function getWithdrawType()
    {
        switch (trim(\YunShop::request()->withdraw_type))
        {
            case 1:
                return 'wechat';
                break;
            case 2:
                return 'alipay';
                break;
            default:
                return '';
        }
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
        \Log::debug('余额充值 result', $result);
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
            'body' => '会员充值金额' . $this->model->money . '元:'. \YunShop::app()->uniacid,
            'amount' => $this->model->money,
            'order_no' => $this->model->ordersn,
            'extra' => ['type' => 2]
        );
    }


}
