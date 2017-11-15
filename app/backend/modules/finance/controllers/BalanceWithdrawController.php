<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/14
 * Time: 下午5:06
 */

namespace app\backend\modules\finance\controllers;


use app\common\exceptions\AppException;
use app\common\helpers\Url;
use app\common\models\Withdraw;
use app\backend\modules\finance\services\WithdrawService;
use app\common\components\BaseController;
use app\common\services\finance\BalanceNoticeService;
use Illuminate\Support\Facades\Log;

class BalanceWithdrawController extends BaseController
{
    private $withdrawModel;


    /**
     * 提现详情
     * @return mixed|string
     */
    public function detail()
    {
        $this->withdrawModel = $this->attachedMode();
        return view('finance.balance.withdraw', ['item' => $this->withdrawModel->toArray(),])->render();
    }


    /**
     * 提现审核、打款
     * @return mixed
     */
    public function examine()
    {
        $requestData = \YunShop::request();
        $this->withdrawModel = $this->attachedMode();

        //审核、重新审核
        if (isset($requestData['submit_check'])) {
            return $this->submitCheck();
        }

        //重新审核
        if (isset($requestData['submit_cancel'])) {
            return $this->submitCancel();
        }

        //打款
        if (isset($requestData['submit_pay'])) {
            $result = $this->submitPay();

            if (is_bool($result) && $result) {
                redirect(Url::web('finance.balance-withdraw.detail', ['id'=>\YunShop::request()->id]))->send();
            } else {
                return $result;
            }
        }

        return $this->message('提交数据有误，请刷新重试', yzWebUrl("finance.balance-withdraw.detail", ['id' => $this->getPostId()]));
    }


    /**
     * 提现审核
     * @return mixed
     */
    private function submitCheck()
    {
        $this->withdrawModel->status = $this->getPostStatus();
        $this->withdrawModel->audit_at = time();

        $this->withdrawUpdate();
        return $this->message('提交审核成功', yzWebUrl("finance.balance-withdraw.detail", ['id' => $this->getPostId()]));
    }



    /**
     * 提现重新审核
     * @return mixed
     */
    private function submitCancel()
    {
        BalanceNoticeService::withdrawFailureNotice($this->withdrawModel);

        return $this->submitCheck();
    }



    /**
     * 提现打款
     * @return mixed
     * @throws AppException
     */
    private function submitPay()
    {
        if ($this->withdrawModel->status !== 1) {
            throw new AppException('打款失败,数据不存在或不符合打款规则!');
        }

        $this->withdrawModel->pay_at = time();
        $this->withdrawModel->status = 2;

        $this->withdrawUpdate();
        return $this->payment();
    }



    /**
     * 提现 model 数据保存
     * @return bool
     * @throws AppException
     */
    private function withdrawUpdate()
    {
        if (!$this->withdrawModel->save()) {
            throw new AppException('数据修改失败，请刷新重试');
        }
        return true;
    }


    /**
     * 提现打款，区分打款方式
     * @return mixed
     * @throws AppException
     */
    private function payment()
    {
        switch ($this->withdrawModel->pay_way) {
            case 'alipay':
                return $this->alipayPayment($this->paymentRemark());
                break;
            case 'wechat':
                return $this->wechatPayment();
                break;
            case 'manual':
                return $this->manualPayment();
                break;
            default:
                throw new AppException('未知打款方式！！！');
        }
    }



    /**
     * 打款日志（备注）
     * @return string
     */
    private function paymentRemark()
    {
        return $remark = '提现打款-' . $this->withdrawModel->type_name . '-金额:' . $this->withdrawModel->actual_amounts . '元,' . '手续费:' . $this->withdrawModel->actual_poundage;
    }



    /**
     * 支付宝打款
     * @param $remark
     * @param null $withdraw
     */
    private function alipayPayment($remark, $withdraw = null)
    {
        if (!is_null($withdraw)) {
            $result = WithdrawService::alipayWithdrawPay($withdraw, $remark);
        } else {
            $result = WithdrawService::alipayWithdrawPay($this->withdrawModel, $remark);
        }

        Log::info('MemberId:' . $this->withdrawModel->member_id . ', ' . $remark . "支付宝打款中!");
        if ($result !== true){
            return $this->paymentError($result['message']);
        }
        return $result;
    }



    /**
     * 微信打款
     * @return mixed
     */
    private function wechatPayment()
    {
        $result = WithdrawService::wechatWithdrawPay($this->withdrawModel, $this->paymentRemark());
        //file_put_contents(storage_path('logs/withdraw1.log'),print_r($resultPay,true));
        Log::info('MemberId:' . $this->withdrawModel->member_id . ', ' . $this->paymentRemark() . "微信打款中!");

        if ($result !== true){
            return $this->paymentError($result['message']);
        }
        return $this->paymentSuccess();
    }


    /**
     * 手动打款
     * @return mixed
     */
    private function manualPayment()
    {
        return $this->paymentSuccess();
    }



    /**
     * 打款成功
     * @return mixed
     */
    private function paymentSuccess()
    {
        return $this->message('打款成功', yzWebUrl("finance.balance-withdraw.detail", ['id' => $this->getPostId()]));
    }


    /**
     * 打款失败
     * @param string $message
     * @throws AppException
     */
    private function paymentError($message = '')
    {
        $this->withdrawModel->status = 1;
        $this->withdrawUpdate();

        throw new AppException($message ?: '打款失败，请重试');
    }


    /**
     * 附值打款 model
     * @return mixed
     * @throws AppException
     */
    private function attachedMode()
    {
        $result = Withdraw::getBalanceWithdrawById($this->getPostId());

        if (!$result) {
            throw new AppException('数据错误，请刷新重试');
        }
        return $result;
    }


    /**
     * @return string
     */
    private function getPostId()
    {
        return trim(\YunShop::request()->id);
    }


    /**
     * @return string
     */
    private function getPostStatus()
    {
        return trim(\YunShop::request()->status);
    }


    /**
     * 丁冉增加批量打款
     * @return mixed
     */
    public function batchAlipay()
    {
        $ids = \YunShop::request()->ids;

        $withdrawId = explode(',', $ids);

        $withdraw = [];
        if (!empty($withdrawId)) {
            foreach ($withdrawId as $id) {
                $withdraw_modle = Withdraw::getBalanceWithdrawById($id);

                if (!is_null($withdraw_modle)) {
                    if ($withdraw_modle->status != '1') {
                        return $this->message('打款失败,数据不存在或不符合打款规则!', yzWebUrl("finance.balance-withdraw.detail", ['id' => $id]), 'error');
                    }

                    $withdraw[] = $withdraw_modle;

                    $remark[] = '提现打款-' . $withdraw_modle->type_name . '-金额:' . $withdraw_modle->actual_amounts . '元,' .
                        '手续费:' . $withdraw_modle->actual_poundage;
                }
            }

            $remark = json_encode($remark);

            $this->alipayPayment($remark, $withdraw);
        }
    }
}
