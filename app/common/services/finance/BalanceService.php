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

    //附值 Type， 收入：Balance::TYPE_INCOME， 支出：Balance::TYPE_EXPENDITURE
    protected function attachedType()
    {
        return $this->type = $this->data['money'] > 0 ? Balance::TYPE_INCOME : Balance::TYPE_EXPENDITURE;

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
