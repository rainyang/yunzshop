<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/17
 * Time: 下午5:35
 */

namespace app\backend\modules\finance\services;

use app\backend\modules\member\models\Member;
use app\common\models\finance\Balance;
use \app\common\services\finance\BalanceService as BaseBalanceService;
class BalanceService extends BaseBalanceService
{

    public function changeBalance($data)
    {
        $this->data = $data;
        $this->service_type = $data['service_type'];
        $this->getMemberInfo();

        if ($data['money'] < 0 && $data['operator'] == Balance::OPERATOR_SHOP && $this->service_type == Balance::BALANCE_RECHARGE) {
            $this->attachedMoney();
            $this->type = Balance::TYPE_EXPENDITURE;
            return $this->subtraction();
        }

        return $this->judgeMethod();
    }



    //实现抽象方法，附值会员信息
    protected function getMemberInfo()
    {
        return $this->memberModel = Member::getMemberInfoById(\YunShop::request()->member_id) ?: '未获取到会员数据';
    }


    //
    protected function validatorResultMoney()
    {
        if ($this->result_money >= 0) {
            return true;
        }
        if ($this->result_money < 0 && $this->data['operator'] == Balance::OPERATOR_SHOP && $this->data['service_type'] == Balance::BALANCE_RECHARGE ) {
            $this->result_money = 0;
            return true;
        }
        return false;
    }




}