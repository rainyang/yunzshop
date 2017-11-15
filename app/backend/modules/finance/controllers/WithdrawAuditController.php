<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/14 上午10:50
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\finance\controllers;



use app\backend\modules\finance\models\Withdraw;
use app\common\models\Income;
use Illuminate\Support\Facades\DB;

class WithdrawAuditController extends WithdrawDetailController
{


    public function index()
    {
        $result_data = \YunShop::request();
        //审核
        if (isset($result_data['submit_check'])) {
            return $this->submitCheck();
        }

        //重新审核
        if (isset($result_data['submit_cancel'])) {
            return $this->submitCancel();
        }

        //打款
        if (isset($result_data['submit_pay'])) {
            return $this->submitPay();
        }

        return $this->errorMessage('提交数据有误，请刷新重试');
    }




    private function submitCheck()
    {
        if ($this->withdrawModel->status != Withdraw::STATUS_INITIAL) {
            return $this->errorMessage('审核失败,数据不符合提现规则!');
        }
        return $this->examine();
    }


    private function submitCancel()
    {
        if ($this->withdrawModel->status != -1) {
            return $this->errorMessage('重审核失败,数据不符合提现规则!');
        }
        return $this->examine();
    }


    private function submitPay()
    {

    }


    private function examine()
    {
        $audit_data = \YunShop::request()->audit;

        $audit_count = count($audit_data);

        $actual_amounts = 0;

        $adopt_count    = 0;
        $invalid_count  = 0;
        $reject_count   = 0;

        DB::beginTransaction();
        foreach ($audit_data as $income_id => $status) {

            //通过
            if ($status == Withdraw::STATUS_AUDIT) {
                $adopt_count += 1;
                $actual_amounts += Income::uniacid()->where('id', $income_id)->sum('amount');
                //Income::where('id',$income_id)->update(['pay_status' => Income::STATUS_WITHDRAW]);
            }

            //无效
            if ($status == Withdraw::STATUS_INVALID) {
                $invalid_count += 1;
                //Income::where('id',$income_id)->update(['pay_status' => Income::STATUS_WITHDRAW]);
            }

            //驳回
            if ($status == Withdraw::STATUS_REJECT) {
                $reject_count += 1;
                //Income::where('id',$income_id)->update(['status' => Income::STATUS_INITIAL, 'pay_status' => Income::PAY_STATUS_REJECT]);
            }
        }

        $this->withdrawModel->status = Withdraw::STATUS_AUDIT;

        //如果全无效
        if ($invalid_count > 0 && $invalid_count == $audit_count) {
            $this->withdrawModel->status = Withdraw::STATUS_INVALID;
        }

        //如果全驳回
        if ($reject_count > 0 && $reject_count == $audit_count) {
            $this->withdrawModel->status = Withdraw::STATUS_PAY;
            $this->withdrawModel->pay_at = $this->withdrawModel->arrival_at = time();
        }

        //如果是无效 + 驳回
        if ($invalid_count > 0 && $reject_count > 0 && ($invalid_count + $reject_count) == $audit_count) {
            dd('未确定解决方案');
            //$withdraw_status = Withdraw::STATUS_AUDIT;
        }


        $this->withdrawModel->audit_at = time();
        $this->withdrawModel->actual_amounts = $actual_amounts;
        $this->withdrawModel->actual_poundage = $this->getActualPoundage();
        $this->withdrawModel->actual_servicetax = $this->getActualServiceTax();


        $result = $this->withdrawModel->save();
        if ($result !== true) {
            DB::rollBack();
            return $this->errorMessage('审核失败：记录修改失败!');
        }

        DB::commit();
        return $this->successJson('审核成功!');
    }




    /**
     * 手续费
     * @return string
     */
    private function getActualPoundage()
    {
        return bcdiv(bcmul($this->withdrawModel->actual_amounts,$this->withdrawModel->poundage_rate,4),100,2);
    }



    /**
     * 劳务税
     * @return string
     */
    private function getActualServiceTax()
    {
        $amount = $this->withdrawModel->actual_amounts - $this->getActualPoundage();

        return bcdiv(bcmul($amount,$this->withdrawModel->servicetax_rate,4),100,2);
    }



    /**
     * 错误消息
     * @param $msg
     * @return mixed
     */
    private function errorMessage($msg)
    {
        return $this->message($msg, yzWebUrl("finance.withdraw-detail.index", ['id' => $this->withdrawModel->id]),'error');
    }



    /**
     * 成功消息
     * @param $msg
     * @return mixed
     */
    private function successMessage($msg)
    {
        return $this->message($msg, yzWebUrl("finance.withdraw-detail.index", ['id' => $this->withdrawModel->id]));
    }







}
