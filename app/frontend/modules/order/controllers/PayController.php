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
use app\common\services\PayFactory;
use Ixudra\Curl\Facades\Curl;

class PayController extends ApiController
{
    public function index(\Request $request)
    {
        $order_id = $request->query('order_id');
        $order = Order::select(['status','id','order_sn','price','uid'])->find($order_id);
        if (!isset($order)) {
            throw new AppException('未找到订单');
        }
        if ($order->status != Order::WAIT_PAY) {
            throw new AppException('订单已付款');
        }
        $member = $order->belongsToMember()->select(['credit2'])->first()->toArray();
        $data = ['order' => $order,'member'=>$member];

        return $this->successJson('成功', $data);
    }
    private function pay($request,$payType){
        $this->validate($request,[
            'order_id' => 'required|integer'
        ]);
        $order = Order::find($request->query('order_id'));
//        dd($request->query('order_id'));
//        exit;
        if(!isset($order)){
            throw new AppException('订单不存在');
        }
        if($order->uid != \YunShop::app()->getMemberId()){
            throw new AppException('无效申请,该订单属于其他用户');
        }
        if($order->status > Order::WAIT_SEND){
            throw new AppException('订单已付款,请勿重复付款');
        }
        if($order->status == Order::CLOSE){
            throw new AppException('订单已关闭,无法付款');
        }

        $query_str = [
            'order_no' => $order->order_sn,
            'amount' => $order->price,
            'subject' => '微信支付',
            'body' => $order->hasManyOrderGoods[0]->title.':'.\YunShop::app()->uniacid,
            'extra' => ['type' => 1]
        ];

        $pay = PayFactory::create($payType);
        $data = $pay->doPay($query_str);

        $data['js'] = json_decode($data['js'], 1);
        return $this->successJson('成功', $data);
    }
    public function wechatPay(\Request $request)
    {
        return $this->pay($request,PayFactory::PAY_WEACHAT);
        //return $this->
        //return view('order.pay', $data)->render();
    }

    public function alipay(\Request $request)
    {
        return $this->pay($request,PayFactory::PAY_ALIPAY);

        //获取支付宝 支付单 数据
    }
}