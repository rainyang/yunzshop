<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/19 下午1:51
 * Email: livsyitian@163.com
 */

namespace app\common\services\withdraw;


use app\common\events\withdraw\WithdrawPayedEvent;
use app\common\events\withdraw\WithdrawPayEvent;
use app\common\events\withdraw\WithdrawPayingEvent;
use app\common\exceptions\ShopException;
use app\common\models\Income;
use app\common\models\Withdraw;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\PayFactory;
use Illuminate\Support\Facades\DB;

class PayedService
{
    /**
     * @var Withdraw
     */
    private $withdrawModel;


    public function __construct(Withdraw $withdrawModel)
    {
        $this->setWithdrawModel($withdrawModel);
    }


    public function withdrawPay()
    {
        if ($this->withdrawModel->status == Withdraw::STATUS_AUDIT) {
            $this->_withdrawPay();
            return true;
        }
        throw new ShopException("提现打款：ID{$this->withdrawModel->id}，不符合打款规则");
    }



    /**
     * 确认打款接口
     *
     * @return bool
     * @throws ShopException
     */
    public function confirmPay()
    {
        if ($this->withdrawModel->status != Withdraw::STATUS_PAYING) {
            throw new ShopException('提现记录不符合确认打款规则');
        }

        $this->withdrawModel->pay_at = time();

        DB::transaction(function () {
            $this->_payed();
        });
        return true;
    }


    /**
     * 提现打款
     *
     * @return bool
     */
    private function _withdrawPay()
    {
        DB::transaction(function () {
            $this->pay();
        });
        return $this->payed();
    }


    private function pay()
    {
        event(new WithdrawPayEvent($this->withdrawModel));

        $this->paying();
    }


    private function paying()
    {
        $this->withdrawModel->status = Withdraw::STATUS_PAYING;
        $this->withdrawModel->pay_at = time();

        event(new WithdrawPayingEvent($this->withdrawModel));
        $this->updateWithdrawModel();
    }


    private function payed()
    {
        $result = $this->tryPayed();
        if ($result === true) {
            DB::transaction(function () {
                $this->_payed();
            });
        }
        return true;
    }


    private function _payed()
    {
        $this->withdrawModel->status = Withdraw::STATUS_PAY;
        $this->withdrawModel->arrival_at = time();

        $this->updateWithdrawModel();

        event(new WithdrawPayedEvent($this->withdrawModel));
    }


    /**
     * 尝试打款
     *
     * @return bool
     * @throws ShopException
     */
    private function tryPayed()
    {
        try {

            $result = $this->_tryPayed();

            //dd($result);
            if ($result !== true) {

                //处理中 返回 false , 提现记录打款中
                return false;
            }
            return true;

        } catch (\Exception $exception) {

            $this->withdrawModel->status = Withdraw::STATUS_AUDIT;
            $this->withdrawModel->pay_at = null;

            $this->updateWithdrawModel();

            throw new ShopException($exception->getMessage());

        } finally {
            // todo 增加验证队列
        }
    }

    
    /**
     * 尝试打款
     *
     * @return bool
     * @throws ShopException
     */
    private function _tryPayed()
    {
        switch ($this->withdrawModel->pay_way)
        {
            case Withdraw::WITHDRAW_WITH_BALANCE:
                $result = $this->balanceWithdrawPay();
                break;
            case Withdraw::WITHDRAW_WITH_WECHAT:
                $result = $this->wechatWithdrawPay();
                break;
            case Withdraw::WITHDRAW_WITH_ALIPAY:
                $result = $this->alipayWithdrawPay();
                break;
            case Withdraw::WITHDRAW_WITH_MANUAL:
                $result = $this->manualWithdrawPay();
                break;
            case Withdraw::WITHDRAW_WITH_HUANXUN:
                $result = $this->huanxunWithdrawPay();
                break;
            case "eup_pay":
                $result = $this->eupWithdrawPay();
                break;
            default:
                throw new ShopException("收入提现ID：{$this->withdrawModel->id}，提现失败：未知打款类型");
        }
        return $result;
    }


