<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/2
 * Time: 下午4:55
 */

namespace app\frontend\modules\order\services\status;


class WaitPay implements StatusService
{
    public function getStatusName()
    {
        return '待付款';
    }

    public function getButtonModels()
    {
        $result =
            [
                ['name' => '付款',
                    'api' => '/order/pay',//
                    'value' => static::PAY],
                [
                    'name' => '取消订单',
                    'api' => 'cancel',
                    'value' => static::CANCEL
                ],
            ];
        return $result;
    }
}