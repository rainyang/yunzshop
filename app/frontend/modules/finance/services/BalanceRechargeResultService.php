<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/13 下午2:52
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\services;


class BalanceRechargeResultService
{
    public function payResult(array $array)
    {

    }

    private function getRechargeModel()
    {
        
    }


    /**
     * 余额充值回调，支付成功回调方法
     *
     *
     * @param array $data
     * @return bool|string
     * @throws AppException
     */
    public function payResult($data = [])
    {
        $rechargeMode = BalanceRecharge::getRechargeRecordByOrdersn($data['order_sn']);
        if (!$rechargeMode) {
            throw new AppException('充值失败');
        }
        $rechargeMode->status = BalanceRecharge::PAY_STATUS_SUCCESS;
        if ($rechargeMode->save()) {
            $this->data = array(
                'member_id'     => $rechargeMode->member_id,
                'remark'        => '会员充值'.$rechargeMode->money . '元，支付单号：' . $data['pay_sn'],
                'source'        => ConstService::SOURCE_RECHARGE,
                'relation'      => $rechargeMode->ordersn,
                'operator'      => ConstService::OPERATOR_MEMBER,
                'operator_id'   => $rechargeMode->member_id,
                'change_value'  => $rechargeMode->money,
            );
            $result = (new BalanceChange())->recharge($this->data);
            if ($result === true) {
                return $this->rechargeSaleMath();
            }
            throw new AppException('更新会员余额失败');
        }
        throw new AppException('修改充值状态失败');
    }

}
