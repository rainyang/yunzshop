<?php

namespace app\frontend\modules\order\services\message;

use app\common\models\notice\MessageTemp;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/7
 * Time: 上午10:15
 */
class ShopMessage extends Message
{
    protected $goods_title;

    public function __construct($order)
    {
        parent::__construct($order);
        $this->goods_title = $this->order->hasManyOrderGoods()->first()->title;
        $this->goods_title .= $this->order->hasManyOrderGoods()->first()->goods_option_title ?: '';
    }

    private function sendToShops()
    {
        if (empty(\Setting::get('shop.notice.salers'))) {
            return;
        }
        if (empty($this->templateId)) {
            return;
        }
        //客服发送消息通知
        foreach (\Setting::get('shop.notice.salers') as $saler) {
            $this->notice($this->templateId, $this->msg, $saler['uid']);
        }
    }

    private function transfer($temp_id, $params)
    {
        $this->msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$this->msg) {
            return;
        }
        $this->templateId = MessageTemp::$template_id;
        $this->sendToShops();
    }

    public function created()
    {
        $temp_id = \Setting::get('shop.notice')['seller_order_create'];
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
        $temp_id = \Setting::get('shop.notice')['seller_order_pay'];
        if (!$temp_id) {
            return;
        }
        $address = $this->order['address'];
        $params = [
            ['name' => '粉丝昵称', 'value' => $this->order->belongsToMembe->nickname],
            ['name' => '订单号', 'value' => $this->order->order_sn],
            ['name' => '下单时间', 'value' => $this->order['create_time']->toDateTimeString()],
            ['name' => '支付时间', 'value' => $this->order['pay_time']->toDateTimeString()],
            ['name' => '支付方式', 'value' => $this->order->pay_type_name],
            ['name' => '订单金额', 'value' => $this->order['price']],
            ['name' => '运费', 'value' => $this->order['dispatch_price']],
            ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
            ['name' => '收件人姓名', 'value' => $address['realname']],
            ['name' => '收件人电话', 'value' => $address['mobile']],
            ['name' => '收件人地址', 'value' => $address['province'] . ' ' . $address['city'] . ' ' . $address['area'] . ' ' . $address['address']],
        ];
        $this->transfer($temp_id, $params);
    }

    public function received()
    {
        $temp_id = \Setting::get('shop.notice')['seller_order_finish'];
        if (!$temp_id) {
            return;
        }
        $address = $this->order['address'];
        $params = [
            ['name' => '粉丝昵称', 'value' => $this->order->belongsToMembe->nickname],
            ['name' => '订单号', 'value' => $this->order->order_sn],
            ['name' => '确认收货时间', 'value' => $this->order['finish_time']->toDateTimeString()],
            ['name' => '运费', 'value' => $this->order['dispatch_price']],
            ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
            ['name' => '收件人姓名', 'value' => $address['realname']],
            ['name' => '收件人电话', 'value' => $address['mobile']],
            ['name' => '收件人地址', 'value' => $address['province'] . ' ' . $address['city'] . ' ' . $address['area'] . ' ' . $address['address']],
        ];
        $this->transfer($temp_id, $params);
    }
}