<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/13
 * Time: 下午7:01
 */

namespace app\frontend\modules\finance\services;

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
        echo '<pre>'; print_r($this->data); exit;

        return $this->test();

    }


}
