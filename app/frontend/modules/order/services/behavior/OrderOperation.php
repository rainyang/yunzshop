<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:19
 * comment: 取消支付
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\AfterOrderCancelPaidEvent;
use app\common\events\order\BeforeOrderCancelPayEvent;
use app\common\events\order\BeforeOrderStatusChangeEvent;
use app\common\models\Order;
use app\frontend\modules\order\services\models\OperationValidator;
use Illuminate\Support\Facades\Event;

class OrderOperation
{
    protected $order_model;
    protected $message = '';

    public function getMessage(){
        return $this->message;
    }
    public function __construct(Order $order_model)
    {
        $this->order_model = $order_model;
    }
}