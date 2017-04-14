<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/2
 * Time: 上午10:31
 */

namespace app\common\services\finance;


use app\common\facades\Setting;
use app\common\models\finance\BalanceRecharge;
use app\common\models\finance\BalanceTransfer;
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
    const INCOME = 1;

    //类型：支出
    const EXPENDITURE = 2;

    //状态：失败
    const STATUS_FAIL = -1;

    //状态：成功
    const STATUS_SUCCESS = 1;


    /**
     * 余额变动接口
     * $data 参数说明
     *     $data = array(
     *           'member_id'     => '', // 会员ID
     *           'change_money'  => '', // 改变余额值 100 或 -100
     *           'serial_number' => '', // 订单号或流水号，有订单号记录的直接写订单号，未做记录的可以为空
     *           'operator'      => '', // 来源，-2会员，-1，订单，0 商城， 1++ 插件ID（没有ID值可以给插件标示）
     *           'operator_id'   => '', // 来源ID，如：文章营销某一篇文章的ID，订单ID，海报ID
     *           'remark'        => '', // 备注，文章营销 '奖励' 余额 'N' 元【越详细越好】
     *           'service_type'  => '', // 例：\app\common\models\finance\Balance::BALANCE_RECHARGE，
     *                                  // service_type 到 Balance 类中找自己所属类型

     *           'recharge_type'          => '', //充值时需要增加 rechrage_type 字段，支付类型 ,后台充值0，微信支付1，支付宝2，其他支付3，
     *      );
     *       //use app\common\services\fiance\Balance;
     *      $result = (new Balance())->changeBalance($data);
     *
     * @param $data
     * @return bool|\Illuminate\Support\MessageBag|string
     */
    public function changeBalance($data)
    {
        $this->data = $data;
        $this->service_type = $data['service_type'];

        return $this->validatorServiceType();
    }

    /**
     * @param $data = array( 'order_sn'=> '', 'pay_sn'=> '' );
     * use app\common\services\finance\Balance;
     * (new Balance())->payResult($data);
     * 充值支付完成回调方法
     * @param $data = array( 'order_sn'=> '', 'pay_sn'=> '' );
     */
    public function payResult($data = [])
    {
        $order_sn = 'RV20170414092235224369';
        $rechargeMode = BalanceRecharge::getRechargeRecordBy0rdersn($order_sn);

        $this->data = array(
            'member_id'         => $rechargeMode->member_id,
            //todo 验证余额值
            'change_money'      => $rechargeMode->money,
            'serial_number'     => $ordersn,
            'operator'          => BalanceRecharge::PAY_TYPE_MEMBER,
            'operator_id'       => $rechargeMode->id,
            'remark'            => '会员充值'.$rechargeMode->money . '元，支付单号：',
            'service_type'      => \app\common\models\finance\Balance::BALANCE_RECHARGE,
        );
        $this->service_type = \app\common\models\finance\Balance::BALANCE_RECHARGE;
        $this->attachedType();

        $result = $this->updateRechargeStatus($rechargeMode->id);
        if ($result === true) {
            $this->rechargeSale();
        }
    }

    /**
     * 充值设置，？应该增加开关判断 ？
     */
    private function rechargeSale()
    {
        $rechargeSet = Setting::get('finance.balance');
        return $this->rechargeSaleMath($rechargeSet['sale']);
    }

    /**
     * 充值满额送计算，
     * @param array $data
     */
    private function rechargeSaleMath($data = [])
    {
        $data = array_values(array_sort($data, function ($value) {

            return $value['enough'];
        }));
        rsort($data);
        foreach ($data as $key) {
            if (empty($key['enough']) || empty($key['give'])) {
                continue;
            }
            if ($this->data['change_money'] >= floatval($key['enough'])) {
                if (strexists($key['give'], '%')) {
                    $result = round(floatval(str_replace('%', '', $key['give'])) / 100 * $this->data['change_money'], 2);
                } else {
                    $result = round(floatval($key['give']), 2);
                }
                $enough = floatval($key['enough']);
                $give = $key['give'];
                break;
            }
        }
        $result = array(
            'member_id'         => $this->data['member_id'],
            //todo 验证余额值
            'change_money'      => $result,
            'serial_number'     => $this->data['serial_number'],
            'operator'          => BalanceRecharge::PAY_TYPE_MEMBER,
            'operator_id'       => $this->data['member_id'],
            'remark'            => '充值满' . $enough . '赠送' . $give . '(充值金额:' . $this->data['change_money'] . '元)',
            'service_type'      => \app\common\models\finance\Balance::BALANCE_AWARD,
        );
        $this->changeBalance($result);

    }




    /**
     * @return bool|\Illuminate\Support\MessageBag|string
     */
    private function validatorServiceType()
    {
        $this->attachedType();

        if ($this->type == static::INCOME && in_array($this->service_type,
                [
                    \app\common\models\finance\Balance::BALANCE_RECHARGE,
                    \app\common\models\finance\Balance::BALANCE_TRANSFER,
                    \app\common\models\finance\Balance::BALANCE_AWARD,
                    \app\common\models\finance\Balance::BALANCE_INCOME,
                    \app\common\models\finance\Balance::CANCEL_DEDUCTION
                ])
        ) {
            return $this->judgeMethod();
        }
        if ($this->type == static::EXPENDITURE && in_array($this->service_type,
                [
                    \app\common\models\finance\Balance::BALANCE_CONSUME,
                    \app\common\models\finance\Balance::BALANCE_DEDUCTION,
                    \app\common\models\finance\Balance::BALANCE_WITHDRAWAL,
                    \app\common\models\finance\Balance::CANCEL_AWARD
                ])
        ) {
            return $this->judgeMethod();
        }
        //后台充值可以充值负数
        if ($this->type == static::EXPENDITURE && $this->data['operator'] == BalanceRecharge::PAY_TYPE_SHOP && $this->data['service_type'] == \app\common\models\finance\Balance::BALANCE_RECHARGE ) {
            return $this->judgeMethod();
        }
        return '接口请求错误';
    }

    /**
     * @return bool|\Illuminate\Support\MessageBag|string
     */
    private function judgeMethod()
    {
        switch ($this->service_type) {
            case \app\common\models\finance\Balance::BALANCE_RECHARGE:
                return $this->rechargeRecord();
                break;
            case \app\common\models\finance\Balance::BALANCE_TRANSFER:
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

    private function transferRecord()
    {
        $transferModel = new BalanceTransfer();

        $transferModel->fill($this->getTransferData());
        $validator = $transferModel->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        if ($transferModel->save()) {

            return $this->updateTransferStatus($transferModel->id);
        }
        return '转让记录写入失败';
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
     * @params int $type 充值类型，后台充值0，微信支付1，支付宝2，其他支付4，
     *
     * @return mixed
     * @Author yitian */
    private function rechargeTypeFor($type, $recordId)
    {
        switch ($type) {
            //充值支付类型：0 后台充值
            case BalanceRecharge::PAY_TYPE_SHOP:
                return $this->updateRechargeStatus($recordId);
                break;
            default:
                return $recordId;
        }
    }



    /**
     * 修改充值记录充值状态
     * @param $recordId
     * @return bool|\Illuminate\Support\MessageBag|string
     * @Author yitian */
    private function updateRechargeStatus($recordId)
    {
        $rechargeModel = BalanceRecharge::getRechargeRecordByid($recordId);
        $rechargeModel->status = static::STATUS_SUCCESS;
        if ($rechargeModel->save()) {
            return $this->detailRecord();
        }
        return '充值记录状态修改失败';
    }

    private function updateTransferStatus($recordId)
    {
        $transferModel = BalanceTransfer::getTransferRecordByRecordId($recordId);
        $transferModel->status = static::STATUS_SUCCESS;
        if ($transferModel->save()) {
            $this->data['change_money'] = -$this->data['change_money'];
            if ($this->detailRecord() === true) {
                $this->data['change_money'] = -$this->data['change_money'];
                $this->data['member_id'] = $this->data['recipient_id'];
                return $this->detailRecord();
            }
            return '修改转让值余额明细记录失败';
        }
        return '修改转让记录状态失败';
    }

    //修改会员余额
    private function updateMemberBalance()
    {
        $memberModel = Member::getMemberById($this->data['member_id']);
        $memberModel->credit2 = $memberModel->credit2 + $this->data['change_money'];
        if ($memberModel->credit2 < 0 ) {
            $memberModel->credit2 = 0;
        }

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
        $new_money = $this->data['change_money'] + $memberModel->credit2;
        if ($new_money < 0) {
            $new_money = 0;
        }
        return array(
            'uniacid'   => \YunShop::app()->uniacid,
            'member_id' => $this->data['member_id'],
            'old_money' => (double)$memberModel->credit2,
            'money'     => $this->data['change_money'],
            'new_money' => $new_money,
            'type'      => $this->data['recharge_type'],
            'ordersn'   => $this->getRechargeOrderSN(),
            'status'    => static::STATUS_FAIL
        );
    }

    private function getTransferData()
    {
        return $data = array(
            'uniacid' => \YunShop::app()->uniacid,
            'transferor' => $this->data['member_id'],
            'recipient' => $this->data['recipient_id'],
            'money' => $this->data['change_money'],
            'status' => static::STATUS_FAIL
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
        $new_money = $this->data['change_money'] + $memberModel->credit2;
        if ($new_money < 0) {
            $new_money = 0;
        }
        return array(
            'uniacid'       => \YunShop::app()->uniacid,
            'member_id'     => $this->data['member_id'],        // 会员ID
            'old_money'     => $memberModel->credit2,
            'change_money'  => $this->data['change_money'],     // 改变余额值 100 或 -100
            'new_money'     => $new_money,
            'type'          => $this->type,
            'service_type'  => $this->service_type,
            'serial_number' => $this->data['serial_number'] ?: '',    // 订单号或流水号，有订单号记录的直接写订单号，未做记录的可以为空
//todo operator 字段值需要如果是插件标示需要主动回去插件ID
            'operator'      => $this->data['operator'],         // 来源，-2会员，-1，订单，0 商城， 1++ 插件ID（没有ID值可以给插件标示）
            'operator_id'   => $this->data['operator_id'],      // 来源ID，如：文章营销某一篇文章的ID，订单ID，海报ID
            'remark'        => $this->data['remark'],           // 备注，文章营销 '奖励' 余额 'N' 元【越详细越好】
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
