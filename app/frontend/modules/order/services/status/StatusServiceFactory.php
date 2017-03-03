<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/2
 * Time: 下午4:52
 */

namespace app\frontend\modules\order\services\status;


class StatusServiceFactory
{
    public static function createStatusService($order){
        switch ($order->stauts){
            case -1:
                break;
            case 0:
                return new WaitPay($order);
                break;
            case 1:
                return new WaitSend($order);
                break;
            case 2:
                //new WaitSend();
                break;
            case 3:
                break;

        }
    }
}