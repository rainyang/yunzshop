<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/8 上午9:19
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\finance;


use app\common\models\Withdraw;
use app\common\services\credit\ConstService;

class IncomeFreeAuditService
{
    private $amount;

    public function incomeFreeAudit($withdraw,$payWay)
    {
        $result = false;
        $remark = '提现打款-' . $withdraw->type_name . '-金额:' . $withdraw->actual_amounts . '元,' . '手续费:' . $withdraw->actual_poundage;

        if ($payWay == 'balance') {
            $result = $this->balanceWithdrawPay($withdraw, $remark);
            dd($result);
            \Log::info('MemberId:' . $withdraw->member_id . ', ' . $remark . "打款到余额中!");
        }
        if ($payWay == 'wechat') {

            \Log::info('MemberId:' . $withdraw->member_id . ', ' . $remark . "微信打款中!");
        }

        if ($result) {
            $withdraw->pay_status = 1;
            //event(new AfterIncomeWithdrawPayEvent($withdraw));
            //Withdraw::updatedWithdrawStatus($withdraw->id, ['pay_at' => time()]);
            //WithdrawService::otherWithdrawSuccess($withdraw->id);
            return true;
        }
        return false;
    }


    private function balanceWithdrawPay($withdraw,$remark)
    {
        $data = array(
            'member_id'     => $withdraw->member_id,
            'remark'        => $remark,
            'source'        => ConstService::SOURCE_INCOME,
            'relation'      => '',
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $withdraw->id,
            'change_value'  => $withdraw->actual_amounts
        );
        return (new BalanceChange())->income($data);
    }

    private function wechatWithdrawPay()
    {

    }




}
