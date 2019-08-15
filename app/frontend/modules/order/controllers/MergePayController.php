<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/25
 * Time: 上午11:00
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\events\order\BeforeOrderPayEvent;
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
use app\common\helpers\Url;

class MergePayController extends ApiController
{
    public $transactionActions = ['*'];
    /**
     * @var OrderCollection
     */
    protected $orders;
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
        return $this->orders;
    }

    /**
     * 通过事件获取支付按钮
     * @param \app\frontend\models\OrderPay $orderPay
     * @return Collection
     */
    private function getPayTypeButtons(\app\frontend\models\OrderPay $orderPay)
    {
        // 获取可用的支付方式
        $result = $orderPay->getPaymentTypes()->map(function (BasePayment $paymentType) {

            //余额
            if($paymentType->getCode()  == 'balance'){
                if($paymentType->getName() !== \Setting::get('shop.shop.credit')){
                      $names = \Setting::get('shop.shop.credit');
                }
            }
            //预存款
            if($paymentType->getCode()  == 'DepositPay'){
                if(app('plugins')->isEnabled('team-rewards'))
                {
                    $names =  TEAM_REWARDS_DEPOSIT.'支付';
                }
            }
                return [
                    'name' => $names ?: $paymentType->getName(),
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
            $redirect = $trade['redirect_url'].'&outtradeno='.request()->input('order_pay_id');
        }
        // 拼团订单支付成功后跳转该团页面
        // 插件开启
        if (app('plugins')->isEnabled('fight-groups')) {
            $orders = Order::whereIn('id', $orderPay->order_ids)->get();
            // 只有一个订单
            if ($orders->count() == 1) {
                $order = $orders[0];
                // 是拼团的订单
                if ($order->plugin_id == 54) {
                    $fightGroupsTeamMember = \Yunshop\FightGroups\common\models\FightGroupsTeamMember::uniacid()->with(['hasOneTeam'])->where('order_id', $order->id)->first();
                    // 有团员并且有团队，跳到拼团详情页
                    if (!empty($fightGroupsTeamMember) && !empty($fightGroupsTeamMember->hasOneTeam)) {
                        $redirect = Url::absoluteApp('group_detail/' . $fightGroupsTeamMember->hasOneTeam->id, ['i' => \YunShop::app()->uniacid]);
                    } else {
                        $redirect = Url::absoluteApp('home');
                    }
                }
            }
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
        $data['order_sn'] = \YunShop::app()->uniacid.'_'.$orderPay->pay_sn;
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

    /**
     * 环迅快捷支付
     *
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function huanxunQuick(\Illuminate\Http\Request $request)
    {
        if (\Setting::get('plugin.huanxun_set') == false) {
            throw new AppException('商城未开启快捷支付');
        }

        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::PAY_Huanxun_Quick, ['pay' => 'quick']);

        return $this->successJson('成功', $data);
    }

    /**
     * 威富通公众号支付
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function wftWechat(\Illuminate\Http\Request $request)
    {

        if (\Setting::get('plugin.wft_pay') == false) {
            throw new AppException('商城未开启威富通公众号支付');
        }
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::WFT_PAY);

        return $this->successJson('成功', $data);
    }

    /**
     * 威富通支付宝支付
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function wftAlipay(\Illuminate\Http\Request $request)
    {

        if (\Setting::get('plugin.wft_alipay') == false) {
            throw new AppException('商城未开启威富通公众号支付');
        }
        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::WFT_ALIPAY);

        return $this->successJson('成功', $data);
    }

    /**
     * 环迅微信支付
     *
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function huanxunWx(\Illuminate\Http\Request $request)
    {
        if (\Setting::get('plugin.dian_bang_scan_set') == false) {
            throw new AppException('商城未开启快捷支付');
        }

        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::PAY_Huanxun_Wx, ['pay' => 'wx']);

        return $this->successJson('成功', $data);
    }

    /**
     * 店帮支付
     *
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function dianBangScan(\Request $request)
    {
        if (\Setting::get('plugin.dian-bang-scan') == false) {
            throw new AppException('商城未开启店帮扫码支付');
        }

        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::PAY_DIANBANG , ['pay' => 'scan']);

        return $this->successJson('成功', $data);
    }

    public function yopPay()
    {
        if (!app('plugins')->isEnabled('yop-pay')) {
            throw new AppException('商城未开启易宝支付未开启');
        }

        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::YOP);

        return $this->successJson('成功', $data);
    }

    /**
     * Usdt支付
     *
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function usdtPay(\Request $request)
    {
        if (\Setting::get('plugin.usdtpay_set') == false) {
            throw new AppException('商城未开启Usdt支付');
        }

        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::PAY_Usdt);

        return $this->successJson('成功', $data);
    }

    /**
     * 微信支付-HJ
     *
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function wechatPayHj(\Request $request)
    {
        if (\Setting::get('plugin.convergePay_set.wechat') == false && !app('plugins')->isEnabled('converge_pay')) {
            throw new AppException('商城未开启微信支付-HJ');
        }

        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::PAY_WECHAT_HJ);

        if ($data['msg'] == '成功') {
            return $this->successJson($data['msg'], $data);
        } else {
            return $this->errorJson($data['msg']);
        }
    }

    /**
     * 微信支付-HJ
     *
     * @param \Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function alipayPayHj(\Request $request)
    {
        if (\Setting::get('plugin.convergePay_set.alipay') == false && !app('plugins')->isEnabled('converge_pay')) {
            throw new AppException('商城未开启支付宝支付-HJ');
        }

        $orderPay = \app\frontend\models\OrderPay::find(request()->input('order_pay_id'));
        $data = $orderPay->getPayResult(PayFactory::PAY_ALIPAY_HJ);

        if ($data['msg'] == '成功') {
            return $this->successJson($data['msg'], $data);
        } else {
            return $this->errorJson($data['msg']);
        }
    }
}