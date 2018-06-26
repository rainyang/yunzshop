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
use app\common\models\PayType;
use app\common\services\password\PasswordService;
use app\common\services\PayFactory;
use app\common\services\Session;
use app\frontend\models\Member;
use app\frontend\modules\order\OrderCollection;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\orderPay\models\PreOrderPay;
use app\frontend\modules\payment\orderPayments\BasePayment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MergePayController extends ApiController
{
    public $transactionActions = ['*'];
    /**
     * @var OrderCollection
     */
    private $orders;
    protected $publicAction = ['alipay'];
    protected $ignoreAction = ['alipay'];


    /**
     * 获取支付按钮列表接口
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function index()
    {
        // 验证
        $this->validate([
            'order_ids' => 'required'
        ]);

        // 订单集合
        $orders = $this->orders(request()->input('order_ids'));

        // 用户余额
        $member = $orders->first()->belongsToMember()->select(['credit2'])->first()->toArray();

        // 生成支付记录 记录订单号,支付金额,用户,支付号
        $orderPay = new PreOrderPay();
        $orderPay->setOrders($orders);
        $orderPay->store();

        // 支付类型
        $buttons = $this->getPayTypeButtons($orderPay);

        $data = ['order_pay' => $orderPay, 'member' => $member, 'buttons' => $buttons, 'typename' => ''];
        return $this->successJson('成功', $data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function anotherPayOrder()
    {
        $this->validate([
            'order_ids' => 'required',
            'pid' => 'required'
        ]);

        // 订单集合
        $orders = $this->orders(request()->input('order_ids'));

        // 生成支付记录 记录订单号,支付金额,用户,支付号
        $orderPay = new PreOrderPay();
        $orderPay->setOrders($orders);
        $orderPay->store();

        // 支付类型
        $buttons = $this->getPayTypeButtons($orderPay);

        // todo bad taste
        $type = \YunShop::request()->type ?: 0;
        $buttons = collect($buttons)->filter(function ($value, $key) use ($type) {
            if ($value['name'] != '找人代付') {
                return $value;
            }
        });

        $member = Member::getMemberById(request()->input('pid'));

        $data = ['order_pay' => $orderPay, 'member' => $member, 'buttons' => $buttons, 'typename' => ''];

        return $this->successJson('成功', $data);
    }

    /**
     * 支付的时候,生成支付记录的时候,通过订单ids获取订单集合
     * @param $orderIds
     * @return OrderCollection
     * @throws AppException
     */
    private function orders($orderIds)
    {
        if (!is_array($orderIds)) {
            $orderIds = explode(',', $orderIds);
        }
        array_walk($orderIds, function ($orderId) {
            if (!is_numeric($orderId)) {
                throw new AppException('(ID:' . $orderId . ')订单号id必须为数字');
            }
        });

        $this->orders = OrderCollection::make(Order::select(['status', 'id', 'order_sn', 'price', 'uid'])->whereIn('id', $orderIds)->get());

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
     * 通过事件获取支付按钮
     * @param \app\frontend\models\OrderPay $orderPay
     * @return static
     */
    private function getPayTypeButtons(\app\frontend\models\OrderPay $orderPay)
    {
        // 获取可用的支付方式
        $result = $orderPay->getPaymentTypes()->map(function (BasePayment $paymentType) {
            //格式化数据结构
            return [
                'name' => $paymentType->getName(),
                'value' => $paymentType->getId(),
                'need_password' => $paymentType->needPassword(),
            ];
        });

        return $result;
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
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function alipay()
    {
        if (\Setting::get('shop.pay.alipay') == false) {
            throw new AppException('商城未开启支付宝支付');
        }
        if (request()->has('uid')) {
            Session::set('member_id', request()->query('uid'));
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
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function wechatAppPay()
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
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function alipayAppRay()
    {
        if (\Setting::get('shop_app.pay.alipay') == false) {
            throw new AppException('商城未开启支付宝支付');
        }
        if (request()->has('uid')) {
            Session::set('member_id', request()->query('uid'));
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
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function cloudWechatPay()
    {
        if (\Setting::get('plugin.cloud_pay_set') == false) {
            throw new AppException('商城未开启微信支付');
        }
        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::PAY_CLOUD_WEACHAT);
        return $this->successJson('成功', $data);
    }

    /**
     * 芸支付
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function yunPayWechat()
    {
        if (\Setting::get('plugin.yun_pay_set') == false) {
            throw new AppException('商城未开启芸支付');
        }

        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::PAY_YUN_WEACHAT);
        return $this->successJson('成功', $data);
    }

    /**
     * 支付宝云支付
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
        $data = $orderPay->getPayResult(PayFactory::PAY_CLOUD_ALIPAY, ['pay' => 'cloud_alipay']);
        return $this->successJson('成功', $data);
    }

    /**
     * 找人代付
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function anotherPay()
    {
        if (\Setting::get('another_pay_set') == false) {
            throw new AppException('商城未开启支付宝支付');
        }

        return $this->successJson('成功', []);
    }


    /**
     * 支付宝—YZ
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function yunPayAlipay()
    {
        if (\Setting::get('plugin.yun_pay_set') == false) {
            throw new AppException('商城未开启芸支付');
        }
        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::PAY_YUN_WEACHAT, ['pay' => 'alipay']);
        return $this->successJson('成功', $data);
    }

    /**
     * 货到付款
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function COD()
    {
        $this->validate([
            'order_pay_id' => 'required|integer'
        ]);
        if (\Setting::get('shop.pay.COD') == false) {
            throw new AppException('商城未开启货到付款');
        }
        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $orderPay->getPayResult(PayFactory::PAY_COD);
        $orderPay->pay();
        $trade = \Setting::get('shop.trade');
        $redirect = '';

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            $redirect = $trade['redirect_url'];
        }

        return $this->successJson('成功', ['redirect' => $redirect]);
    }

    /**
     * 货到付款
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function remittance()
    {
        $this->validate([
            'order_pay_id' => 'required|integer'
        ]);

        if (\Setting::get('shop.pay.remittance') == false) {
            throw new AppException('商城未开启转账付款');
        }
        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));

        $data = $orderPay->getPayResult(PayType::REMITTANCE);

        // todo data怎么传
        $orderPay->applyPay();

        $orderPay->save();
        $trade = \Setting::get('shop.trade');
        $redirect = '';

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            $redirect = $trade['redirect_url'];
        }
        $data['redirect'] = $redirect;
        return $this->successJson('成功', $data);
    }
}