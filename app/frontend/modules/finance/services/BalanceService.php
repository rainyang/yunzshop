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
    protected function transferSet()
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


    //余额变动接口（对外接口）
    public function balanceChange($data)
    {
        $this->data = $data;
        $this->getMemberInfo();

        return $this->updateBalanceRecord();
    }

    protected function getNewMoney()
    {
        $this->attachedType();
        switch ($this->type)
        {
            case Balance::TYPE_INCOME:
                $new_money = $this->data['money'] + $this->memberModel->credit2;
                break;
            case Balance::TYPE_EXPENDITURE:
                $new_money = $this->memberModel->credit2 - $this->data['money'];
                break;
            default:
                $new_money = 0;
        }
        if ($new_money < 0) {
            throw new AppException('会员余额不足！');
        }
        return $new_money;
    }

    protected function getMemberInfo()
    {
        $this->memberModel = Member::getMemberInfoById(\YunShop::app()->getMemberId());
        if (!$this->memberModel) {
            throw new AppException('未获取到会员信息，请重试！');
        }
    }

    protected function attachedType()
    {
        if (in_array($this->data['service_type'], [
            Balance::BALANCE_RECHARGE,
            Balance::BALANCE_TRANSFER,
            Balance::BALANCE_AWARD,
            Balance::BALANCE_INCOME,
            Balance::BALANCE_CANCEL_DEDUCTION
        ])) {
            $this->type = Balance::TYPE_INCOME;
            $this->service_type = $this->data['service_type'];
        } elseif (in_array($this->data['service_type'], [
            Balance::BALANCE_CONSUME,
            Balance::BALANCE_DEDUCTION,
            Balance::BALANCE_WITHDRAWAL,
            Balance::BALANCE_CANCEL_AWARD
        ])) {
            $this->type = Balance::TYPE_EXPENDITURE;
            $this->service_type = $this->data['service_type'];
        } else {
            throw new AppException('服务类型不存在');
        }
    }


}
