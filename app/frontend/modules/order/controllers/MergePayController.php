<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/25
 * Time: 上午11:00
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\Order;
use app\common\services\PayFactory;
use app\common\services\Session;
use Illuminate\Support\Collection;

class MergePayController extends ApiController
{
    /**
     * @var Collection
     */
    protected $orders;
    protected $publicAction = ['alipay'];
    protected $ignoreAction = ['alipay'];

    /**
     * @param $order_ids
     * @return Collection
     * @throws AppException
     */
    protected function orders($order_ids)
    {
        if (isset($this->orders)) {
            return $this->orders;
        }
        $this->orders = Order::select(['status', 'id', 'order_sn', 'price', 'uid'])->whereIn('id', $order_ids)->get();
        if ($this->orders->count() != count($order_ids)) {
            throw new AppException('(ID:' . $order_ids . ')未找到订单');
        }
        $this->orders->each(function ($order) {
            if ($order->status > Order::WAIT_PAY) {
                throw new AppException('(ID:'.$order->id.')订单已付款,请勿重复付款');
            }
            if ($order->status == Order::CLOSE) {
                throw new AppException('(ID:'.$order->id.')订单已关闭,无法付款');
            }
            if ($order->uid != \YunShop::app()->getMemberId()) {
                throw new AppException('该订单属于其他用户');
            }
        });

        return $this->orders;
    }

    public function index(\Request $request)
    {
        $this->validate($request, [
            'order_ids' => 'required|string'
        ]);
        $orders = $this->orders($request['order_ids']);

        $member = $orders->first()->belongsToMember()->select(['credit2'])->first()->toArray();
        $price = $orders->sum('price');
        if ($price <= 0) {
            throw new AppException('('.$price.')订单金额有误');

        }
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
        $data = ['price' => $price, 'member' => $member, 'buttons' => $buttons];

        return $this->successJson('成功', $data);
    }

    protected function _validate($request)
    {
        $this->validate($request, [
            'order_ids' => 'required|string'
        ]);





    }

    protected function pay($request, $payType)
    {
        $this->validate($request, [
            'order_ids' => 'required|string'
        ]);
        $orders = $this->orders($request['order_ids']);
        //为订单设置pay_sn
        $query_str = [
            'order_no' => $pay_sn,
            'amount' => $orders->sum('price'),
            'subject' => '微信支付',
            'body' => $order->hasManyOrderGoods[0]->title . ':' . \YunShop::app()->uniacid,
            'extra' => ['type' => 1]

        ];

        $pay = PayFactory::create($payType);
        //如果支付模块常量改变 数据会受影响
        $order->pay_type_id = $payType;

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