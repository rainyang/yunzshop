<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/13
 * Time: 下午7:01
 */

namespace app\frontend\modules\finance\services;

use app\common\exceptions\AppException;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\facades\Setting;
use app\frontend\modules\finance\models\BalanceRecharge;

class BalanceService
{
    private $_recharge_set;

    private $_withdraw_set;

    private $data;

    public function __construct()
    {
        $this->_recharge_set = Setting::get('finance.balance');
        $this->_withdraw_set = Setting::get('withdraw.balance');
    }


    //余额设置接口
    public function getBalanceSet()
    {
        return array(
            'recharge'          => $this->_recharge_set['recharge'] ? 1 : 0,
            'transfer'          => $this->_recharge_set['transfer'] ? 1 : 0,
            'withdraw'          => $this->_withdraw_set['status'] ? 1 : 0,
            'withdrawToWechat'  => $this->_withdraw_set['wechat'] ? true : false,
            'withdrawToAlipay'  => $this->_withdraw_set['alipay'] ? true : false
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
        return $this->rechargeSet() ? $this->_recharge_set['sale'] : [];
    }

    //0赠送固定金额，1赠送充值比例
    
    public function proportionStatus()
    {
        return isset($this->_recharge_set['proportion_status']) ? $this->_recharge_set['proportion_status'] : '0';
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
        return $this->_withdraw_set['poundage'] ?: 0;
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

    /**
     * 余额充值回调，支付成功回调方法
     *
     *
     * @param array $data
     * @return bool|string
     * @throws AppException
     */
    public function payResult($data = [])
    {
        $rechargeMode = BalanceRecharge::getRechargeRecordByOrdersn($data['order_sn']);
        if (!$rechargeMode) {
            throw new AppException('充值失败');
        }
        $rechargeMode->status = BalanceRecharge::PAY_STATUS_SUCCESS;
        if ($rechargeMode->save()) {
            $this->data = array(
                'member_id'     => $rechargeMode->member_id,
                'remark'        => '会员充值'.$rechargeMode->money . '元，支付单号：' . $data['pay_sn'],
                'source'        => ConstService::SOURCE_RECHARGE,
                'relation'      => $rechargeMode->ordersn,
                'operator'      => ConstService::OPERATOR_MEMBER,
                'operator_id'   => $rechargeMode->member_id,
                'change_value'  => $rechargeMode->money,
            );
            $result = (new BalanceChange())->recharge($this->data);
            if ($result === true) {
                return $this->rechargeSaleMath();
            }
            throw new AppException('更新会员余额失败');
        }
        throw new AppException('修改充值状态失败');
    }

    /**
     * 余额满额送计算，充值赠送
     *
     * @return bool|string
     */
    private function rechargeSaleMath()
    {
        $sale = $this->rechargeSale();
        $sale = array_values(array_sort($sale, function ($value) {
            return $value['enough'];
        }));
        rsort($sale);
        $result = '';

        foreach ($sale as $key) {
            if (empty($key['enough']) || empty($key['give'])) {
                continue;
            }
            if (bccomp($this->data['change_value'],$key['enough'],2) != -1) {
                if ($this->proportionStatus()) {
                    $result = bcdiv(bcmul($this->data['change_value'],$key['give'],2),100,2);
                } else {
                    //$result = round(floatval($key['give']), 2);
                    $result = bcmul($key['give'],1,2);
                }
                $enough = floatval($key['enough']);
                $give = $key['give'];
                break;
            }
        }
        if ($result) {
            $result = array(
                'member_id' => $this->data['member_id'],
                'remark' => '充值满' . $enough . '赠送' . $give . '(充值金额:' . $this->data['change_value'] . '元)',
                'source' => ConstService::SOURCE_AWARD,
                'relation' => $this->data['source'],
                'operator' => ConstService::OPERATOR_MEMBER,
                'operator_id' => $this->data['member_id'],
                //todo 验证余额值
                'change_value' => $result,
            );
            return (new BalanceChange())->award($result);
        }
        return true;
    }

}
