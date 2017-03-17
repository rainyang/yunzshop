<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午10:35
 * comment:订单支付类
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\events\order\AfterOrderPaidEvent;
use app\common\models\Order;
use Illuminate\Support\Facades\Event;

class OrderPay
{
    public $order_model;

    public function __construct(Order $order_model)
    {
        $this->order_model = $order_model;
    }

    public function pay()
    {
        $this->order_model->status = 1;
        $result = $this->order_model->save();
        Event::fire(new AfterOrderPaidEvent($this->order_model));
        return $result;
    }

    public function payable()
    {
        if ($this->order_model->status == 0) {
            return true;
        }
        return false;
    }
}