<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/17
 * Time: 下午11:34
 */

namespace app\frontend\modules\order\services\status;

use app\common\models\Order;

abstract class Status
{
    const PAY = 1;
    const COMPLETE = 5;
    const EXPRESS = 8;
    const CANCEL = 9;
    const COMMENT = 10;
    const ADD_COMMENT = 11;
    const DELETE = 12;
    const REFUND = 13;
    const VERIFY = 14;
    const AFTER_SALES = 15;
    const IN_REFUND = 16;
    const IN_AFTER_SALE = 17;
    const REFUND_INFO = 18;
    const COMMENTED = 19;
    const STORE_PAY = 20;
    const REMITTANCE_RECORD = 21;
    abstract function getStatusName();

    abstract function getButtonModels();

    /**
     * 退款按钮
     * @param $order
     * @return array
     */
    public static function getRefundButtons(Order $order)
    {
        if ($order['status'] >= Order::COMPLETE) {
            // 完成后不许退款
            if (\Setting::get('shop.trade.refund_days') === '0') {
                return [];
            }
            // 完成后n天不许退款
            if ($order->finish_time->diffInDays() > \Setting::get('shop.trade.refund_days')) {
                return [];
            }
        }
        if($order['status'] <= Order::WAIT_PAY){
            return [];
        }
        if (!empty($order->refund_id) && isset($order->hasOneRefundApply)) {
            // 退款处理中
            if ($order->hasOneRefundApply->isRefunded()) {
                $result[] = [
                    'name' => '已退款',
                    'api' => 'refund.detail',
                    'value' => static::REFUND_INFO
                ];
            } else {
                $result[] = [
                    'name' => '退款中',
                    'api' => 'refund.detail',
                    'value' => static::REFUND_INFO
                ];
            }

        } else {
            // 可申请
            $result[] = [
                'name' => '申请退款',
                'api' => 'refund.apply',
                'value' => static::REFUND
            ];

        }

        return $result;
    }

    /**
     * 评论按钮
     * @param $orderGoods
     * @return array
     */
    public static function getCommentButtons($orderGoods)
    {

        if ($orderGoods->comment_status == 0) {
            $result[] = [
                'name' => '评价',
                'api' => '',
                'value' => static::COMMENT
            ];
        } else {
            $result[] = [
                'name' => '已评价',
                'api' => '',
                'value' => static::COMMENTED
            ];
        }

        return $result;
    }
}
