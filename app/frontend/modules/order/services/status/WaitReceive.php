<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/2
 * Time: 下午4:55
 */

namespace app\frontend\modules\order\services\status;


use app\common\models\Order;

class WaitReceive implements StatusService
{
    private $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getStatusName()
    {
        return '待收货';
    }

    public function getButtonModels()
    {
        //            $button_id_arr[] = static::COMPLETE;//收货
        $result =
            [
                [
                    'name' => '确认收货',
                    'api' => '/order/operation/receive',
                    'value' => static::COMPLETE //todo
                ],
                [
                    'name' => '查看物流', //todo 原来商城的逻辑是, 当有物流单号时, 才显示"查看物流"按钮
                    'api' => '/order/operation/', //todo
                    'value' => static::EXPRESS //todo
                ],
            ];
        return $result;
    }
}