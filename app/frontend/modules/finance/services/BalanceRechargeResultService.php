<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/13 下午2:52
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\services;


use app\common\models\finance\BalanceRechargeActivity;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\frontend\modules\finance\models\BalanceRecharge;
use EasyWeChat\Support\Log;
use Illuminate\Support\Facades\DB;

class BalanceRechargeResultService
{
    private $array;

    private $rechargeModel;

    private $enough;

    private $give;

    private $balance_set;


    public function __construct()
    {
        $this->balance_set = new BalanceService();

    }

    /**
     * 余额充值支付回调
     * @param array $array
     * @return bool|string
     */
    public function payResult(array $array)
    {
        $this->array = $array;
        //调试使用
        if (!$array['order_sn']) {
            return '没有获取到订单号';
        }

        DB::beginTransaction();

        $result = $this->updateRechargeStatus();
        if ($result !== true) {
            Log::debug('余额充值：订单号'. $array['order_sn']."修改充值状态失败");
            DB::rollBack();
            return true;
        }
        $result = $this->updateMemberBalance();
        if ($result !== true) {
            Log::debug('余额充值：订单号'. $array['order_sn']."修改会员余额失败");
            DB::rollBack();
            return true;
        }

        //是否增加充值活动限制
        if ($this->balance_set->rechargeActivityStatus()) {

            //是否在活动时间
            $start_time = $this->balance_set->rechargeActivityStartTime();
            $end_time = $this->balance_set->rechargeActivityEndTime();
            $recharge_time = $this->rechargeModel->created_at->timestamp;

            if (!($start_time <= $recharge_time) || !($end_time >= $recharge_time)) {
                Log::debug('余额充值：订单号'. $array['order_sn']."充值时间未在充值活动时间之内，取消赠送");
                return true;
            }

            //参与次数检测
            $rechargeActivity = BalanceRechargeActivity::where('member_id', $this->rechargeModel->member_id)
                ->where('activity_id',$this->balance_set->rechargeActivityCount())
                ->first();

            $fetter = $this->balance_set->rechargeActivityFetter();
            if ($fetter && $fetter >= 1 && $rechargeActivity && $rechargeActivity->partake_count >= $fetter) {
                Log::debug('余额充值：订单号'. $array['order_sn']."会员参与次数已达到上限");
                return true;
            }

            //更新会员参与活动次数
            if ($rechargeActivity) {
                $rechargeActivity->partake_count += 1;
            } else {
                $rechargeActivity = new BalanceRechargeActivity();

                $rechargeActivity->uniacid = $this->rechargeModel->uniacid;
                $rechargeActivity->member_id = $this->rechargeModel->member_id;
                $rechargeActivity->partake_count += 1;
                $rechargeActivity->activity_id = $this->balance_set->rechargeActivityCount();
            }
            $rechargeActivity->save();
        }


        $result = $this->rechargeEnoughGive();
        if ($result !== true) {
            Log::debug('余额充值：订单号'. $array['order_sn']."充值满奖失败");
            DB::rollBack();
            return true;
        }
        DB::commit();
        return true;
    }

    /**
     * 修改充值状态
     * @return mixed
     */
    private function updateRechargeStatus()
    {
        $this->rechargeModel = BalanceRecharge::withoutGlobalScope('member_id')->ofOrderSn($this->array['order_sn'])->first();

        if ($this->rechargeModel) {
            $this->rechargeModel->status = ConstService::STATUS_SUCCESS;
            return $this->rechargeModel->save();
        }
        return false;
    }

    /**
     * 修改会员余额
     * @return bool|string
     */
    private function updateMemberBalance()
    {
        return (new BalanceChange())->recharge($this->getBalanceChangeData());
    }

    /**
     * 获取余额变动明细记录 data 数组
     * @return array
     */
    private function getBalanceChangeData()
    {
        return [
            'member_id'     => $this->rechargeModel->member_id,
            'remark'        => '会员充值'.$this->rechargeModel->money . '元，支付单号：' . $this->array['pay_sn'],
            'source'        => ConstService::SOURCE_RECHARGE,
            'relation'      => $this->rechargeModel->ordersn,
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $this->rechargeModel->member_id,
            'change_value'  => $this->rechargeModel->money,
        ];
    }

    /**
     * 余额充值奖励
     * @return bool|string
     */
    private function rechargeEnoughGive()
    {
        if ($this->getGiveMoney()) {
            return (new BalanceChange())->award($this->getBalanceAwardData());
        }
        return true;
    }

    /**
     * 获取充值奖励金额
     * @return string
     */
    private function getGiveMoney()
    {
        $sale = $this->getRechargeSale();
        $money = $this->rechargeModel->money;

        rsort($sale);
        $result = '';
        foreach ($sale as $key) {
            if (empty($key['enough']) || empty($key['give'])) {
                continue;
            }
            if (bccomp($money,$key['enough'],2) != -1) {
                if ($this->getProportionStatus()) {
                    $result = bcdiv(bcmul($money,$key['give'],2),100,2);
                } else {
                    $result = bcmul($key['give'],1,2);
                }
                $this->enough   = floatval($key['enough']);
                $this->give     = $key['give'];
                break;
            }
        }
        return $result;
    }

    /**
     * 获取充值奖励营销设置数组
     * @return array
     */
    private function getRechargeSale()
    {
        $sale = $this->balance_set->rechargeSale();

        $sale = array_values(array_sort($sale, function ($value) {
            return $value['enough'];
        }));
        return $sale;
    }

    /**
     * 获取余额充值奖励变动 data 数组
     * @return array
     */
    private function getBalanceAwardData()
    {
        return [
            'member_id'     => $this->rechargeModel->member_id,
            'remark'        => $this->getBalanceAwardRemark(),
            'source'        => ConstService::SOURCE_AWARD,
            'relation'      => $this->array['order_sn'],
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $this->rechargeModel->member_id,
            'change_value'  => $this->getGiveMoney(),
        ];
    }

    /**
     * 获取余额奖励日志
     * @return string
     */
    private function getBalanceAwardRemark()
    {
        if ($this->getProportionStatus()) {
            return '充值满' . $this->enough . '元赠送'.$this->give.'%,(充值金额:' . $this->rechargeModel->money . '元)';
        }
        return '充值满' . $this->enough . '元赠送'.$this->give.'元,(充值金额:' . $this->rechargeModel->money . '元)';
    }

    /**
     * 获取余额奖励设置，比例 或 固定金额
     * @return string
     */
    private function getProportionStatus()
    {
        return $this->balance_set->proportionStatus();
    }




}
