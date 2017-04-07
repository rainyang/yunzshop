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
    //余额变动方式，
    private $type;

    //余额变动所属业务类型
    private $service_type;

    // 需要记录的数据 类型array
    private $data;

    //类型：收入
    const INCOME      = 1;

    //类型：支出
    const EXPENDITURE = 2;

    /*
     *
        $data = array(
            'member_id'     => '', // 会员ID
            'change_money'  => '', // 改变余额值 100 或 -100
            'serial_number' => '', // 订单号或流水号，有订单号记录的直接写订单号，未做记录的可以为空
            'operator'      => '', // 来源，-2会员，-1，订单，0 商城， 1++ 插件ID（没有ID值可以给插件标示）
            'operator_id'   => '', // 来源ID，如：文章营销某一篇文章的ID，订单ID，海报ID
            'remark'        => '', // 备注，文章营销 '奖励' 余额 'N' 元【越详细越好】

            'type'          => '', //充值时需要增加type字段，支付类型 ,后台充值1，微信支付2，支付宝3，其他支付4，
        );
        //use app\common\services\fiance\Balance;
        $result = (new Balance())->rechargeBalance($data);
     *
     */

    //1余额充值接口 +
    public function rechargeBalance($data = [])
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_RECHARGE;

        return $this->acceptInterface($data);
    }

    //2余额消费接口 -
    public function consumeBalance($data = [])
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_CONSUME;

        return $this->acceptInterface($data);
    }

    //3余额转让接口 +-
    public function transferBalance($data = [])
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_TRANSFER;

        return $this->acceptInterface($data);
    }

    //4余额抵扣 -
    public function deductionBalance($data = [])
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_DEDUCTION;

        return $this->acceptInterface($data);
    }

    //5余额奖励 +
    public function awardBalance($data = [])
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_AWARD;

        return $this->acceptInterface($data);
    }

    //6余额提现 -
    public function withdrawalBalance($data = [])
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_WITHDRAWAL;

        return $this->acceptInterface($data);
    }

    //7提现到余额  +
    public function incomeBalance($data = [])
    {
        $this->service_type = \app\common\models\finance\Balance::BALANCE_INCOME;

        return $this->acceptInterface($data);
    }

    //8抵扣取消余额回滚 +
    public function cancelDeductionBalance($data = [])
    {
        $this->service_type = \app\common\models\finance\Balance::CANCEL_DEDUCTION;

        return $this->acceptInterface($data);
    }

    //9奖励取消余额回滚 -
    public function cancelAwardBalance($data =[])
    {
        $this->service_type = \app\common\models\finance\Balance::CANCEL_AWARD;

        return $this->acceptInterface($data);
    }


    /*
     * 承接接口
     *
     * @params array $data
     *
     * @retrun mixed
     * @Author yitian */
    private function acceptInterface($data = [])
    {
        $this->data = $data;
        $this->attachedType();

        if ($this->type == 1 && in_array($this->service_type, [1,3,5,7,8])) {
            return $this->resolveInterface();
        }
        if ($this->type == 2 && in_array($this->service_type, [2,3,4,6,9])) {
            return $this->resolveInterface();
        }
        //后台充值可以充值负数
        if ($this->type == 2 && $this->data['operator'] == 0 && $this->data['type'] == 1) {
            return $this->resolveInterface();
        }
        return '接口请求错误';
    }

    /*
     * 分解承接接口 调用不同方法
     *
     * @Author yitian */
    private function resolveInterface()
    {
        switch ($this->service_type)
        {
            case 1:
                return $this->rechargeRecord();
                break;
            case 3:
                return $this->transferRecord();
                break;
            default:
                return $this->detailRecord();
        }
    }

    /*
     * 添加充值记录，成功跳转修改状态方法，失败直接返回错误信息
     *
     * @return mixed
     * @return yitian */
    private function rechargeRecord()
    {
        $rechargeModel = new BalanceRecharge();

        $rechargeModel->fill($this->getRechargeData());
        $validator = $rechargeModel->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        if ($rechargeModel->save()) {
            $this->data['serial_number'] = $rechargeModel->ordersn;
            return $this->rechargeTypeFor($rechargeModel->type, $rechargeModel->id);
        }
        return '充值记录写入失败';
    }

    //
    private function transferRecord()
    {
        return 'transferRecord';
    }

    /*
     * 余额变动明细记录,写入成功修改会员余额，失败返回错误信息
     *
     * @Author yitian */
    private function detailRecord()
    {
        $balanceMode = new \app\common\models\finance\Balance();
        $balanceMode->fill($this->getDetailData());
        $validator = $balanceMode->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        if ($balanceMode->save()) {
            return $this->updateMemberBalance();
        }
        return '余额明细记录写入失败';
    }

    /*
     * 充值类型，后台充值直接写入，修改会员余额， 会员自行充值需调用支付接口
     *
     * @params int $type 充值类型，后台充值1，微信支付2，支付宝3，其他支付4，
     *
     * @return mixed
     * @Author yitian */
    private function rechargeTypeFor($type, $recordId)
    {
        switch ($type)
        {
            case 1:
                return $this->updateRrechargeStatus($recordId);
                break;
            default:
                //todo 调用支付接口
                return '需要调用支付接口';
        }
    }

    //
    private function updateRrechargeStatus($recordId)
    {
        $rechargeModel = BalanceRecharge::getRechargeRecordByid($recordId);
        $rechargeModel->status = 1;
        if ($rechargeModel->save()) {
            return $this->detailRecord();
        }
        return '充值记录状态修改失败';
    }

    //修改会员余额
    private function updateMemberBalance()
    {
        $memberModel = Member::getMemberById($this->data['member_id']);
        $memberModel->credit2 = ($memberModel->credit2 + $this->data['change_money']) >= 0 ?: 0;

        if ($memberModel->save()) {
            //接口调用完成
            return true;
        }
        return '会员余额写入失败';
    }

    /*
     * 获取充值记录数据
     *
     * @return array
     * @Author yitian */
    private function getRechargeData()
    {
        $memberModel = Member::getMemberById($this->data['member_id']);
        return array(
            'uniacid'   => \YunShop::app()->uniacid,
            'member_id' => $this->data['member_id'],
            'old_money' => $memberModel->credit2,
            'money'     => $this->data['change_money'],
            'new_money' => $this->data['change_money'] + $memberModel->credit2 >= 0 ?: 0,
            'type'      => $this->data['type'],
            'ordersn'   => $this->getRechargeOrderSN(),
            'status'    => '-1'
        );
    }

    /*
     * 获取余额变动明细数据
     *
     * @return array
     * @Author yitian */
    private function getDetailData()
    {
        $memberModel = Member::getMemberById($this->data['member_id']);
        return array(
            'uniacid'       => \YunShop::app()->uniacid,
            'member_id'     => $this->data['member_id'],    // 会员ID
            'old_money'     => $memberModel->credit2,
            'change_money'  => $this->data['change_money'], // 改变余额值 100 或 -100
            'new_money'     => $memberModel->credit2 + $this->data['change_money'] >= 0 ?: 0,
            'type'          => $this->type,
            'service_type'  => $this->service_type,
            'serial_number' => $this->data['serial_number'], // 订单号或流水号，有订单号记录的直接写订单号，未做记录的可以为空
//todo operator 字段值需要如果是插件标示需要主动回去插件ID
            'operator'      => $this->data['operator'], // 来源，-2会员，-1，订单，0 商城， 1++ 插件ID（没有ID值可以给插件标示）
            'operator_id'   => $this->data['operator_id'], // 来源ID，如：文章营销某一篇文章的ID，订单ID，海报ID
            'remark'        => $this->data['remark'], // 备注，文章营销 '奖励' 余额 'N' 元【越详细越好】
            'created_at'    => time()
        );
    }




    /*
     * 获取交易类型，支出 1， 收入 2，
     *
     * @params numeric $chargeMoney
     *
     * @Author yitian */
    private function attachedType()
    {
        return $this->type = ($this->data['change_money'] > 0) ? static::INCOME : static::EXPENDITURE;
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

}
