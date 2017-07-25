<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/2
 * Time: 下午5:37
 */

namespace app\frontend\modules\finance\controllers;



use app\common\facades\Setting;
use app\common\models\MemberShopInfo;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\finance\BalanceNoticeService;
use app\common\services\PayFactory;
use app\common\components\ApiController;

use app\frontend\modules\finance\models\Balance as BalanceCommon;
use app\frontend\modules\finance\models\BalanceTransfer;
use app\frontend\modules\finance\models\Withdraw;
use app\frontend\modules\finance\models\BalanceRecharge;
use app\frontend\modules\finance\services\BalanceService;

use app\backend\modules\member\models\Member;
use Illuminate\Support\Facades\DB;

class BalanceController extends ApiController
{
    protected $publicAction = ['alipay'];
    protected $ignoreAction = ['alipay'];

    private $memberInfo;

    private $model;

    private $money;


    /**
     * 会员余额页面信息，（余额设置+会员余额值）
     * @return \Illuminate\Http\JsonResponse
     */
    public function balance()
    {
        $memberInfo = $this->getMemberInfo();
        if ($memberInfo) {
            $result = (new BalanceService())->getBalanceSet();
            $result['member_credit2'] = $memberInfo->credit2;

            $pay = \Setting::get('shop.pay');
            $result['wechat'] = $pay['weixin'] ? true : false;
            $result['alipay'] = $pay['alipay'] ? true : false;

            return $this->successJson('获取数据成功', $result);
        }
        return $this->errorJson('未获取到会员数据');

    }


    /**
     * 余额提现页面按钮接口
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function poundage()
    {
        if (!$this->getMemberInfo()) {
            return $this->errorJson('未获取到会员信息');
        }

        $balanceSet = new BalanceService();
        $data = [
            'balance'       => $this->memberInfo->credit2 ?: 0,
            'wechat'        => $balanceSet->withdrawWechat(),
            'alipay'        => $balanceSet->withdrawAlipay(),
            'poundage'      => $balanceSet->withdrawPoundage(),
        ];

        return $this->successJson('获取数据成功', $data);
    }

    //提现+提现限制+提现手续费
    public function withdraw()
    {
        $result = (new BalanceService())->withdrawSet() ? $this->withdrawStart() : '未开启余额提现';

        return $result === true ? $this->successJson('提现申请提交成功') : $this->errorJson($result);
    }



    //余额充值+充值优惠
    public function recharge()
    {
        $result = (new BalanceService())->rechargeSet() ? $this->rechargeStart() : '未开启余额充值';

        if ($result === true) {
            if (intval(\YunShop::request()->pay_type) == PayFactory::PAY_ALIPAY) {
                return $this->successJson('支付接口对接成功', ['ordersn' => $this->model->ordersn]);
            }
            return  $this->successJson('支付接口对接成功', $this->payOrder());
        }
        //return $result === true ? $this->successJson('支付接口对接成功', $this->payOrder()) : $this->errorJson($result);
        return $this->errorJson($result);
    }

    //余额充值，如果是支付宝支付需要二次请求 alipay 支付接口
    public function alipay()
    {
        $orderSn = \YunShop::request()->order_sn;

        $this->model = BalanceRecharge::ofOrderSn($orderSn)->withoutGlobalScope('member_id')->first();
        if ($this->model) {
            return  $this->successJson('支付接口对接成功', $this->payOrder());
        }

        return $this->errorJson('充值订单不存在');
    }

    //余额转让
    public function transfer()
    {
        $result = (new BalanceService())->transferSet() ? $this->transferStart() : '未开启余额转让';

        return $result === true ? $this->successJson('转让成功') : $this->errorJson($result);
    }


    //记录【全部、收入、支出】
    public function record()
    {
        $memberInfo = $this->getMemberInfo();
        if ($memberInfo) {
            $type = \YunShop::request()->record_type;
            $recordList = BalanceCommon::getMemberDetailRecord($this->memberInfo->uid, $type);

            return $this->successJson('获取记录成功', $recordList->toArray());
        }
        return $this->errorJson('未获取到会员信息');
    }

    //获取会员信息
    private function getMemberInfo()
    {
        $member_id = \YunShop::app()->getMemberId();
        //$member_id = \YunShop::app()->getMemberId() ?: \YunShop::request()->uid;
        return $this->memberInfo = Member::getMemberInfoById($member_id) ?: false;
    }

    //充值开始
    private function rechargeStart()
    {
        if (!$this->getMemberInfo()) {
            return '未获取到会员数据,请重试！';
        }
        $this->model = new BalanceRecharge();

        $this->model->fill($this->getRechargeData());
        $validator = $this->model->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        if ($this->model->save()) {
            return true;
        }
        return '充值写入失败，请联系管理员';
    }

    //余额转让开始
    private function transferStart()
    {
        $recipient = \YunShop::request()->recipient;
        if (!$this->getMemberInfo()) {
            return '未获取到会员信息';
        }
        if (!Member::getMemberInfoById(\YunShop::request()->recipient)) {
            return '被转让者不存在';
        }
        if ($this->memberInfo->uid == $recipient) {
            return '转让者不能是自己';
        }
        if (\YunShop::request()->transfer_money <= 0){
            return '转让金额必须大于零';
        }
        if ($this->memberInfo->credit2 < \YunShop::request()->transfer_money) {
            return '转让余额不能大于您的余额';
        }
        $this->model = new BalanceTransfer();

        $this->model->fill($this->getTransferData());
        $validator = $this->model->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        if ($this->model->save()) {
            //$result = (new BalanceService())->balanceChange($this->getChangeBalanceDataToTransfer());
            $result = (new BalanceChange())->transfer($this->getChangeBalanceDataToTransfer());
            if ($result === true) {
                $this->model->status = BalanceTransfer::TRANSFER_STATUS_SUCCES;
                if ($this->model->save()) {
                    return true;
                }
            }
            return '修改转让状态失败';
        }
        return '转让写入出错，请联系管理员';
    }

    //提现开始
    private function withdrawStart()
    {
        if (!$this->getMemberInfo()) {
            return '未获取到会员数据,请重试！';
        }

        if (!$this->getWithdrawType()) {
            return '未找到提现类型';
        }
        $withdrawType = $this->getWithdrawType();
        if (!(new BalanceService())->withdrawWechat() && $withdrawType == 'wechat') {
            return '未开启提现到微信';
        }
        if (!(new BalanceService())->withdrawAlipay() && $withdrawType == 'alipay') {
            return '未开启提现到支付宝';
        }
        if (!$this->getMemberAlipaySet() && $withdrawType == 'alipay') {
            return '您未配置支付宝信息，请先修改个人信息中支付宝信息';
        }

        $withdrawAstrict = (new BalanceService())->withdrawAstrict();
        if ($withdrawAstrict > $this->getWithdrawMoney()) {
            return '提现金额不能小于' . $withdrawAstrict . '元';
        }
        if (bccomp($this->getResultWithdrawMoney(),1,2) == -1) {
            return '扣除手续费后的金额不能小于1元';
        }

        //写入提现记录
        DB::beginTransaction();
        $result = $this->withdrawRecordSave();
        if ($result !== true) {
            return $result;
        }

        //修改会员余额
        $result = (new BalanceChange())->withdrawal($this->getChangeBalanceDataToWithdraw());
        if ($result !== true) {
            DB::rollBack();
            return '提现写入失败，请联系管理员';
        }

        DB::commit();

        BalanceNoticeService::withdrawSubmitNotice($this->model);
        return true;
    }


    /**
     * 提现记录写入
     *
     * @return bool|\Illuminate\Support\MessageBag|string
     */
    private function withdrawRecordSave()
    {
        $this->model = new Withdraw();

        $this->model->fill($this->getWithdrawData());
        $validator = $this->model->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        if (!$this->model->save()) {
            return '提现记录写入出错';
        }
        return true;
    }





