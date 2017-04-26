<?php
namespace app\backend\modules\finance\services;

use app\backend\modules\finance\services\BalanceService;
use app\common\services\finance\Withdraw;
use app\common\services\PayFactory;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/31
 * Time: 下午3:13
 */
class WithdrawService extends Withdraw
{
    public static function createStatusService($withdraw)
    {

        switch ($withdraw->status) {
            case -1:
                return '无效';
                break;
            case 0:
                return '未审核';
                break;
            case 1:
                return '未打款';
                break;
            case 2:
                return '已打款';
                break;

        }
    }

    public static function balanceWithdrawPay($withdraw, $remark)
    {
        $data = array(
            'member_id' => $withdraw->member_id,
            'money' => $withdraw->actual_amounts,
            'serial_number' => '',
            'operator' => '-2',
            'operator_id' => $withdraw->id,
            'remark' => $remark,
            'service_type' => \app\common\models\finance\Balance::BALANCE_INCOME,
        );

        return (new BalanceService())->changeBalance($data);
    }

    public static function wechtWithdrawPay($withdraw, $remark)
    {
        //echo '<pre>'; print_r($withdraw); exit;
        return PayFactory::create(1)->doWithdraw($withdraw->member_id, $withdraw->withdraw_sn,
            $withdraw->actual_amounts, $remark);
    }

    public static function alipayWithdrawPay($withdraw, $remark)
    {
        $result = PayFactory::create(2)->doWithdraw($withdraw->member_id, $withdraw->withdraw_sn,
            $withdraw->actual_amounts, $remark);
        echo '<pre>'; print_r($result); exit;
        redirect($result)->send();
    }
}