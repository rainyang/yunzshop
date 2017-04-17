<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/2
 * Time: 下午4:55
 */

namespace app\frontend\modules\order\services\status;


use app\common\models\Order;

class Complete implements StatusService
{
    private $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getStatusName()
    {
        return '交易完成';
    }

    public function getButtonModels()
    {
        $result =
            [
                [
                    'name' => '评价',
                    'api' => '', //todo
                    'value' => static::COMMENT
                ],
                [
                    'name' => '查看物流', //todo 原来商城的逻辑是, 当有物流单号时, 才显示"查看物流"按钮
                    'api' => 'dispatch.express',
                    'value' => static::EXPRESS
                ],
                [
                    'name' => '删除订单',
                    'api' => 'order.operation.delete',
                    'value' => static::DELETE
                ],

            ];
        $can_comment = $this->order->hasManyOrderGoods->contains(function ($orderGoods){
            return $orderGoods->comment_status == 0;
        });

        if($can_comment){
            $result[] = [
                'name' => '评价',
                'api' => '',
                'value' => static::COMMENT
            ];
        }
        if(empty($this->order->refund_id)){
            $result[] = [
                'name' => '申请退款',
                'api' => 'order.refund.apply', //todo
                'value' => static::REFUND
            ];
        }
        return $result;
    }
}