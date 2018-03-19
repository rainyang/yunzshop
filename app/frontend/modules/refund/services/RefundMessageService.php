<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/15
 * Time: 18:09
 */

namespace app\frontend\modules\refund\services;

use app\common\services\MessageService;
use app\common\facades\Setting;
use app\common\models\notice\MessageTemp;
use app\backend\modules\order\models\Order;
use app\backend\modules\member\models\Member;
use app\backend\modules\goods\models\Goods;
use app\backend\modules\order\models\OrderGoods;

class RefundMessageService extends MessageService
{
    public static function applyRefundNotice($refundApply,$uniacid = '')
    {
        $couponNotice = Setting::get('shop.notice');
        $temp_id = $couponNotice['order_refund_apply'];
        if (!$temp_id) {
            return false;
        }

        $memberDate = Member::getMemberBaseInfoById($refundApply->uid);
        $orderDate = Order::getOrderDetailById($refundApply->order_id);
//        $goods = Order::find($refundApply->order_id)->hasManyOrderGoods()->value('goods_option_title');//商品详情
//        $goods_title = Order::find($refundApply->order_id)->hasManyOrderGoods()->value('title').$goods;
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '粉丝昵称', 'value' => $memberDate['nickname']],
            ['name' => '退款单号', 'value' => $refundApply->refund_sn],
//            ['name' => '下单时间', 'value' => $orderDate['create_time']],
//            ['name' => '订单金额', 'value' => $orderDate['price']],
//            ['name' => '运费', 'value' => $orderDate['dispatch_price']],
//            ['name' => '商品详情（含规格）', 'value' => $goods_title],
//            ['name' => '支付方式', 'value' => $orderDate->pay_type_name],
            ['name' => '退款申请时间', 'value' => $refundApply->create_time],
            ['name' => '退款方式', 'value' => $orderDate->pay_type_name],
            ['name' => '退款金额', 'value' => $refundApply->price],
            ['name' => '退款原因', 'value' => $refundApply->reason],
        ];

        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return false;
        }
        MessageService::notice(MessageTemp::$template_id, $msg, $refundApply->uid, $uniacid);
    }
}