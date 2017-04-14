<?php

namespace app\frontend\modules\refund\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\refund\RefundApply;
use app\frontend\modules\order\models\Order;
use app\frontend\modules\refund\services\RefundService;
use Request;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/12
 * Time: 下午4:24
 */
class ApplyController extends ApiController
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer'
        ]);
        $order = Order::find($request->query('order_id'));
        if (!isset($order)) {
            throw new AppException('订单不存在');
        }
        $reasons = [
            '不想要了',
            '卖家缺货',
            '拍错了/订单信息错误',
            '其他',
        ];
        $refundTypes = [
            [
                'name' => '退款(仅退款不退货)',
                'value' => 0
            ], [
                'name' => '退款退货',
                'value' => 1
            ], [
                'name' => '换货',
                'value' => 2
            ]
        ];
        $data = compact('order', 'refundTypes', 'reasons');
        return $this->successJson('成功', $data);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'reason' => 'required|string',
            'content' => 'sometimes|string',
            'images' => 'sometimes|filled|json',
            'refund_type' => 'required|integer',
            'order_id' => 'required|integer'
        ], [
            'images.json' => 'images非json格式'
        ]);

        $order = Order::find($request->input('order_id'));
        if (!isset($order)) {
            throw new AppException('订单不存在');
        }
        if ($order->uid != \YunShop::app()->getMemberId()) {
            throw new AppException('无效申请,该订单属于其他用户');
        }
        if ($order < Order::WAIT_SEND) {
            throw new AppException('订单未付款,无法退款');
        }
        if (RefundApply::where('order_id', $request->input('order_id'))->count()) {
            throw new AppException('申请已提交,处理中');
        }
        $refundApply = new RefundApply($request->only(['reason', 'content', 'refund_type', 'order_id']));
        $refundApply->images = $request->input('images',[]);
        $refundApply->content = $request->input('content','');
        $refundApply->price = $order->price;
        $refundApply->refund_sn = RefundService::createOrderRN();
        $refundApply->create_time = time();
        if (!$refundApply->save()) {
            throw new AppException('请求失败');
        }

        return $this->successJson('成功', $refundApply->toArray());
    }
}