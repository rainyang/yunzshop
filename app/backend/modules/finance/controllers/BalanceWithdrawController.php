<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/14
 * Time: 下午5:06
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\finance\models\Withdraw;
use app\backend\modules\finance\services\WithdrawService;
use app\common\components\BaseController;
use app\common\facades\Setting;
use Illuminate\Support\Facades\Log;

class BalanceWithdrawController extends BaseController
{
    private $withdrawModel;


    private $withdrawPoundage;



    public function detail()
    {
        $this->attachedMode();
        if (!$this->withdrawModel) {
            return $this->message('数据错误，请刷新重试！');
        }

        return view('finance.balance.withdraw', [
            'item' => $this->withdrawModel->toArray(),
            'examine' => $this->getExamine(),
        ])->render();
    }

    public function examine()
    {
        $this->attachedMode();
        if (!$this->withdrawModel) {
            return $this->message('数据错误，请刷新重试！');
        }
        $requestData = \YunShop::request();
        if (isset($requestData['submit_check']) || isset($requestData['submit_cancel'])) {
            //审核--重新审核
            $result = $this->submitCheck();
            if ($result === true) {
                return $this->message('提交审核成功', yzWebUrl("finance.balance-withdraw.detail", ['id' => $requestData['id']]));
            }
            return $this->message($result, yzWebUrl("finance.balance-withdraw.detail", ['id' => $requestData['id']]), 'error');
        } elseif (isset($requestData['submit_pay'])) {
            //打款
            if ($this->withdrawModel->status !== '1') {
                return ['msg' => '打款失败,数据不存在或不符合打款规则!'];
            }
            $result = $this->submitPay();
            if ($result === true) {
               return $this->message('提交审核成功', yzWebUrl("finance.balance-withdraw.detail", ['id' => $requestData['id']]));
            }
            return $this->message($result, yzWebUrl("finance.balance-withdraw.detail", ['id' => $requestData['id']]), 'error');
        }
        return $this->message('提交数据有误，请刷新重试', yzWebUrl("finance.balance-withdraw.detail", ['id' => $requestData['id']]));
    }

    //提交审核
    private function submitCheck()
    {
        $this->attachedExamineData();
        $this->withdrawModel->status = \YunShop::request()->status;
        return $this->withdrawUpdate();
    }

    //打款
    private function submitPay()
    {
        $resultPay = '';
        $remark = '提现打款-' . $this->withdrawModel->type_name . '-金额:' . $this->withdrawModel->actual_amounts . '元,' .
            '手续费:' . $this->withdrawModel->actual_poundage;
        if ($this->withdrawModel->pay_way == '2') {
            //支付宝打款
            $resultPay = WithdrawService::alipayWithdrawPay($this->withdrawModel, $remark);
            Log::info('MemberId:' . $this->withdrawModel->member_id . ', ' . $remark . "支付宝打款中!");
        } elseif ($this->withdrawModel->pay_way == '1') {
            //微信打款
            $resultPay = WithdrawService::wechtWithdrawPay($this->withdrawModel, $remark);
            Log::info('MemberId:' . $this->withdrawModel->member_id . ', ' . $remark . "微信打款中!");
        }
        return $resultPay ? $this->updatePayTime(): "打款失败";
    }

    //保存数据
    private function withdrawUpdate()
    {
        return $this->withdrawModel->save() ?: '数据修改失败，请刷新重试';
    }

    //修改打款时间
    private function updatePayTime()
    {
        $this->withdrawModel->pay_at = time();
        return $this->withdrawUpdate();
    }

    //附值审核数据
    private function attachedExamineData()
    {
        $examine = $this->getExamine();
        $this->withdrawModel->actual_poundage = $examine['poundage'];
        $this->withdrawModel->actual_amounts  = $examine['result_money'];
        $this->withdrawModel->audit_at        = time();
    }

    //获取去提现手续费设置
    private function withdrawSet()
    {
        $withdrawSet = Setting::get('withdraw.balance');
        $this->withdrawPoundage = $withdrawSet['poundage'];
    }

    //余额提现手续费N元
    private function withdrawPoundageMath()
    {
        $this->withdrawSet();
        return round(floatval($this->withdrawModel->amounts * $this->withdrawPoundage), 2);
    }

    //审核金额运算数据、结果
    private function getExamine()
    {
        return array(
            'examine_money' => $this->withdrawModel->amounts,
            'poundage'      => $this->withdrawPoundageMath(),
            'result_money'  => $this->withdrawModel->amounts - $this->withdrawPoundageMath()
        );
    }

    //附值 model
    private function attachedMode()
    {
        $this->withdrawModel = Withdraw::getBalanceWithdrawById(\YunShop::request()->id);
    }


}
