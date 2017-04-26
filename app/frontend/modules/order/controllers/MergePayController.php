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
use app\common\models\OrderPay;
use app\common\services\PayFactory;
use app\common\services\Session;
use app\frontend\modules\order\services\OrderService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MergePayController extends ApiController
{
    /**
     * @var Collection
     */
    protected $orders;
    protected $orderPay;//todo 临时解决,后续需要重构
    protected $publicAction = ['alipay'];
    protected $ignoreAction = ['alipay'];

    /**
     * @param $order_ids
     * @return Collection
     * @throws AppException
     */
    protected function orders($orderIds)
    {
        //$orderIds = explode(',', $order_ids);
        array_walk($orderIds, function ($orderId) {
            if (!is_numeric($orderId)) {
                throw new AppException('(ID:' . $orderId . ')订单号id必须为数字');
            }
        });

        $this->orders = Order::select(['status', 'id', 'order_sn', 'price', 'uid'])->whereIn('id', $orderIds)->get();

        if ($this->orders->count() != count($orderIds)) {
            throw new AppException('(ID:' . implode(',',$orderIds) . ')未找到订单');
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
        $this->validate($request, [
            'order_ids' => 'required|string'
        ]);
        $orders = $this->orders($request->input('order_ids'));

        $member = $orders->first()->belongsToMember()->select(['credit2'])->first()->toArray();
        if ($orders->sum('price') <= 0) {
            throw new AppException('(' . $orders->sum('price') . ')订单金额有误');
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

        $orderPay = new OrderPay();
        $orderPay->order_ids = explode(',', $request->input('order_ids'));
        $orderPay->amount = $orders->sum('price');
        $orderPay->uid = $orders->first()->uid;
        $orderPay->pay_sn = OrderService::createPaySN();
        $orderPay->save();
        if (!$orderPay) {
            throw new AppException('支付流水记录保存失败');
        }
        $data = ['order_pay' => $orderPay, 'member' => $member, 'buttons' => $buttons];

        return $this->successJson('成功', $data);
    }


    protected function pay($request, $payType)
    {
        $this->validate($request, [
            'order_pay_id' => 'required|integer'
        ]);
        $this->orderPay = $orderPay = OrderPay::find($request->input('order_pay_id'));
        if (!isset($orderPay)) {
            throw new AppException('(ID' . $request->input('order_pay_id') . ')支付流水记录不存在');
        }
        if ($orderPay->status > 0) {
            throw new AppException('(ID' . $request->input('order_pay_id') . '),此流水号已支付');
        }
        $result = DB::transaction(function () use ($orderPay, $payType) {

            $orders = $this->orders($orderPay->order_ids);
            //支付流水号
            $orderPay->pay_type_id = $payType;
            $orderPay->save();
            //订单支付方式
            $orders->each(function ($order) use ($payType) {
                $order->pay_type_id = $payType;
                if (!$order->save()) {
                    throw new AppException('支付方式选择失败');
                }
            });

            $query_str = $this->getPayParams($orderPay, $orders);
            $pay = PayFactory::create($payType);
            //如果支付模块常量改变 数据会受影响

            $result = $pay->doPay($query_str);
            if (!isset($result)) {
                throw new AppException('获取支付参数失败');
            }
            return $result;
        });

        return $result;

    }

    protected function getPayParams($orderPay, Collection $orders)
    {
        return [
            'order_no' => $orderPay->pay_sn,
            'amount' => $orderPay->amount,
            'subject' => '微信支付',
            'body' => $orders->first()->hasManyOrderGoods[0]->title . ':' . \YunShop::app()->uniacid,
            'extra' => ['type' => 1]

        ];
    }

    public function wechatPay(\Request $request)
    {
        $data = $this->pay($request, PayFactory::PAY_WEACHAT);
        $data['js'] = json_decode($data['js'], 1);
        return $this->successJson('成功', $data);
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