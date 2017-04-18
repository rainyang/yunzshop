<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/9
 * Time: 上午9:38
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\Order;
use app\common\models\PayType;
use app\common\services\PayFactory;
use app\common\services\Session;
use app\frontend\modules\order\services\OrderService;
use Ixudra\Curl\Facades\Curl;

class PayController extends ApiController
{
    protected $order;
    protected $publicAction = ['alipay'];
    protected $ignoreAction = ['alipay'];

    protected function order()
    {
        if (isset($this->order)) {
            return $this->order;
        }
        return $this->order = Order::select(['status', 'id', 'order_sn', 'price', 'uid'])->find(\Request::query('order_id'));
    }

    public function index(\Request $request)
    {
        $order = $this->order();
        if (!isset($order)) {
            throw new AppException('未找到订单');
        }
        if ($order->status != Order::WAIT_PAY) {
            throw new AppException('订单已付款');
        }
        $member = $order->belongsToMember()->select(['credit2'])->first()->toArray();
        $buttons = [
            [
                'name' => '余额支付',
                'value' => '3'
            ],
            [
                'name' => '微信支付',
                'value' => '1'
            ], [
                'name' => '支付宝支付',
                'value' => '2'
            ],
        ];
        $data = ['order' => $order, 'member' => $member, 'buttons' => $buttons];

        return $this->successJson('成功', $data);
    }

    protected function _validate($request)
    {
        $this->validate($request, [
            'order_id' => 'required|integer'
        ]);
        $order = $this->order();
//        dd($request->query('order_id'));
//        exit;
        if (!isset($order)) {
            throw new AppException('订单不存在');
        }
        if ($order->uid != \YunShop::app()->getMemberId()) {
            throw new AppException('该订单属于其他用户');
        }
        if ($order->status > Order::WAIT_PAY) {
            throw new AppException('订单已付款,请勿重复付款');
        }
        if ($order->status == Order::CLOSE) {
            throw new AppException('订单已关闭,无法付款');
        }
    }

    protected function pay($request, $payType)
    {
        $this->_validate($request);
        $order = $this->order();

        $query_str = [
            'order_no' => $order->order_sn,
            'amount' => $order->price,
            'subject' => '微信支付',
            'body' => $order->hasManyOrderGoods[0]->title . ':' . \YunShop::app()->uniacid,
            'extra' => ['type' => 1]

        ];

        $pay = PayFactory::create($payType);
        $order->pay_type_id = PayType::ONLINE;

        $result = $pay->doPay($query_str);
        if (!isset($result)) {
            throw new AppException('获取支付参数失败');
        }

        if (!$order->save()) {
            throw new AppException('支付方式选择失败');
        }
        return $result;

    }

    public function wechatPay(\Request $request)
    {
        $data = $this->pay($request, PayFactory::PAY_WEACHAT);
        $data['js'] = json_decode($data['js'], 1);
        return $this->successJson('成功', $data);
        //return $this->
        //return view('order.pay', $data)->render();
    }

    public function alipay(\Request $request)
    {
        if ($request->has('uid')) {
            Session::set('member_id', $request->query('uid'));
        }
        $data = $this->pay($request, PayFactory::PAY_ALIPAY);
        return $this->successJson('成功', $data);

        //获取支付宝 支付单 数据
    }

}