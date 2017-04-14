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

    public function wechatPay()
    {


        //$order_id = '';
        //$pay = PayFactory::create(PayFactory::PAY_WEACHAT);
        /*$result = $pay->setyue('50');
        if($result == false){
            $this->errorJson($pay->getMessage());
        }*/
        $query_str = [
            'order_no' => 'sn' . time(),
            'amount' => 0.1,
            'subject' => '微信支付',
            'body' => '商品的描述:2',
            'extra' => ['type' => 1]
        ];
        $pay = PayFactory::create(PayFactory::PAY_WEACHAT);
        $data = $pay->doPay($query_str);

        $data['js'] = json_decode($data['js'], 1);
        return $this->successJson('成功', $data);

        //return view('order.pay', $data)->render();
    }

    public function alipay()
    {
        //获取支付宝 支付单 数据
    }
}