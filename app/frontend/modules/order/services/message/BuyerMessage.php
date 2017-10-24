<?php

namespace app\frontend\modules\order\services\message;

use app\common\models\Member;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/7
 * Time: 上午10:15
 */
class BuyerMessage extends Message
{

    protected function sendToBuyer()
    {
        return $this->sendToMember($this->order->uid);
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

    public function created()
    {
        $this->templateId = \Setting::get('shop.notice.order_submit_success');

        $remark = "\r\n订单下单成功,请到后台查看!";
        $orderpricestr = $this->order['price'] . '(包含运费:' . $this->order['dispatch_price'] . ')';

        $this->msg = array(
            'first' => array(
                'value' => (string)"订单下单通知!",
                "color" => "#4a5077"
            ),
            'keyword1' => array(
                //todo
                'value' => (string)array_get(\Setting::get('shop'),'name','自营'),
                "color" => "#4a5077"
            ),
            'keyword2' => array(
                'value' => (string)$this->order['create_time']->toDateTimeString(),
                "color" => "#4a5077"
            ),
            'keyword3' => array(
                'value' => (string)$this->order->hasManyOrderGoods()->first()->title,
                "color" => "#4a5077"
            ),
            'keyword4' => array(
                'value' => (string)$orderpricestr,
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => (string)$remark,
                "color" => "#4a5077"
            )
        );
        //$this->sendToShops();
        $this->msg['remark']['value'] = "\r\n订单下单成功";

        $this->sendToBuyer();
//order_pay_success
    }

    public function paid()
    {
        $this->templateId = \Setting::get('shop.notice.order_pay_success');
        $orderpricestr = $this->order['price'] . '(包含运费:' . $this->order['dispatch_price'] . ')';
        $remark = "\r\n订单已支付!";
        $this->msg = array(
            'first' => array(
                'value' => (string)"您的订单已支付成功!",
                "color" => "#4a5077"
            ),
            'keyword1' => array(
                //todo
                'value' => (string)$this->order['order_sn'] .
                    "\r\n商品: " . (string)$this->order->hasManyOrderGoods()->first()->title,
                "color" => "#4a5077"
            ),
            'keyword2' => array(
                'value' => (string)'已支付',
                "color" => "#4a5077"
            ),
            'keyword3' => array(
                'value' => (string)$this->order['pay_time']->toDateTimeString(),
                "color" => "#4a5077"
            ),
            'keyword4' => array(
                'value' => array_get(\Setting::get('shop'),'name','自营'),
                "color" => "#4a5077"
            ),
            'keyword5' => array(
                'value' => (string)$orderpricestr,
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => (string)$remark,
                "color" => "#4a5077"
            )
        );
        $this->sendToBuyer();
    }

    public function canceled()
    {
        $this->templateId = \Setting::get('shop.notice.order_cancel');

        $this->msg = array(
            'first' => array(
                'value' => (string)"您的订单已取消!",
                "color" => "#4a5077"
            ),
            'orderProductPrice' => array(
                'title' => '订单金额',
                'value' => (string)'￥' . $this->order['price'] . '元(含运费' . $this->order['dispatch_price'] . '元)',
                "color" => "#4a5077"
            ),
            'orderProductName' => array(
                'title' => '商品详情',
                'value' => (string)$this->order->hasManyOrderGoods()->first()->title,
                "color" => "#4a5077"
            ),
            'orderAddress' => $this->order['address']['address'],
            'orderName' => array(
                'title' => '订单编号',
                'value' => (string)$this->order['order_sn'],
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => (string)"欢迎您的再次购物！",
                "color" => "#4a5077"
            )
        );
        $this->sendToBuyer();
        $this->sendToParentBuyer();
    }

    public function sent()
    {
        $address = $this->order['address'];

        $this->templateId = \Setting::get('shop.notice.order_send');

        $orderpricestr = ' 订单总价: ' . $this->order['price'] . '(包含运费:' . $this->order['dispatch_price'] . ')';

        $this->msg = array(
            'first' => array(
                'value' => (string)"您的宝贝已经发货！",
                "color" => "#4a5077"
            ),
            'keyword1' => array(
                'title' => '订单内容',
                'value' => (string)$this->order->hasManyOrderGoods()->first()->title . $orderpricestr,
                "color" => "#4a5077"
            ),
            'keyword2' => array(
                'title' => '物流服务',
                'value' => (string)$this->order['express']['express_company_name'] ?: "暂无信息",
                "color" => "#4a5077"
            ),
            'keyword3' => array(
                'title' => '快递单号',
                'value' => (string)$this->order['express']['express_sn'] ?: "暂无信息",
                "color" => "#4a5077"
            ),
            'keyword4' => array(
                'title' => '收货信息',
                'value' => (string)$address['province'] . ' ' . $address['city'] . ' ' . $address['area'] . ' ' . $address['address'] .
                    "\r\n收件人: " . $address['realname'] . ' (' . $address['mobile'] . ') ',
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => (string)"\r\n我们正加速送到您的手上，请您耐心等候。",
                "color" => "#4a5077"
            )
        );
        $this->sendToBuyer();

    }

    public function received()
    {
        $this->templateId = \Setting::get('shop.notice.order_finish');

        $remark = "\r\n订单已完成!";
        $orderpricestr = " \r\n订单总金额: " . $this->order['price'] . '(包含运费:' . $this->order['dispatch_price'] . ')';
        $this->msg = array(
            'first' => array(
                'value' => (string)'订单完成通知',
                "color" => "#4a5077"
            ),
            'keyword1' => array(
                'value' => (string)$this->order['order_sn'],
                "color" => "#4a5077"
            ),

            'keyword2' => array(
                'value' => (string)$this->order->hasManyOrderGoods()->first()->title . $orderpricestr,
                "color" => "#4a5077"
            ),
            'keyword3' => array(
                'value' => (string)$this->order['create_time']->toDateTimeString(),
                "color" => "#4a5077"
            ),
            'keyword4' => array(
                'value' => (string)$this->order['send_time']->toDateTimeString(),
                "color" => "#4a5077"
            ),
            'keyword5' => array(
                'value' => (string)$this->order['finish_time']->toDateTimeString(),
                "color" => "#4a5077"
            ),

            'remark' => array(
                'title' => '',
                'value' => (string)$remark,
                "color" => "#4a5077"
            )
        );
        $this->sendToBuyer();

    }
}