<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/2
 * Time: 上午10:31
 */

namespace app\common\services\fiance;


use app\common\models\finance\BalanceRecharge;
use app\common\models\Member;

class Balance
{
    private $service_type;

    private $type;

    const INCOME      = 1; //类型：收入

    const EXPENDITURE = 2; //类型：支出


    /*
     * 修改会员余额 使用时需注意数据判断
     *
     * @params int $memberId 会员ID
     * @params int $balance 改变金额值
     *
     * @return bool
     * @Author yitian */
    public function updateBalance($memberId, $balance)
    {
        $memberModel = Member::getMemberById($memberId);
        if ($memberModel) {
            $memberModel->credit2 += trim($balance);
            if ($memberModel->credit2 < 0) {
                $memberModel->credit2 = 0;
            }
            if ($memberModel->save()) {
                return true;
            }
        }
        //todo 需要增加余额变动日志
        return false;
    }

    /*
     * 商城余额充值,正确返回true 错误返回错误信息
     *
     * @params int $memberId 会员ID
     * @params int or float $balance 余额值
     *
     * @return mixed
     * */
    public function shopBalanceRecharge($memberId, $balance)
    {
        $result = $this->createBalanceRechargeRecord($memberId, $balance);
        if (is_numeric($result)) {
            if ($this->updateBalance($memberId, $balance) === true) {
                return $this->updateRecordStatus($result, 1);
            }
            return '更新会员余额失败';
        }
        return $result;
    }

    /*
     * 会员余额充值,正确返回 true 错误返回错误信息
     *
     * @params int $memberId 会员ID
     * @params int or float $balance 余额值
     *
     * @return mixed
     * @Author yitain */
    public function memberBalanceRecharge($memberId, $balance)
    {
        if (!$memberId) {
            return '未获取到会员ID';
        }
        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $balance)) {
            return '金额必须是大于0的数值，可以为两位小数';
        }
        return $this->createBalanceRechargeRecord($memberId, $balance);
    }

    /*
     * 创建余额充值记录 返回 true 或错误信息
     *
     * @params int $memberId 会员ID
     * @params int or float $balance 余额值
     *
     * @return mixed
     * @Author yitian */
    private function createBalanceRechargeRecord($memberId, $balance)
    {
        $rechargeMode = new BalanceRecharge();

        $rechargeMode->fill($this->getRecordData($memberId, $balance));
        $validator = $rechargeMode->validator($rechargeMode->getAttributes());
        if ($validator->fails()) {
            return $validator->messages();
        }
        if ($rechargeMode->save()) {

            return $rechargeMode->id;
        }
        return '充值记录写入失败';
    }

    /*
     * 更新充值记录中状态值
     *
     * @params int recordId 记录ID
     * @params int $status 充值状态 (-1 充值失败， 0正常， 1 充值成功)
     *
     * */
    public function updateRecordStatus($recordId, $status)
    {
        $rechargeModel = BalanceRecharge::getRechargeRecordByid($recordId);
        if (!$rechargeModel) {
            return '未找到充值记录！';
        }
        $rechargeModel->status = $status;
        if ($rechargeModel->save()) {
            return true;
        }
        return '更新充值状态失败';
    }

    /*
     * 获取充值记录数据
     *
     * @params int $memberId 会员ID
     * @params int or float $balance 余额值
     *
     * @return array()
     * @Author yitian */
    private function getRecordData($memberId, $balance)
    {
        $memberInfo = Member::getMemberById($memberId);
        $newMoney = trim($memberInfo['credit2']) + trim($balance);
        if ($newMoney < 0 ) {
            $newMoney = 0;
        }
        return array(
            'uniacid'       => \YunShop::app()->uniacid,
            'member_id'     => $memberId,
            'old_money'     => $memberInfo['credit2'],
            'money'         => trim($balance),
            'new_money'     => $newMoney,
            'type'          => 1,
            'ordersn'       => $this->getRechargeOrderSN(),
            'status'        => 0
        );
    }

    /*
     * 生成充值订单号
     *
     * @return string
     * @Author yitian */
    private function getRechargeOrderSN()
    {
        $ordersn = createNo('RV', true);
        while (1) {
            if (!BalanceRecharge::validatorOrderSn($ordersn)) {
                break;
            }
            $ordersn = createNo('RV', true);
        }
        return $ordersn;
    }

    //余额充值接口
    public function rechargeBalance()
    {
        $this->type = \app\common\models\finance\Balance::BALANCE_RECHARGE;

    }

    //余额消费接口
    public function consumeBalance()
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_CONSUME;

    }

    //余额转让接口
    public function transferBalance()
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_TRANSFER;
    }

    //余额抵扣
    public function deductionBalance()
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_DEDUCTION;
    }

    //余额奖励
    public function awardBalance()
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_AWARD;
    }

    //余额提现
    public function withdrawalBalance()
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_WITHDRAWAL;
    }

    //提现到余额
    public function incomeBalance()
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_INCOME;
    }

    //抵扣取消余额回滚
    public function cancelDeductionBalance()
    {

    }

    //奖励取消余额回滚
    public function cancelAwardBalance()
    {

    }




    /*
     * 获取交易类型，支出 1， 收入 2，
     *
     * @params numeric $chargeMoney
     *
     * */
    private function attachedType($chargeMoney)
    {
        $this->type = static::INCOME;
        if ($chargeMoney > 0 ) {
            $this->type = static::EXPENDITURE;
        }
    }

}