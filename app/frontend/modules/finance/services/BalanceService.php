<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/13
 * Time: 下午7:01
 */

namespace app\frontend\modules\finance\services;

use app\backend\modules\member\models\Member;
use app\common\exceptions\AppException;
use app\common\models\finance\Balance;
use \app\common\services\finance\BalanceService as BaseBalanceService;
use app\common\facades\Setting;
use app\frontend\modules\finance\models\BalanceRecharge;

class BalanceService extends BaseBalanceService
{
    private $_recharge_set;

    private $_withdraw_set;

    public function __construct()
    {
        $this->_recharge_set = Setting::get('finance.balance');
        $this->_withdraw_set = Setting::get('withdraw.balance');
    }


    //余额设置接口
    public function getBalanceSet()
    {
        return array(
            'recharge'          => $this->_recharge_set['recharge'],
            'transfer'          => $this->_recharge_set['transfer'],
            'withdraw'          => $this->_withdraw_set['status'],
            'withdrawToWechat'  => $this->_withdraw_set['wechat'],
            'withdrawToAlipay'  => $this->_withdraw_set['alipay']
        );
    }

    //余额充值设置
    public function rechargeSet()
    {
        return $this->_recharge_set['recharge'] ? true : false;
    }

    //余额充值优惠
    public function rechargeSale()
    {
        return $this->_recharge_set['sale'];
    }

    //余额转让设置
    public function transferSet()
    {
        return $this->_recharge_set['transfer'] ? true : false;
    }

    //余额提现设置
    public function withdrawSet()
    {
        return $this->_withdraw_set['status'] ? true : false;
    }

    //余额提现限额设置
    public function withdrawAstrict()
    {
        return $this->_withdraw_set['withdrawmoney'];
    }

    //余额提现手续费
    public function withdrawPoundage()
    {
        return $this->_withdraw_set['poundage'];
    }

    //余额提现到微信
    public function withdrawWechat()
    {
        return $this->_withdraw_set['wechat'] ? true : false;
    }

    //余额提现到支付宝
    public function withdrawAlipay()
    {
        return $this->_withdraw_set['alipay'] ? true : false;
    }

    public function payResult($data = [])
    {
        $rechargeMode = BalanceRecharge::getRechargeRecordBy0rdersn($data['order_sn']);

        $rechargeMode->status = BalanceRecharge::PAY_STATUS_SUCCESS;
        if ($rechargeMode->save) {
            $this->data = array(
                'member_id'         => $rechargeMode->member_id,
                'money'             => $rechargeMode->money,
                'serial_number'     => $rechargeMode->ordersn,
                'operator'          => Balance::OPERRTOR_MEMBER,
                'operator_id'       => $rechargeMode->member_id,
                'remark'            => '会员充值'.$rechargeMode->money . '元，支付单号：' . $data['pay_sn'],
                'service_type'      => Balance::BALANCE_RECHARGE
            );
            $result = $this->balanceChange($data);
            if ($result === true) {
                return true;
            }
            throw new AppException($result);
        }
        throw new AppException('修改充值状态失败');
    }


    //余额变动接口（对外接口）
    public function balanceChange($data)
    {
        $this->data = $data;
        $this->getMemberInfo();
        $this->service_type = $data['service_type'];

        if ($this->service_type == Balance::BALANCE_TRANSFER) {
           return $this->balanceTransfer();
        }

        return  $this->detectionBalance() ? $this->judgeMethod() : '余额必须大于零';
    }

    protected function validatorResultMoney()
    {
        if ($this->result_money >= 0) {
            return true;
        }
        throw new AppException('余额不足');
    }

    protected function getMemberInfo()
    {
        $this->memberModel = Member::getMemberInfoById(\YunShop::app()->getMemberId());
        if ($this->data['member_id']) {
            $this->memberModel = Member::getMemberInfoById($this->data['member_id']);
        }
        if (!$this->memberModel) {
            throw new AppException('未获取到会员信息，请重试！');
        }
    }


    private function detectionBalance()
    {
        return $this->data['money'] > 0 ? true : false;
    }

    private function balanceTransfer()
    {
        $this->attachedMoney();
        $this->type = Balance::TYPE_EXPENDITURE;
        //echo '<pre>'; print_r($this->memberModel); exit;
        //echo '<pre>'; print_r($this->data); exit;
        $result = $this->subtraction();
        if ($result === true) {
            $this->type = Balance::TYPE_INCOME;
            $this->data['member_id'] = $this->data['recipient'];
            $this->getMemberInfo();
            return $this->addition();
        }

        //echo '<pre>'; print_r($result); exit;
        throw new AppException($result);
    }







}
