<?php

namespace app\frontend\modules\order\services\message;

use app\common\models\Notice;
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
        $this->goods_title .= $this->order->hasManyOrderGoods()->first()->goods_option_title ? '['.$this->order->hasManyOrderGoods()->first()->goods_option_title.']': '';
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
        $this->goodsBuy(1);

        $temp_id = \Setting::get('shop.notice')['seller_order_create'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '粉丝昵称', 'value' => $this->order->belongsToMember->nickname],
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
        $this->goodsBuy(2);

        $temp_id = \Setting::get('shop.notice')['seller_order_pay'];
        if (!$temp_id) {
            return;
        }
        $address = $this->order['address'];
        $params = [
            ['name' => '粉丝昵称', 'value' => $this->order->belongsToMember->nickname],
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
        $this->goodsBuy(3);

        $temp_id = \Setting::get('shop.notice')['seller_order_finish'];
        if (!$temp_id) {
            return;
        }
        $address = $this->order['address'];
        $params = [
            ['name' => '粉丝昵称', 'value' => $this->order->belongsToMember->nickname],
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

    /**
     * @name 购买商品发送通知
     * @author
     * @param $status
     */
    public function goodsBuy($status)
    {
        \Log::info('购买商品通知开始');
        $order_goods = $this->order->hasManyOrderGoods()->get();
        foreach ($order_goods as $goods) {
            $goods_notice = Notice::select()->where('goods_id', $goods->goods_id)->whereType($status)->first();
            if (!$goods_notice) {
                \Log::info('未找到商品通知设置');
                continue;
            }
            $temp_id = \Setting::get('shop.notice')['buy_goods_msg'];
            if (!$temp_id) {
                \Log::info('未找到消息模板id');
                continue;
            }
            $params = [
                ['name' => '会员昵称', 'value' => $this->order->belongsToMember->nickname],
                ['name' => '订单编号', 'value' => $this->order->order_sn],
                ['name' => '商品详情（含规格）', 'value' => $this->getGoodsTitle($goods)],
                ['name' => '商品金额', 'value' => $goods->price],
                ['name' => '商品数量', 'value' => $goods->total],
                ['name' => '订单状态', 'value' => $this->order->status_name],
                ['name' => '时间', 'value' => $this->getOrderTime($status)],
            ];
            $msg = MessageTemp::getSendMsg($temp_id, $params);
            if ($msg) {
                \Log::info('未找到消息模板');
                continue;
            }
            $template_id = MessageTemp::$template_id;
            $this->notice($template_id, $msg, $goods_notice->uid);
        }
    }

    /**
     * @name 获取订单操作时间
     * @author
     * @param $status
     * @return mixed
     */
    private function getOrderTime($status)
    {
        if ($status == 1) {
            $order_time = $this->order['create_time']->toDateTimeString();
        } else if ($status == 2) {
            $order_time = $this->order['pay_time']->toDateTimeString();
        } else if ($status == 3) {
            $order_time = $this->order['finish_time']->toDateTimeString();
        }
        return $order_time;
    }

    /**
     * @name 获取商品名
     * @author
     * @param $goods
     * @return string
     */
    private function getGoodsTitle($goods)
    {
        $goods_title = $goods->title;
        if ($goods->goods_option_title) {
            $goods_title .= '[' . $goods->goods_option_title . ']';
        }
        return $goods_title;
    }
}