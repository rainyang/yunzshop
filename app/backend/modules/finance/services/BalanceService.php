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
        echo '<pre>'; print_r($data); exit;
        $this->data = $data;
        $this->service_type = $data['service_type'];
        $this->getMemberInfo();

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
        if ($this->result_money < 0 && $this->type == Balance::TYPE_EXPENDITURE && $this->data['operator'] == Balance::OPERATOR_SHOP && $this->data['service_type'] == Balance::BALANCE_RECHARGE ) {
            $this->result_money = 0;
            return true;
        }
        return false;
    }

    private function judgeMethod()
    {

        $this->attachedType();

        if ($this->type == Balance::TYPE_INCOME && in_array($this->service_type,
                [
                    Balance::BALANCE_RECHARGE,
                    Balance::BALANCE_TRANSFER,
                    Balance::BALANCE_AWARD,
                    Balance::BALANCE_INCOME,
                    Balance::BALANCE_CANCEL_DEDUCTION
                ])
        ) {
            $this->money = $this->data['money'];
            return $this->addition();
        }
        if ($this->type == Balance::TYPE_EXPENDITURE && in_array($this->service_type,
                [
                    Balance::BALANCE_CONSUME,
                    Balance::BALANCE_DEDUCTION,
                    Balance::BALANCE_WITHDRAWAL,
                    Balance::BALANCE_CANCEL_AWARD
                ])
        ) {
            $this->money = $this->data['money'];
            return $this->subtraction();
        }
        //后台充值可以充值负数
        if ($this->type == Balance::TYPE_EXPENDITURE && $this->data['operator'] == Balance::OPERATOR_SHOP
            && $this->data['service_type'] == Balance::BALANCE_RECHARGE ) {
            $this->money = -$this->data['money'];
            return $this->subtraction();
        }
        return '服务类型选择错误';

    }



}