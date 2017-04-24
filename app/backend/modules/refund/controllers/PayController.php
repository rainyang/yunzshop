<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/21
 * Time: 下午6:16
 */

namespace app\backend\modules\refund\controllers;

use app\backend\modules\refund\models\RefundApply;
use app\backend\modules\refund\services\RefundOperationService;
use app\common\components\BaseController;
use app\common\exceptions\AdminException;
use app\common\models\PayType;
use app\common\services\PayFactory;

class PayController extends BaseController
{
    private $refundApply;
    /**
     * 退款
     * @param \Request $request
     * @return mixed
     * @throws AdminException
     */
    public function index(\Request $request)
    {
        $this->validate($request, [
            'refund_id' => 'required'
        ]);
        //dd($request->query('refund_id'));
        //exit;
        /**
         * @var $this->refundApply RefundApply
         */
        $this->refundApply = RefundApply::find($request->query('refund_id'));
        if (!isset($this->refundApply)) {
            throw new AdminException('未找到退款记录');
        }
        if ($this->refundApply->status != RefundApply::WAIT_REFUND) {
            throw new AdminException($this->refundApply->status_name . '的退款申请,无法执行' . '打款' . '操作');
        }
        if(!is_numeric($this->refundApply->order->pay_type_id)){
            throw new AdminException($this->refundApply->id . '获取支付方式失败');

        }
        //dd($this->refundApply->order);
        //exit;
        $pay = PayFactory::create($this->refundApply->order->pay_type_id);

        $result = $pay->doRefund($this->refundApply->order->order_sn, $this->refundApply->order->price, $this->refundApply->order->price);

        if (!$result) {
            $this->error('操作失败');
        }

        switch ($this->refundApply->order->pay_type_id){
            case PayType::WECHAT_PAY:
                $this->wechat();
                break;

            case PayType::ALIPAY:
                $this->alipay();
                break;

            case PayType::CREDIT:
                $this->balance();
                break;

            default:
                break;
        }
        return $this->message('操作成功');

    }

    private function wechat()
    {
        if ($this->refundApply->order->pay_type_id == PayType::WECHAT_PAY) {
            $result = RefundOperationService::refundComplete(['order_id', $this->refundApply->order->id]);

        }
    }

    private function alipay()
    {

    }

    private function balance()
    {

    }
}