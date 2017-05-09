<?php

namespace app\backend\modules\refund\controllers;

use app\backend\modules\refund\models\RefundApply;
use app\common\components\BaseController;
use app\common\exceptions\AdminException;
use app\frontend\modules\order\services\OrderService;
use function foo\func;
use Illuminate\Support\Facades\DB;

/**
 * 退款申请操作
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/13
 * Time: 下午3:05
 */
class OperationController extends BaseController
{
    /**
     * @var $refundApply RefundApply
     */
    private $refundApply;

    public function __construct()
    {
        parent::__construct();
        $request = \Request::capture();
        $this->validate($request, [
            'refund_id' => 'required',
            //'reject_reason'=>''
        ]);
        $this->refundApply = RefundApply::find($request->input('refund_id'));
        if (!isset($this->refundApply)) {
            throw new AdminException('退款记录不存在');
        }
    }

    /**
     * 拒绝
     * @param \Request $request
     * @return mixed
     */
    public function reject(\Request $request)
    {
        $this->refundApply->reject($request->only(['reject_reason']));
        return $this->message('操作成功', '');
    }

    /**
     * 同意
     * @param \Request $request
     * @return mixed
     */
    public function pass(\Request $request)
    {
        $this->refundApply->pass();
        return $this->message('操作成功', '');
    }

    public function receiveReturnGoods(\Request $request)
    {
        $this->refundApply->receiveReturnGoods();
        return $this->message('操作成功', '');
    }

    public function resend()
    {

    }

    /**
     * 手动退款
     * @param \Request $request
     * @return mixed
     */
    public function consensus(\Request $request)
    {
        $refundApply = $this->refundApply;
        DB::transaction(function () use ($refundApply) {
            $refundApply->consensus();
            $refundApply->order->close();
        });
        return $this->message('操作成功', '');
    }
}