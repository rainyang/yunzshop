<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/18
 * Time: 下午2:14
 */

namespace app\common\events\order;


use app\common\events\Event;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

abstract class PreGenerateOrderEvent extends Event
{
    private $orderModel;

    public function __construct(PreGeneratedOrderModel $orderModel)
    {
        $this->orderModel = $orderModel;
    }
    //todo
    public function getOrderModel(){
        return $this->orderModel;
    }
}