    //余额转让详细记录数据
    private function getChangeBalanceDataToTransfer()
    {
        return array(
            'member_id'     =>  $this->model->transferor,
            'remark'        => '会员【ID:'.$this->model->transferor.'】余额转让会员【ID：'.$this->model->recipient. '】' . $this->model->money . '元',
            'source'        => ConstService::SOURCE_TRANSFER,
            'relation'      => $this->model->order_sn,
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $this->model->transferor,
            'change_value'  => $this->model->money,
            'recipient'     => $this->model->recipient,
        );
    }


    /**
     * 获取余额提现改变余额 data 数据
     * @return array
     */
    private function getChangeBalanceDataToWithdraw()
    {
        return array(
            'member_id'     => \YunShop::app()->getMemberId(),
            'remark'        => '会员余额提现' . $this->model->amounts,
            'source'        => ConstService::SOURCE_WITHDRAWAL,
            'relation'      => $this->model->withdraw_sn,
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $this->model->member_id,
            'change_value'  => $this->model->amounts
        );
    }

    private function getTransferData()
    {
        return array(
            'uniacid'       => \YunShop::app()->uniacid,
            'transferor'    => \YunShop::app()->getMemberId(),
            'recipient'     => \YunShop::request()->recipient,
            'money'         => trim(\YunShop::request()->transfer_money),
            'status'        => BalanceTransfer::TRANSFER_STATUS_ERROR,
            'order_sn'      => $this->getTransferOrderSN()
        );
    }


