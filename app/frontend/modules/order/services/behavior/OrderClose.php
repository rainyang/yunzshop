<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:07
 * comment:订单关闭类
 */

namespace app\frontend\modules\order\services\behavior;
use app\common\events\order\AfterOrderCanceledEvent;
use app\common\models\Order;
use Illuminate\Support\Facades\Event;

class OrderClose
{
    public $order_model;

    public function __construct(Order $order_model)
    {
        $this->order_model = $order_model;
    }

    public function close()
    {
        $this->order_model->status = -1;
        $result = $this->order_model->save();
        Event::fire(new AfterOrderCanceledEvent($this->order_model));
        return $result;
    }

    public function closeable()
    {
        if ($this->order_model->status == 0) {
            return true;
        }
        return false;
    }
}