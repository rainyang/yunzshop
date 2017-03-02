<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/2
 * Time: 上午9:44
 */

namespace app\frontend\modules\order\model\factory;


use app\common\models\Order;
use app\frontend\modules\order\model\WaitPayOrderModel;

class WaiPayOrderModelFactory
{
    public static function createModel(){
        $result = [];
        foreach (self::getOrdersFormOrm() as $order){
            $result[] = new WaitPayOrderModel($order->toArray());
        }
        var_dump($result[0]);exit;
        return $result;
    }
    public static function getOrdersFormOrm(){
        return Order::WaitPay()->take(2);
    }
}