<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/13
 * Time: 上午11:54
 */

namespace app\common\services\finance;


use app\common\facades\Setting;
use app\common\models\finance\Balance;

class BalanceService
{


    public static function attachedTypeName($model)
    {
        switch ($model->type)
        {
            case \app\common\services\finance\Balance::INCOME:
                return '收入';
                break;
            case \app\common\services\finance\Balance::EXPENDITURE:
                return '支出';
                break;
            default:
                return '';
        }

    }

    public static function attachedServiceTypeName($model)
    {
        switch ($model->service_type)
        {
            case Balance::BALANCE_RECHARGE:
                return '充值';
                break;
            case Balance::BALANCE_CONSUME:
                return '消费';
                break;
            case Balance::BALANCE_TRANSFER:
                return '转让';
                break;
            case Balance::BALANCE_DEDUCTION:
                return '抵扣';
                break;
            case Balance::BALANCE_AWARD:
                return '充值';
                break;
            case Balance::BALANCE_WITHDRAWAL:
                return '余额提现';
                break;
            case Balance::BALANCE_INCOME:
                return '提现至余额';
                break;
            case Balance::CANCEL_DEDUCTION:
                return '抵扣取消余额回滚';
                break;
            case Balance::CANCEL_AWARD:
                return '奖励取消回滚';
                break;
            default:
                return '未知';
        }
    }


}