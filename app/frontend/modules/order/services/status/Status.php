<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/17
 * Time: 下午11:34
 */

namespace app\frontend\modules\order\services\status;


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

    abstract function getStatusName();

    abstract function getButtonModels();

    /**
     * 退款按钮
     * @param $order
     * @return array
     */
    public static function getRefundButtons($order)
    {
        if (empty($order->refund_id)) {
            $result[] = [
                'name' => '申请退款',
                'api' => 'refund.apply',
                'value' => static::REFUND
            ];
        } else {
            $result[] = [
                'name' => '退款中',
                'api' => 'refund.detail',
                'value' => static::REFUND_INFO
            ];
        }
        return $result;
    }

    /**
     * 评论按钮
     * @param $order
     * @return array
     */
    public static function getCommentButtons($order)
    {
        $result = [];
        $can_comment = $order->hasManyOrderGoods->contains(function ($orderGoods){
            return $orderGoods->comment_status == 0;
        });

        if($can_comment){
            $result[] = [
                'name' => '评价',
                'api' => '',
                'value' => static::COMMENT
            ];
        }
        return $result;
    }
}
