<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/14
 * Time: 下午5:06
 */

namespace app\backend\modules\finance\controllers;


use app\common\models\Withdraw;
use app\backend\modules\finance\services\WithdrawService;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\services\finance\BalanceNoticeService;
use Illuminate\Support\Facades\Log;

class BalanceWithdrawController extends BaseController
{
    private $withdrawModel;


    private $withdrawPoundage;


    /**
     * 提现详情
     * @return mixed|string
     */
    public function detail()
    {
        if (!$this->attachedMode()) {
            return $this->message('数据错误，请刷新重试！','','error');
        }

        return view('finance.balance.withdraw', [
            'item' => $this->withdrawModel->toArray(),
        ])->render();
    }

    public function examine()
    {
        if (!$this->attachedMode()) {
            return $this->message('数据错误，请刷新重试!', '', 'error');
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
            if ($this->withdrawModel->status !== 1) {
                return $this->message('打款失败,数据不存在或不符合打款规则!', yzWebUrl("finance.balance-withdraw.detail", ['id' => $requestData['id']]), 'error');
            }
            $result = $this->submitPay();
            if ($result === true) {
               return $this->message('打款成功', yzWebUrl("finance.balance-withdraw.detail", ['id' => $requestData['id']]));
            }
            return $this->message($result, yzWebUrl("finance.balance-withdraw.detail", ['id' => $requestData['id']]), 'error');
        }
        return $this->message('提交数据有误，请刷新重试', yzWebUrl("finance.balance-withdraw.detail", ['id' => $requestData['id']]));
    }

    //提交审核
    private function submitCheck()
    {
        $this->withdrawModel->status = $this->getPostStatus();
        $result = $this->withdrawUpdate();
        if ($result !== true) {
            return $result;
        }
        if ($this->withdrawModel->status == -1) {
            BalanceNoticeService::withdrawFailureNotice($this->withdrawModel);
        }
        return true;
    }


    //保存数据
    private function withdrawUpdate()
    {
        return $this->withdrawModel->save() ?: '数据修改失败，请刷新重试';
    }

    //打款
    private function submitPay()
    {
        $resultPay = '';
        $remark = '提现打款-' . $this->withdrawModel->type_name . '-金额:' . $this->withdrawModel->actual_amounts . '元,' .
            '手续费:' . $this->withdrawModel->actual_poundage;
        if ($this->withdrawModel->pay_way == 'alipay') {
           $resultPay = $this->alipayWithdrawPay($remark);

        } elseif ($this->withdrawModel->pay_way == 'wechat') {
            //微信打款
            $resultPay = $this->wechatWithdrawPay($remark);
        }
        file_put_contents(storage_path('logs/withdraw2.log'),print_r($resultPay,true));
        if ($resultPay === true) {
            $this->withdrawModel->pay_at = time();
            $this->withdrawModel->status = 2;
            if ($this->withdrawModel->save()) {
                Log::info('打款完成!');
                return true;
            }
        }
        return $resultPay;
    }

    private function alipayWithdrawPay($remark)
    {
        $resultPay = WithdrawService::alipayWithdrawPay($this->withdrawModel, $remark);
        Log::info('MemberId:' . $this->withdrawModel->member_id . ', ' . $remark . "支付宝打款中!");
        return $resultPay;
    }

    private function wechatWithdrawPay($remark)
    {
        $resultPay = WithdrawService::wechatWithdrawPay($this->withdrawModel, $remark);
        file_put_contents(storage_path('logs/withdraw1.log'),print_r($resultPay,true));
        Log::info('MemberId:' . $this->withdrawModel->member_id . ', ' . $remark . "微信打款中!");
        if ($resultPay['errno'] == 0){
            return $resultPay['message'];
        }
        return $resultPay;
    }


    /**
     * 获取提现数据 model
     * @return mixed
     */
    private function attachedMode()
    {
        return $this->withdrawModel = Withdraw::getBalanceWithdrawById($this->getPostId());
    }

    /**
     * 获取 post 提交的ID值
     * @return string
     */
    private function getPostId()
    {
        return trim(\YunShop::request()->id);
    }

    private function getPostStatus()
    {
        return trim(\YunShop::request()->status);
    }


}
