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
use app\common\services\finance\BalanceNoticeService;
use app\common\services\finance\MessageService;

class PayedService
{
    /**
     * @var Withdraw
     */
    private $withdrawModel;

    private $uids;

    public function __construct(Withdraw $withdrawModel)
    {
        $this->setWithdrawModel($withdrawModel);
        $this->uids = \Setting::get('withdraw.notice.withdraw_user');
    }


    public function withdrawPay()
    {
        if ($this->withdrawModel->status == Withdraw::STATUS_AUDIT) {
            $this->_withdrawPay();
            return true;
        }
        $this->sendMessage();

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
        \Log::debug('---------进入确认打款接口-----------------');
        if ($this->withdrawModel->status == Withdraw::STATUS_PAYING || $this->withdrawModel->status == Withdraw::STATUS_AUDIT) {

            $this->withdrawModel->pay_at = time();
            DB::transaction(function () {
                $this->_payed();
            });
            return true;
        }
        
        $this->sendMessage();

        throw new ShopException('提现记录不符合确认打款规则');
    }


    /**
     * 提现打款
     *
     * @return bool
     */
    private function _withdrawPay()
    {
        \Log::debug('---------进入提现打款-----------------');
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


    private function  payed()
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
            
            $this->sendMessage(); //发送消息

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
            case Withdraw::WITHDRAW_WITH_EUP_PAY:
                $result = $this->eupWithdrawPay();
                break;
            case Withdraw::WITHDRAW_WITH_SEPARATE_UNION_PAY:
                $result = $this->separateUnionPay();
                break;
            case Withdraw::WITHDRAW_WITH_YOP:
                $result = $this->yopWithdrawPay();
                break;
            case Withdraw::WITHDRAW_WITH_CONVERGE_PAY:
                $result = $this->convergePayWithdrawPay();
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
            
            $this->sendMessage();            

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
            
            $this->sendMessage();            

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
        $this->sendMessage(); //发送消息

        throw new ShopException("收入提现ID：{$this->withdrawModel->id}，提现失败：{$result['msg']}");
    }


    private function eupWithdrawPay()
    {
        $member_id = $this->withdrawModel->member_id;
        $sn = $this->withdrawModel->withdraw_sn;
        $amount = $this->withdrawModel->actual_amounts;
        $remark = '';

        $result = PayFactory::create(PayFactory::PAY_EUP)->doWithdraw($member_id, $sn, $amount, $remark);
        if ($result['errno'] === 0) {
            return true;
        }

        throw new ShopException("收入提现ID：{$this->withdrawModel->id}，提现失败：{$result['message']}");
    }

    private function yopWithdrawPay()
    {
        $member_id = $this->withdrawModel->member_id;
        $sn = $this->withdrawModel->withdraw_sn;
        $amount = $this->withdrawModel->actual_amounts;
        $remark = 'withdraw';

        $result = PayFactory::create(PayFactory::YOP)->doWithdraw($member_id, $sn, $amount, $remark);
        if ($result['errno'] == 200) {
            return false;
        }

        throw new ShopException("收入提现ID：{$this->withdrawModel->id}，提现失败：{$result['message']}");
    }

    private function convergePayWithdrawPay()
    {
        $member_id = $this->withdrawModel->member_id;
        $sn = $this->withdrawModel->withdraw_sn;
        $amount = $this->withdrawModel->actual_amounts;
        $remark = 'withdraw';

        $result = PayFactory::create(PayFactory::PAY_WECHAT_HJ)->doWithdraw($member_id, $sn, $amount, $remark);
        if (!$result['data']['errorCode'] && $result['hmac']) {
            return false;
        }
        
        \Log::debug("-----收入提现ID：{$this->withdrawModel->id}-----.-----汇聚提现失败：{$result['data']['errorDesc']}-----");
        throw new ShopException("收入提现ID：{$this->withdrawModel->id}，汇聚提现失败：{$result['data']['errorDesc']}");
    }


    private function separateUnionPay()
    {

        \Log::debug('--------尝试打款withdrawPay---------');
        $member_id = $this->withdrawModel->member_id;
        $withdraw_id = $this->withdrawModel->id;
        $amount = $this->withdrawModel->amounts;

        $sn = $this->withdrawModel->separate['order_sn'];
        $trade_no = $this->withdrawModel->separate['trade_no'];
        //如果订单号不存在或支付单号不存在 重新获取 服务重新打款功能
        if(app('plugins')->isEnabled('separate') && (!$sn || !$trade_no)) {

            $incomeId = $this->withdrawModel->type_id;

            $incomeRelationModel = \Yunshop\Separate\Common\Models\IncomeRelationModel::whereIncomeId($incomeId)->first();

            $sn = $incomeRelationModel->order_sn;
            $trade_no = $incomeRelationModel->pay_order_sn;
        }

        \Log::debug('--------withdrawPay1---------$member_id', print_r($member_id,1));
        //\Log::debug('--------withdrawPay2---------$sn', print_r($sn,1));
        //\Log::debug('--------withdrawPay3---------$withdraw_id', print_r($withdraw_id,1));
        \Log::debug('--------withdrawPay4---------$amount', print_r($amount,1));
        //\Log::debug('--------withdrawPay5---------$trade_no', print_r($trade_no,1));
            //调用分帐接口
        $result = PayFactory::create(PayFactory::PAY_SEPARATE)->doWithdraw($member_id, $sn, $amount, $withdraw_id,$trade_no);

        \Log::debug('--------withdrawPay---------$result', print_r($result, 1));

        if($result) {
            return true;
        }

        return false;
        //TODO  对接结果进行判断1
        //throw new ShopException("分账失败");
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
        \Log::debug('--------进入更新打款体现记录---------');
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


    /**
     * @param string $msg 提现错误信息
     *
     */
    private function sendMessage() 
    {
        if ($this->withdrawModel->type == 'balance') {
            
            return BalanceNoticeService::withdrawFailureNotice($this->withdrawModel);  //余额提现失败通知
        
        } else {
            
            foreach ($this->uids as $k => $v) {
                
                $usermodel = \app\common\models\Member::uniacid()->where('uid', $k)->first();

                if ($usermodel) {
                    //收入提现失败通知
                    MessageService::withdrawFailure($this->withdrawModel->toArray(), $usermodel->toArray()); 
                }
            }
            return ;
        }
    }
}
