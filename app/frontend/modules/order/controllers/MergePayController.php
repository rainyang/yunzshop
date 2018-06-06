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
use app\common\services\password\PasswordService;
use app\common\services\PayFactory;
use app\common\services\Session;
use app\frontend\models\Member;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\payment\orderPayments\BasePayment;
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
     * 支付的时候,生成支付记录的时候,通过订单ids获取订单集合
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

            //找人代付
            if ($order->uid != \YunShop::app()->getMemberId() && !Member::getPid()) {
                throw new AppException('(ID:' . $order->id . ')该订单属于其他用户');
            }
        });
        // 订单金额验证
        if ($this->orders->sum('price') < 0) {
            throw new AppException('(' . $this->orders->sum('price') . ')订单金额有误');
        }
        return $this->orders;
    }

    /**
     * 获取支付按钮列表接口
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function index(\Request $request)
    {
        // 验证
        $this->validate([
            //'order_ids' => 'required|string'  //todo, string就会报错,奇怪...
            'order_ids' => 'required'
        ]);
        // 订单集合
        $orders = $this->orders($request->input('order_ids'));
        // 用户余额
        $member = $orders->first()->belongsToMember()->select(['credit2'])->first()->toArray();
        // 支付类型
        $buttons = $this->getPayTypeButtons($orders->first());

        // 生成支付记录 记录订单号,支付金额,用户,支付号
        $orderPay = new OrderPay();
        $orderPay->order_ids = explode(',', $request->input('order_ids'));
        $orderPay->amount = $orders->sum('price');
        $orderPay->uid = $orders->first()->uid;
        $orderPay->pay_sn = OrderService::createPaySN();
        $orderPayId = $orderPay->save();
        if (!$orderPayId) {
            throw new AppException('支付流水记录保存失败');
        }

        $data = ['order_pay' => $orderPay, 'member' => $member, 'buttons' => $buttons, 'typename' => ''];

        return $this->successJson('成功', $data);
    }

    /**
     * 通过事件获取支付按钮
     * @param $order
     * @return mixed
     */
    private function getPayTypeButtons(Order $order)
    {
        $order = Order::find($order->id);
        $paymentTypes = app('PaymentManager')->make('OrderPaymentTypeManager')->getOrderPaymentTypes($order);
        $result =  $paymentTypes->map(function (BasePayment $paymentType) {
            return [
                'name' => $paymentType->getName(),
                'value' => $paymentType->getId(),
                'need_password' => $paymentType->needPassword(),
            ];
        });
        //订单金额为0时只显示‘余额支付’按钮
        if ($order->price == 0) {
            unset($result[0]);
            unset($result[1]);
            unset($result[2]);
            unset($result[6]);
            unset($result[7]);
            unset($result[9]);
            unset($result[10]);
            unset($result[12]);
            unset($result[14]);
        }
        $type    = \YunShop::request()->type ?:0;
        if ($type == 2 && !empty($result[2])) {
            unset($result[2]);
        }
        return $result;
    }

    /**
     * 支付
     * @param $payType
     * @param array $payParams
     * @return \app\common\services\strin5|array|bool|mixed|string|void
     * @throws AppException
     * @throws \app\common\exceptions\ShopException
     */
    protected function pay($payType, $payParams=[])
    {
        $this->validate([
            'order_pay_id' => 'required|integer'
        ]);
        // 支付记录
        $this->orderPay = $orderPay = OrderPay::find(request()->input('order_pay_id'));
        if (!isset($orderPay)) {
            throw new AppException('(ID' . request()->input('order_pay_id') . ')支付流水记录不存在');
        }
        if ($orderPay->status > 0) {
            throw new AppException('(ID' . request()->input('order_pay_id') . '),此流水号已支付');
        }
        // 订单集合
        $orders = $this->orders($orderPay->order_ids);
        // 验证支付密码
        return $this->getPayResult($payType,$orderPay,$orders, $payParams);
    }

    /**
     * 支付结果
     * @param $payType
     * @param $orderPay
     * @param $orders
     * @param array $payParams
     * @return \app\common\services\strin5|array|bool|mixed|string|void
     * @throws AppException
     */
    protected function getPayResult($payType,$orderPay,$orders, $payParams=[]){
        $query_str = $this->getPayParams($orderPay, $orders, $payParams);
        $pay = PayFactory::create($payType);
        //如果支付模块常量改变 数据会受影响
        $result = $pay->doPay($query_str, $payType);

        if (!isset($result)) {
            throw new AppException('获取支付参数失败');
        }
        return $result;
    }

    /**
     * 拼写支付参数
     * @param $orderPay
     * @param Collection $orders
     * @param array $option
     * @return array
     * @throws AppException
     */
    protected function getPayParams($orderPay, Collection $orders, $option=[])
    {
        $extra = ['type' => 1];

        if (!is_array($option)) {
            throw new AppException('参数类型错误');
        }

        $extra   = array_merge($extra, $option);

        return [
            'order_no' => $orderPay->pay_sn,
            'amount' => $orderPay->amount,
            'subject' => $orders->first()->hasManyOrderGoods[0]->title ?: '芸众商品',
            'body' => ($orders->first()->hasManyOrderGoods[0]->title ?: '芸众商品') . ':' . \YunShop::app()->uniacid,
            'extra' => $extra
        ];
    }

    /**
     * 微信支付
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function wechatPay()
    {
        $this->validate([
            'order_pay_id' => 'required|integer'
        ]);
        if (\Setting::get('shop.pay.weixin') == false) {
            throw new AppException('商城未开启微信支付');
        }
        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::PAY_WEACHAT);
        $data['js'] = json_decode($data['js'], 1);

        $trade = \Setting::get('shop.trade');
        $redirect = '';

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            $redirect = $trade['redirect_url'];
        }

        $data['redirect'] = $redirect;

        return $this->successJson('成功', $data);
    }

    /**
     * 支付宝支付
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function alipay(\Request $request)
    {
        if (\Setting::get('shop.pay.alipay') == false) {
            throw new AppException('商城未开启支付宝支付');
        }
        if ($request->has('uid')) {
            Session::set('member_id', $request->query('uid'));
        }
        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::PAY_ALIPAY);
        return $this->successJson('成功', $data);
    }

    /**
     * 微信app支付
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function wechatAppPay(\Request $request)
    {
        if (\Setting::get('shop_app.pay.weixin') == false) {
            throw new AppException('商城未开启微信支付');
        }
        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::PAY_APP_WEACHAT);
        return $this->successJson('成功', $data);
    }

    /**
     * 支付宝app支付
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function alipayAppRay(\Request $request)
    {
        if (\Setting::get('shop_app.pay.alipay') == false) {
            throw new AppException('商城未开启支付宝支付');
        }
        if ($request->has('uid')) {
            Session::set('member_id', $request->query('uid'));
        }
        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data['payurl'] = $orderPay->getPayResult(PayFactory::PAY_APP_ALIPAY);
        $data['isnewalipay'] = \Setting::get('shop_app.pay.newalipay');
        return $this->successJson('成功', $data);
    }

    /**
     * 微信云支付
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function cloudWechatPay(\Request $request)
    {
        if (\Setting::get('plugin.cloud_pay_set') == false) {
            throw new AppException('商城未开启微信支付');
        }
        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data= $orderPay->getPayResult(PayFactory::PAY_CLOUD_WEACHAT);
        return $this->successJson('成功', $data);
    }

    /**
     * 芸支付
     *
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function yunPayWechat(\Request $request)
    {
        if (\Setting::get('plugin.yun_pay_set') == false) {
            throw new AppException('商城未开启芸支付');
        }

        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data= $orderPay->getPayResult(PayFactory::PAY_YUN_WEACHAT);
        return $this->successJson('成功', $data);
    }

    /**
     * 支付宝云支付
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function cloudAliPay()
    {
        if (\Setting::get('plugin.cloud_pay_set') == false) {
            throw new AppException('商城未开启支付宝支付');
        }

        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data= $orderPay->getPayResult(PayFactory::PAY_CLOUD_ALIPAY,['pay' => 'cloud_alipay']);
        return $this->successJson('成功', $data);
    }

    /**
     * 找人代付
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function anotherPay(\Request $request)
    {
        if (\Setting::get('another_pay_set') == false) {
            throw new AppException('商城未开启支付宝支付');
        }

        return $this->successJson('成功', []);
    }

    public function anotherPayOrder(\Request $request)
    {
        $this->validate([
            //'order_ids' => 'required|string'  //todo, string就会报错,奇怪...
            'order_ids' => 'required',
            'pid' => 'required'
        ]);

        // 订单集合
        $orders = $this->orders($request->input('order_ids'));

        if (is_null($orders)) {
            return $this->errorJson('订单不存在', '');
        }

        // 支付类型
        $buttons = $this->getPayTypeButtons($orders->first());
        $type    = \YunShop::request()->type ?:0;
        $buttons = collect($buttons)->filter(function ($value, $key) use ($type) {
            if ($value['name'] != '找人代付') {
                return $value;
            }
        });

        if ($type == 2 && !empty($buttons[2])) {
            unset($buttons[2]);
        }

        $member = Member::getMemberById($request->input('pid'));

        // 生成支付记录 记录订单号,支付金额,用户,支付号
        $orderPay = new OrderPay();
        $orderPay->order_ids = explode(',', $request->input('order_ids'));
        $orderPay->amount = $orders->sum('price');
        $orderPay->uid = $orders->first()->uid;
        $orderPay->pay_sn = OrderService::createPaySN();
        $orderPayId = $orderPay->save();

        if (!$orderPayId) {
            throw new AppException('支付流水记录保存失败');
        }

        $data = ['order_pay' => $orderPay, 'member' => $member, 'buttons' => $buttons, 'typename' => ''];

        return $this->successJson('成功', $data);
    }

    /**
     * 支付宝—YZ
     *
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function yunPayAlipay(\Request $request)
    {
        if (\Setting::get('plugin.yun_pay_set') == false) {
            throw new AppException('商城未开启芸支付');
        }

        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data= $orderPay->getPayResult(PayFactory::PAY_YUN_WEACHAT,['pay' => 'alipay']);
        return $this->successJson('成功', $data);
    }
}