    /**
     * 余额提现记录 data 数据
     * @return array
     */
    private function getWithdrawData()
    {
        return array(
            'withdraw_sn'           => $this->getWithdrawOrderSN(),
            'uniacid'               => \YunShop::app()->uniacid,
            'member_id'             => $this->memberInfo->uid,
            'type'                  => 'balance',
            'type_id'               => '',
            'type_name'             => '余额提现',
            'amounts'               => $this->getWithdrawMoney(),                   //提现金额
            'poundage'              => $this->getWithdrawPoundageMoney(),           //提现手续费
            'poundage_rate'         => $this->getWithdrawPoundage(),                //手续费比例
            'pay_way'               => $this->getWithdrawType(),                    //打款方式
            'status'                => '0',                                         //0未审核，1未打款，2已打款， -1无效
            'actual_amounts'        => $this->getResultWithdrawMoney(),
            'actual_poundage'       => $this->getWithdrawPoundageMoney()
        );
    }


    /**
     * 获取 post 提交的提现金额
     * @return string
     */
    private function getWithdrawMoney()
    {
        return trim(\YunShop::request()->withdraw_money) ?: '0';
    }


    /**
     * 获取提现类型
     * @return string
     */
    private function getWithdrawType()
    {
        switch (trim(\YunShop::request()->withdraw_type))
        {
            case 1:
                return 'wechat';
                break;
            case 2:
                return 'alipay';
                break;
            default:
                return '';
        }
    }


    /**
     * 获取提现手续费比例
     * @return int
     */
    private function getWithdrawPoundage()
    {
        return (new BalanceService())->withdrawPoundage();
    }


    /**
     * 获取提现手续费计算的金额值
     * @return float|int|string
     */
    private function getWithdrawPoundageMoney()
    {
        $withdrawMoney = $this->getWithdrawMoney();
        $withdrawProportion = $this->getWithdrawPoundage();

        if ($withdrawMoney && $withdrawProportion) {
            $poundage_money = bcdiv(bcmul($withdrawMoney,$withdrawProportion,2),100,2);
            if (bccomp($poundage_money, 0.01,2) == -1) {
                $poundage_money = 0.01;
            }
            return $poundage_money;
        }
        return 0;
    }

    /**
     * 获取扣除手续费后可提现金额
     * @return string
     */
    private function getResultWithdrawMoney()
    {
        $money = $this->getWithdrawMoney();
        $poundage = $this->getWithdrawPoundageMoney();
        if ($money && $poundage) {
            return bcsub($money, $poundage, 2);
        }
        return $money;
    }


    /**
     * 生成唯一提现订单号
     * @return string
     */
    private function getWithdrawOrderSN()
    {
        $withdraw_sn = createNo('WS', true);
        while (1) {
            if (!Withdraw::validatorOrderSn($withdraw_sn)) {
                break;
            }
            $withdraw_sn = createNo('WS', true);
        }
        return $withdraw_sn;
    }

    /**
     * 生成唯一转让订单号
     * @return string
     */
    private function getTransferOrderSN()
    {
        $orderSn = createNo('TS', true);
        while (1) {
            if (!BalanceTransfer::ofOrderSn($orderSn)->first()) {
                break;
            }
            $orderSn = createNo('TS', true);
        }
        return $orderSn;
    }



    //充值记录表data数据
    private function getRechargeData()
    {
        //$change_money = substr(\YunShop::request()->recharge_money, 0, strpos(\YunShop::request()->recharge_money, '.')+3);
        $change_money = \YunShop::request()->recharge_money;
        return array(
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $this->memberInfo->uid,
            'old_money' => $this->memberInfo->credit2 ?: 0,
            'money' => floatval($change_money),
            'new_money' => $change_money + $this->memberInfo->credit2,
            'ordersn' => $this->getRechargeOrderSN(),
            'type' => intval(\YunShop::request()->pay_type),
            'status' => BalanceRecharge::PAY_STATUS_ERROR
        );
    }

    //生成充值订单号
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

    /**
     * 会员余额充值支付接口
     *
     * @param $data
     * @return array|string|
     * @Author yitian
     */
    private function payOrder()
    {
        $pay = PayFactory::create($this->model->type);

        $result = $pay->doPay($this->payData());
        if ($this->model->type == 1) {
            $result['js'] = json_decode($result['js'], 1);
        }
        \Log::debug('余额充值 result', $result);
        return $result;
    }

    /**
     * 支付请求数据
     *
     * @return array
     * @Author yitian
     */
    private function payData()
    {
        return array(
            'subject' => '会员充值',
            'body' => '会员充值金额' . $this->model->money . '元:'. \YunShop::app()->uniacid,
            'amount' => $this->model->money,
            'order_no' => $this->model->ordersn,
            'extra' => ['type' => 2]
        );
    }

    private function getMemberAlipaySet()
    {
        $array = MemberShopInfo::select('alipay','alipayname')->where('member_id',\YunShop::app()->getMemberId())->first();
        if ($array && $array['alipay'] && $array['alipayname']) {
            return true;
        }
        return false;
    }




}
