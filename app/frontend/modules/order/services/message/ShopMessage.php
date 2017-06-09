<?php

namespace app\frontend\modules\order\services\message;

use app\common\models\Member;


/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/7
 * Time: 上午10:15
 */
class ShopMessage extends Message
{
    private function sendToShops()
    {

        if(empty(\Setting::get('shop.notice.salers'))){
            return ;
        }
        if(empty($this->templateId)){
            return ;
        }
        //客服发送消息通知
       /* foreach (\Setting::get('shop.notice.salers') as $saler) {
            $openid = Member::getOpenId($saler['uid']);
            if(!empty($openid)){
                $this->notice->uses($this->templateId)->andData($this->msg)->andReceiver($openid)->send();
            }
        }*/
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
                'value' => (string)$this->order['create_time']->toDateTimeString(),
                "color" => "#4a5077"
            ),
            'keyword2' => array(
                'value' => (string)$this->order->hasManyOrderGoods()->first()->title,
                "color" => "#4a5077"
            ),
            'keyword3' => array(
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

        $this->sendToShops();

    }

    public function paid()
    {

        $this->templateId = \Setting::get('shop.notice.task');

        $remark = "\r\n订单已经下单支付，请及时备货，谢谢!";
        $orderpricestr = "\r\n订单总价: " . $this->order['price'] . '(包含运费:' . $this->order['dispatch_price'] . ')';

        $this->msg = array(
            'first' => array(
                'value' => (string)"订单下单支付通知!",
                "color" => "#4a5077"
            ),
            'keyword1' => array(
                'value' => (string)'订单下单支付通知!',
                "color" => "#4a5077"
            ),
            'keyword2' => array(
                'value' => (string)$this->order->hasManyOrderGoods()->first()->title . $orderpricestr .
                    "\r\n订单号: " . (string)$this->order['order_sn'],
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => (string)$remark,
                "color" => "#4a5077"
            )
        );
        $this->sendToShops();

    }

    public function received()
    {
        $this->templateId = \Setting::get('shop.notice.task');

        $remark = "\r\n订单已完成,请到后台查看!";
        $orderpricestr = '订单总价: ' . $this->order['price'] . '(包含运费:' . $this->order['dispatch_price'] . ')';

        $this->msg = array(
            'first' => array(
                'value' => (string)"订单完成通知!",
                "color" => "#4a5077"
            ),
            'keyword1' => array(
                'value' => (string)'订单完成通知!',
                "color" => "#4a5077"
            ),
            'keyword2' => array(
                'value' => (string)$this->order->hasManyOrderGoods()->first()->title
                    . "\r\n" . $orderpricestr .
                    "\r\n订单号: " . (string)$this->order['order_sn'],
                "\r\n完成时间: " . (string)$this->order['finish_time']->toDateTimeString(),
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => (string)$remark,
                "color" => "#4a5077"
            )
        );
        $this->sendToShops();

    }
}