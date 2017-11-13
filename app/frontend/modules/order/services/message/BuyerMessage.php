<?php

namespace app\frontend\modules\order\services\message;

use app\common\models\Member;
use app\common\models\notice\MessageTemp;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/7
 * Time: 上午10:15
 */
class BuyerMessage extends Message
{
    protected $goods_title;

    public function __construct($order)
    {
        parent::__construct($order);
        $this->goods_title = $this->order->hasManyOrderGoods()->first()->title;
        $this->goods_title .= $this->order->hasManyOrderGoods()->first()->goods_option_title ?: '';
    }

    protected function sendToBuyer()
    {
        try {

            return $this->sendToMember($this->order->uid);
        } catch (\Exception $exception) {

        }
    }

    protected function sendToParentBuyer()
    {
        if (!isset($this->order->belongsToMember->yzMember->parent_id)) {
            return;
        }
        return $this->sendToMember($this->order->belongsToMember->yzMember->parent_id);
    }

    protected function sendToMember($uid)
    {
        if (empty($this->templateId)) {
            return;
        }
        $this->notice($this->templateId, $this->msg, $uid);
    }

    private function transfer($temp_id, $params, $type = false)
    {
        $this->msg = MessageTemp::getSendMsg($temp_id, $params);
        $this->templateId = MessageTemp::$template_id;
        $this->sendToBuyer();
        if ($type) {
            $this->sendToParentBuyer();
        }
    }

    public function created()
    {
        $temp_id = \Setting::get('shop.notice')['order_submit_success'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '粉丝昵称', 'value' => $this->order->belongsToMembe->nickname],
            ['name' => '订单号', 'value' => $this->order->order_sn],
            ['name' => '下单时间', 'value' => $this->order['create_time']->toDateTimeString()],
            ['name' => '订单金额', 'value' => $this->order['price']],
            ['name' => '运费', 'value' => $this->order['dispatch_price']],
            ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
        ];
        $this->transfer($temp_id, $params);
    }

    public function paid()
    {
        $temp_id = \Setting::get('shop.notice')['order_pay_success'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '粉丝昵称', 'value' => $this->order->belongsToMembe->nickname],
            ['name' => '订单号', 'value' => $this->order->order_sn],
            ['name' => '下单时间', 'value' => $this->order->created_at],
            ['name' => '订单金额', 'value' => $this->order['price']],
            ['name' => '运费', 'value' => $this->order['dispatch_price']],
            ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
            ['name' => '支付方式', 'value' => $this->order->pay_type_name],
            ['name' => '支付时间', 'value' => $this->order['pay_time']->toDateTimeString()],
        ];
        $this->transfer($temp_id, $params);
    }

    public function canceled()
    {
        $temp_id = \Setting::get('shop.notice')['order_cancel'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '粉丝昵称', 'value' => $this->order->belongsToMembe->nickname],
            ['name' => '订单号', 'value' => $this->order->order_sn],
            ['name' => '下单时间', 'value' => $this->order->created_at],
            ['name' => '订单金额', 'value' => $this->order['price']],
            ['name' => '运费', 'value' => $this->order['dispatch_price']],
            ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
            ['name' => '支付方式', 'value' => $this->order->pay_type_name],
            ['name' => '订单取消时间', 'value' => $this->order['cancel_time']->toDateTimeString()],
        ];
        $this->transfer($temp_id, $params, true);
    }

    public function sent()
    {
        $temp_id = \Setting::get('shop.notice')['order_send'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '粉丝昵称', 'value' => $this->order->belongsToMembe->nickname],
            ['name' => '订单号', 'value' => $this->order->order_sn],
            ['name' => '下单时间', 'value' => $this->order->created_at],
            ['name' => '订单金额', 'value' => $this->order['price']],
            ['name' => '运费', 'value' => $this->order['dispatch_price']],
            ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
            ['name' => '发货时间', 'value' => $this->order['send_time']->toDateTimeString()],
            ['name' => '快递公司', 'value' => $this->order['express']['express_company_name'] ?: "暂无信息"],
            ['name' => '快递单号', 'value' => $this->order['express']['express_sn'] ?: "暂无信息"],
        ];
        $this->transfer($temp_id, $params);
    }

    public function received()
    {
        $temp_id = \Setting::get('shop.notice')['order_finish'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '粉丝昵称', 'value' => $this->order->belongsToMembe->nickname],
            ['name' => '订单号', 'value' => $this->order->order_sn],
            ['name' => '下单时间', 'value' => $this->order->created_at],
            ['name' => '订单金额', 'value' => $this->order['price']],
            ['name' => '运费', 'value' => $this->order['dispatch_price']],
            ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
            ['name' => '确认收货时间', 'value' => $this->order['finish_time']->toDateTimeString()],
        ];
        $this->transfer($temp_id, $params);
    }
}