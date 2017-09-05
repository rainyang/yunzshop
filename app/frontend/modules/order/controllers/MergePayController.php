<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/25
 * Time: 上午11:00
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\exceptions\AppException;
use app\common\models\Order;
use app\common\models\OrderPay;
use app\common\services\PayFactory;
use app\common\services\Session;
use app\frontend\modules\order\services\OrderService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MergePayController extends ApiController
{
    public $transactionActions = ['wechatPay', 'alipay'];
    /**
     * @var Collection
     */
    protected $orders;
    protected $orderPay;//todo 临时解决,后续需要重构
    protected $publicAction = ['alipay'];
    protected $ignoreAction = ['alipay'];

    /**
     * @param $orderIds
     * @return Collection
     * @throws AppException
     */
    protected function orders($orderIds)
    {
        if (!is_array($orderIds)) {
            $orderIds = explode(',', $orderIds);
        }
        array_walk($orderIds, function ($orderId) {
            if (!is_numeric($orderId)) {
                throw new AppException('(ID:' . $orderId . ')订单号id必须为数字');
            }
        });

        $this->orders = Order::select(['status', 'id', 'order_sn', 'price', 'uid'])->whereIn('id', $orderIds)->get();

        if ($this->orders->count() != count($orderIds)) {
            throw new AppException('(ID:' . implode(',', $orderIds) . ')未找到订单');
        }
        $this->orders->each(function ($order) {
            if ($order->status > Order::WAIT_PAY) {
                throw new AppException('(ID:' . $order->id . ')订单已付款,请勿重复付款');
            }
            if ($order->status == Order::CLOSE) {
                throw new AppException('(ID:' . $order->id . ')订单已关闭,无法付款');
            }
            if ($order->uid != \YunShop::app()->getMemberId()) {
                throw new AppException('(ID:' . $order->id . ')该订单属于其他用户');
            }
        });

        return $this->orders;
    }

    public function index(\Request $request)
    {
        $this->validate([
            'order_ids' => 'required|string'
        ]);
        $orders = $this->orders($request->input('order_ids'));

        $member = $orders->first()->belongsToMember()->select(['credit2'])->first()->toArray();
        if ($orders->sum('price') < 0) {
            throw new AppException('(' . $orders->sum('price') . ')订单金额有误');
        }
        $buttons = $this->getPayTypeButtons();

        $orderPay = new OrderPay();
        $orderPay->order_ids = explode(',', $request->input('order_ids'));
        $orderPay->amount = $orders->sum('price');
        $orderPay->uid = $orders->first()->uid;
        $orderPay->pay_sn = OrderService::createPaySN();
        $orderPayId = $orderPay->save();
        if (!$orderPayId) {
            throw new AppException('支付流水记录保存失败');
        }

        $data = ['order_pay' => $orderPay, 'member' => $member, 'buttons' => $buttons, 'typename' => '支付'];

        return $this->successJson('成功', $data);
    }

    private function getPayTypeButtons()
    {
        $event = new GetOrderPaymentTypeEvent($this->orders);
        event($event);
        $result = $event->getData();
        return $result ? $result : [];
    }

    protected function pay($payType)
    {
        $this->validate([
            'order_pay_id' => 'required|integer'
        ]);

        $this->orderPay = $orderPay = OrderPay::find(request()->input('order_pay_id'));
        if (!isset($orderPay)) {
            throw new AppException('(ID' . request()->input('order_pay_id') . ')支付流水记录不存在');
        }
        if ($orderPay->status > 0) {
            throw new AppException('(ID' . request()->input('order_pay_id') . '),此流水号已支付');
        }

        $orders = $this->orders($orderPay->order_ids);
        //支付流水号
        $orderPay->pay_type_id = $payType;
        $orderPay->save();
        //订单支付方式,流水号保存
        $orders->each(function ($order) use ($orderPay) {
            $order->pay_type_id = $orderPay->pay_type_id;
            $order->order_pay_id = $orderPay->id;
            if (!$order->save()) {
                throw new AppException('支付方式选择失败');
            }
        });
        return $this->getPayResult($payType,$orderPay,$orders);
    }
    protected function getPayResult($payType,$orderPay,$orders){
        $query_str = $this->getPayParams($orderPay, $orders);

        $pay = PayFactory::create($payType);
        //如果支付模块常量改变 数据会受影响

        $result = $pay->doPay($query_str);
        if (!isset($result)) {
            throw new AppException('获取支付参数失败');
        }
        return $result;
    }
    protected function getPayParams($orderPay, Collection $orders)
    {
        return [
            'order_no' => $orderPay->pay_sn,
            'amount' => $orderPay->amount,
            'subject' => $orders->first()->hasManyOrderGoods[0]->title ?: '芸众商品',
            'body' => ($orders->first()->hasManyOrderGoods[0]->title ?: '芸众商品') . ':' . \YunShop::app()->uniacid,
            'extra' => ['type' => 1]
        ];
    }

    public function wechatPay(\Request $request)
    {
        if (\Setting::get('shop.pay.weixin') == false) {
            throw new AppException('商城未开启微信支付');
        }
        $data = $this->pay( PayFactory::PAY_WEACHAT);
        $data['js'] = json_decode($data['js'], 1);
        return $this->successJson('成功', $data);
    }

    public function alipay(\Request $request)
    {
        if (\Setting::get('shop.pay.alipay') == false) {
            throw new AppException('商城未开启支付宝支付');
        }
        if ($request->has('uid')) {
            Session::set('member_id', $request->query('uid'));
        }
        $data = $this->pay( PayFactory::PAY_ALIPAY);
        return $this->successJson('成功', $data);
    }

    public function cloudWechatPay(\Request $request)
    {
        if (\Setting::get('plugin.cloud_pay_set') == false) {
            throw new AppException('商城未开启支付宝支付');
        }

        $data = $this->pay( PayFactory::PAY_CLOUD_WEACHAT);
        return $this->successJson('成功', $data);
    }
}