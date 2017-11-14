<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/14 上午10:50
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\finance\controllers;



class WithdrawAuditController extends WithdrawDetailController
{


    public function index()
    {
        //审核、重新审核
        if (!\YunShop::request()->submit_check) {
            return $this->submitCheck();
        }

        //重新审核
        if (!\YunShop::request()->submit_cancel) {
            return $this->submitCancel();
        }

        //打款
        if (!\YunShop::request()->submit_pay) {
            return $this->submitPay();
        }

        return $this->message('提交数据有误，请刷新重试', yzWebUrl("finance.withdraw-detail.index", ['id' => $this->withdrawModel->id]));
    }


    private function submitCheck()
    {
        if ($this->withdrawModel->status != 0) {
            return $this->message('审核失败,数据不符合提现规则!', yzWebUrl("finance.withdraw-detail.index", ['id' => $this->withdrawModel->id]));
        }
        return $this->examine();
    }


    private function submitCancel()
    {
        if ($this->withdrawModel->status != -1) {
            return $this->message('重审核失败,数据不符合提现规则!', yzWebUrl("finance.withdraw-detail.index", ['id' => $this->withdrawModel->id]));
        }
        return $this->examine();
    }


    private function submitPay()
    {

    }


    private function examine()
    {
        $audit_data = \YunShop::request()->audit;

        $
        foreach ($audit_data as $income_id => $status) {

        }
    }







}
