<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:19
 * comment: 订单操作基类
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\AfterOrderCancelPaidEvent;
use app\common\events\order\BeforeOrderCancelPayEvent;
use app\common\events\order\BeforeCreatedOrderStatusChangeEvent;
use app\common\models\Order;
use app\frontend\modules\order\services\models\OperationValidator;


abstract class OrderOperation
{
    protected $order_model;
    /**
     * @var string 默认返回信息
     */
    protected $message = '成功';
    /**
     * @var array 合法前置状态
     */
    protected $status_before_change = [];

    /**
     * @var类名的过去式
     */
    protected $past_tense_class_name;
    /**
     * @var 操作名
     */
    protected $name;

    /**
     * @return string 获取消息
     */
    public function getMessage(){
        return $this->name.$this->message;
    }

    /**
     * OrderOperation constructor.
     * @param Order $order_model
     */
    public function __construct(Order $order_model)
    {
        //dd();exit;
        $this->order_model = $order_model;
    }

    /**
     * 获取不带命名空间的类名
     * @return mixed
     */
    private function _getOperationName(){
        $result = explode('\\',static::class);
        return end($result);
    }

    /**
     * @return 类名的过去式
     */
    protected function _getPastTense(){
        return $this->past_tense_class_name;
    }

    /**
     * 是否满足操作条件
     * @return bool
     */
    public function enable()
    {
        $event_name = '\app\common\events\order\Before'.$this->_getOperationName().'Event';

        $Event = new $event_name($this->order_model);
        event($Event);
        if ($Event->hasOpinion()) {
            $this->message = $Event->getOpinion()->message;
            return $Event->getOpinion()->result;
        }


        if (!in_array($this->order_model['status'],$this->status_before_change)) {
            $this->message = "订单状态不满足{$this->name}操作";
            return false;
        }
        return true;
    }

    /**
     * 执行订单操作
     * @return mixed
     */
    abstract public function execute();

    /**
     *
     */
    protected function _fireEvent(){
        $event_name = '\app\common\events\order\After'.$this->_getPastTense().'Event';
        event(new $event_name($this->order_model));
        return;
    }
}