    /**
     * 提现打款：余额打款
     *
     * @return bool
     * @throws ShopException
     */
    private function balanceWithdrawPay()
    {
        $remark = "提现打款-{$this->withdrawModel->type_name}-金额:{$this->withdrawModel->actual_amounts}";

        $data = array(
            'member_id'     => $this->withdrawModel->member_id,
            'remark'        => $remark,
            'source'        => ConstService::SOURCE_INCOME,
            'relation'      => '',
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $this->withdrawModel->id,
            'change_value'  => $this->withdrawModel->actual_amounts
        );

        $result = (new BalanceChange())->income($data);

        if ($result !== true) {
            throw new ShopException("收入提现ID：{$this->withdrawModel->id}，提现失败：{$result}");
        }
        return true;
    }


    /**
     * 提现打款：微信打款
     *
     * @return bool
     * @throws ShopException
     */
    private function wechatWithdrawPay()
    {
        $member_id = $this->withdrawModel->member_id;
        $sn = $this->withdrawModel->withdraw_sn;
        $amount = $this->withdrawModel->actual_amounts;
        $remark = '';

        $result = PayFactory::create(PayFactory::PAY_WEACHAT)->doWithdraw($member_id, $sn, $amount, $remark);
        if ($result['errno'] == 1) {
            throw new ShopException("收入提现ID：{$this->withdrawModel->id}，提现失败：{$result['message']}");
        }
        return true;
    }



    private function alipayWithdrawPay()
    {
        $member_id = $this->withdrawModel->member_id;
        $sn = $this->withdrawModel->withdraw_sn;
        $amount = $this->withdrawModel->actual_amounts;
        $remark = '';

        $result = PayFactory::create(PayFactory::PAY_ALIPAY)->doWithdraw($member_id, $sn, $amount, $remark);

        if (is_array($result)) {

            if ($result['errno'] == 1) {
                throw new ShopException("收入提现ID：{$this->withdrawModel->id}，提现失败：{$result['message']}");
            }
            return true;
        }

        redirect($result)->send();
    }


    private function huanxunWithdrawPay()
    {
        $member_id = $this->withdrawModel->member_id;
        $sn = $this->withdrawModel->withdraw_sn;
        $amount = $this->withdrawModel->actual_amounts;
        $remark = '';

        $result = PayFactory::create(PayFactory::PAY_Huanxun_Quick)->doWithdraw($member_id, $sn, $amount, $remark);
        if ($result['result'] == 10) {
            return true;
        }
        if ($result['result'] == 8) {
            return false;
        }
        throw new ShopException("收入提现ID：{$this->withdrawModel->id}，提现失败：{$result['msg']}");
    }


    private function eupWithdrawPay()
    {
        $member_id = $this->withdrawModel->member_id;
        $sn = $this->withdrawModel->withdraw_sn;
        $amount = $this->withdrawModel->actual_amounts;
        $remark = '';

        $result = PayFactory::create(PayFactory::PAY_EUP)->doWithdraw($member_id, $sn, $amount, $remark);
        if ($result['errno'] == 0) {
            return true;
        }

        throw new ShopException("收入提现ID：{$this->withdrawModel->id}，提现失败：{$result['message']}");
    }


    /**
     * 手动打款
     *
     * @return bool
     */
    private function manualWithdrawPay()
    {
        return true;
    }


    /**
     * @return bool
     * @throws ShopException
     */
    private function updateWithdrawModel()
    {
        $validator = $this->withdrawModel->validator();
        if ($validator->fails()) {
            throw new ShopException($validator->messages());
        }
        if (!$this->withdrawModel->save()) {
            throw new ShopException("提现打款-打款记录更新状态失败");
        }
        return true;
    }


    /**
     * @param $withdrawModel
     * @throws ShopException
     */
    private function setWithdrawModel($withdrawModel)
    {
        $this->withdrawModel = $withdrawModel;
    }

}
