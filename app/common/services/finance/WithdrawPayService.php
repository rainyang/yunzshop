<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/10 上午11:46
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\finance;


use app\common\services\PayFactory;

class WithdrawPayService
{

    public static function balancePay()
    {
    }


    public static function weChatPay($member_id, $withdraw_sn, $amount, $remark)
    {
        return  PayFactory::create(PayFactory::PAY_WEACHAT)->doWithdraw($member_id, $withdraw_sn, $amount, $remark);
    }


    public static function alipayPay()
    {

    }

}
