<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/13
 * Time: 上午11:54
 */

namespace app\common\services\finance;



use app\backend\modules\member\models\Member;
use app\common\exceptions\AppException;
use app\common\models\finance\Balance;

abstract class BalanceService
{
    protected $memberModel;

    protected $balanceModel;


    protected $data;

    protected $type;

    protected $service_type;

    protected $money;   //正数

    protected $result_money;




    //计算后金额值
    abstract protected function validatorResultMoney();

    //获取会员信息
    abstract protected function getMemberInfo();

    protected function judgeMethod()
    {

        $this->attachedMoney();

        if (in_array($this->service_type,
            [
                Balance::BALANCE_RECHARGE,
                Balance::BALANCE_TRANSFER,
                Balance::BALANCE_AWARD,
                Balance::BALANCE_INCOME,
                Balance::BALANCE_CANCEL_DEDUCTION
            ])
        ) {
            $this->type = Balance::TYPE_INCOME;
            return $this->addition();
        }
        if (in_array($this->service_type,
            [
                Balance::BALANCE_CONSUME,
                Balance::BALANCE_DEDUCTION,
                Balance::BALANCE_WITHDRAWAL,
                Balance::BALANCE_CANCEL_AWARD
            ])
        ) {
            $this->type = Balance::TYPE_EXPENDITURE;
            return $this->subtraction();
        }

        return '服务类型不存在';

    }

    protected function attachedMoney()
    {
        return $this->money = $this->data['money'] > 0 ? $this->data['money'] : -$this->data['money'];
    }

    //余额+
    protected function addition()
    {
        $this->result_money = $this->memberModel->credit2 + $this->money;
        return $this->updateBalanceRecord();
    }

    //余额—
    protected function subtraction()
    {
        $this->result_money = $this->memberModel->credit2 - $this->money;
        return $this->validatorResultMoney() === true ? $this->updateBalanceRecord() : '余额不足';
    }

    //余额明细记录写入 protected
    protected function updateBalanceRecord()
    {
        $this->balanceModel = new Balance();

        $this->balanceModel->fill($this->getRecordData());
        $validator = $this->balanceModel->validator();
        if ($validator->fails()) {
            throw new AppException($validator->messages());
        }
        if ($this->balanceModel->save()) {
            return $this->updateMemberBalance();
        }
        return '余额变动记录写入失败，请联系管理员！';
    }

    //修改会员余额
    protected function updateMemberBalance()
    {
        //echo '<pre>'; print_r($this->result_money); exit;
        $this->memberModel->credit2 = $this->result_money;
        if ($this->memberModel->save()) {
            return true;
        }
        return '会员余额写入出错，请联系管理员';
    }




    protected function getRecordData()
    {
        return array(
            'uniacid'       => \YunShop::app()->uniacid,
            'member_id'     => $this->memberModel->uid,
            'old_money'     => $this->memberModel->credit2 ?: 0,
            'change_money'  => $this->data['money'],
            'new_money'     => $this->result_money > 0 ? $this->result_money : 0,
            'type'          => $this->type,
            'service_type'  => $this->service_type,
            'serial_number' => $this->data['serial_number'] ?: '',
            'operator'      => $this->data['operator'],
            'operator_id'   => $this->data['operator_id'],
            'remark'        => $this->data['remark'],
        );
    }






}
