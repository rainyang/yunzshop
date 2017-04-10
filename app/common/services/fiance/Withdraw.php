<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/4/7
 * Time: 下午3:08
 */

namespace app\common\services\fiance;

use app\common\models\Income;
use app\common\models\Withdraw as WithdrawModel;
use Yunshop\Commission\models\Agents;

class Withdraw
{

    public static function paySuccess($withdrawId)
    {
        
        $withdraw = WithdrawModel::getWithdrawById($withdrawId)->first();
        if ($withdraw->status !== '1') {
            return false;
        }
        $withdraw = $withdraw->toArray();
        
        $commissionConfigs = \Config::get('income.commission');
        //分销佣金 - 分销商增加已打佣金
        if($withdraw['type'] == $commissionConfigs['class']){
            Agents::addPayCommission($withdraw['member_id'], $withdraw['actual_amounts']);
        }
        
        //修改收入状态
        foreach ($withdraw['type_data']['incomes'] as $item) {
            if($item['pay_status'] === '1'){
                Income::updatedIncomePayStatus($item['id'], ['pay_status'=>'2']);
            }
        }
        //修改提现记录状态
        $updatedData = [
            'status' => 2,
            'arrival_at' => time(),
        ];
        return WithdrawModel::updatedWithdrawStatus($withdrawId, $updatedData);

    }
}