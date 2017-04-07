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
class Withdraw
{

    public static function paySuccess($withdrawId)
    {
        $withdraw = WithdrawModel::getWithdrawById($withdrawId)->first();
        if ($withdraw->status !== '1') {
            return false;
        }
        $withdraw = $withdraw->toArray();
        foreach ($withdraw['type_data']['incomes'] as $item) {
            if($item['pay_status'] === '1'){
                Income::updatedIncomePayStatus($item['id'], '2');
            }
        }
        $updatedData = ['status' => 2];
        
        return WithdrawModel::updatedWithdrawStatus($withdrawId, $updatedData);

    }
}