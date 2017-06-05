<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/5
 * Time: 下午7:53
 */

namespace app\frontend\modules\order\services;


use app\common\models\Member;

class MessageService extends \app\common\services\MessageService
{
    public static function sendMessage($templateId, $msg, $uid)
    {
        $app = app('wechat');
        $notice = $app->notice;
        $result = $notice->uses($templateId)->andData($msg);

        $result->andReceiver(Member::getOpenId($uid));

        foreach (\Setting::get('shop.notice.salers') as $saler) {
            $openid = Member::getOpenId($saler['uid']);
            $result->andReceiver($openid);
        }
        return $result->send();

    }

    public static function canceled($order)
    {
        $template_id = \Setting::get('shop.notice.order_cancel');

        $msg = array(
            'first' => array(
                'value' => "您的订单已取消!",
                "color" => "#4a5077"
            ),
            'orderProductPrice' => array(
                'title' => '订单金额',
                'value' => '￥' . $order['price'] . '元(含运费' . $order['dispatch_price'] . '元)',
                "color" => "#4a5077"
            ),
            'orderProductName' => array(
                'title' => '商品详情',
                'value' => $order->hasManyOrderGoods()->first()->title,
                "color" => "#4a5077"
            ),
            'orderAddress' => $order['address']['address'],
            'orderName' => array(
                'title' => '订单编号',
                'value' => $order['order_sn'],
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => "欢迎您的再次购物！",
                "color" => "#4a5077"
            )
        );

        return self::sendMessage($template_id, $msg, $order['uid']);
    }


    public static function created($order)
    {
        $template_id = \Setting::get('shop.notice.order_submit_success');

        $remark = "\n订单下单成功,请到后台查看!";
        $orderpricestr = ' 订单总价: ' . $order['price'] . '(包含运费:' . $order['dispatch_price'] . ')';
        $msg = array(
            'first' => array(
                'value' => "订单下单通知!",
                "color" => "#4a5077"
            ),
            'keyword1' => array(
                'title' => '时间',
                'value' => $order['create_time'],
                "color" => "#4a5077"
            ),
            'keyword2' => array(
                'title' => '商品名称',
                'value' => $order->hasManyOrderGoods()->first()->title . $orderpricestr,
                "color" => "#4a5077"
            ),
            'keyword3' => array(
                'title' => '订单号',
                'value' => $order['order_sn'],
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => $remark,
                "color" => "#4a5077"
            )
        );

        return self::sendMessage($template_id, $msg);
    }

    public static function paid($order)
    {

        $template_id = \Setting::get('shop.notice.order_pay_success');

        $remark = "\n订单已经下单支付，请及时备货，谢谢!";
        $orderpricestr = ' 订单总价: ' . $order['price'] . '(包含运费:' . $order['dispatch_price'] . ')';

        $msg = array(
            'first' => array(
                'value' => "订单下单支付通知!",
                "color" => "#4a5077"
            ),
            'keyword1' => array(
                'title' => '时间',
                'value' => $order['create_time'],
                "color" => "#4a5077"
            ),
            'keyword2' => array(
                'title' => '商品名称',
                'value' => $order->hasManyOrderGoods()->first()->title . $orderpricestr,
                "color" => "#4a5077"
            ),
            'keyword3' => array(
                'title' => '订单号',
                'value' => $order['order_sn'],
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => $remark,
                "color" => "#4a5077"
            )
        );
        return self::sendMessage($template_id, $msg);
    }

    public static function sent($order)
    {
        $address = $order['address'];

        $template_id = \Setting::get('shop.notice.order_send');

        $orderpricestr = ' 订单总价: ' . $order['price'] . '(包含运费:' . $order['dispatch_price'] . ')';
        $msg = array(
            'first' => array(
                'value' => "您的宝贝已经发货！",
                "color" => "#4a5077"
            ),
            'keyword1' => array(
                'title' => '订单内容',
                'value' => $order->hasManyOrderGoods()->first()->title . $orderpricestr,
                "color" => "#4a5077"
            ),
            'keyword2' => array(
                'title' => '物流服务',
                'value' => $order['expresscom'],
                "color" => "#4a5077"
            ),
            'keyword3' => array(
                'title' => '快递单号',
                'value' => $order['expresssn'],
                "color" => "#4a5077"
            ),
            'keyword4' => array(
                'title' => '收货信息',
                'value' => "地址: " . $address['province'] . ' ' . $address['city'] . ' ' . $address['area'] . ' ' . $address['address'] . "收件人: " . $address['realname'] . ' (' . $address['mobile'] . ') ',
                "color" => "#4a5077"
            ),
            'remark' => array(
                'value' => "\r\n我们正加速送到您的手上，请您耐心等候。",
                "color" => "#4a5077"
            )
        );

        return self::sendMessage($template_id, $msg);
    }

    public static function received($order)
    {
        $template_id = \Setting::get('shop.notice.order_finish');

        $remark = "\n订单已完成,请到后台查看!";
        //$orderpricestr = ' 订单总价: ' . $order['price'] . '(包含运费:' . $order['dispatch_price'] . ')';
        $msg = array(
            'first' => array(
                'value' => '订单完成通知',
                "color" => "#4a5077"
            ),
            'keyword1' => array(
                'title' => '订单号',
                'value' => $order['order_sn'],
                "color" => "#4a5077"
            ),
            'keyword2' => array(
                'title' => '商品名称',
                'value' => $order->hasManyOrderGoods()->first()->title,
                "color" => "#4a5077"
            ),
            'keyword3' => array(
                'title' => '下单时间',
                'value' => date('Y-m-d H:i:s', $order['create_time']),
                "color" => "#4a5077"
            ),
            'keyword4' => array(
                'title' => '发货时间',
                'value' => date('Y-m-d H:i:s', $order['send_time']),
                "color" => "#4a5077"
            ),
            'keyword5' => array(
                'title' => '确认收货时间',
                'value' => date('Y-m-d H:i:s', $order['finish_time']),
                "color" => "#4a5077"
            ),
            'remark' => array(
                'title' => '',
                'value' => $remark,
                "color" => "#4a5077"
            )
        );

        return self::sendMessage($template_id, $msg);
    }
}