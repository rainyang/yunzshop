<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/11
 * Time: 上午11:56
 */

namespace app\common\services\finance;


use app\common\facades\Setting;
use app\common\traits\ValidatorTrait;
use Hamcrest\Core\Set;

class BalanceSet
{
    use ValidatorTrait;

    const BALANCE_WITHDRAW_OPEN = 1;    //开启余额提现

    const BALANCE_WITHDRAW_CLOSE = 0;   //关闭余额提现





    public function rules()
    {
        return [
            'poundage'      => 'regex:/^[\d]?(\.[\d]{0,2})?$/',
            'withdrawmoney' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/'
        ];
    }

    public  function atributeNames() {
        return [
            'poundage'=> "请输入正确的提现手续费",
            'withdrawmoney' => "请输入正确的提现限制金额"
        ];
    }

}
