<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 上午11:19
 * comment: 订单操作基类
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\models\Order;


abstract class OrderOperation
{
    /**
     * @var Order
     */
    protected $order;
    /**
     * @var string 默认返回信息
     */
    protected $message = '成功';
    /**
     * @var array 合法前置状态
     */
    protected $status_before_change = [];

    /**
     * @var string 类名的过去式
     */
    protected $past_tense_class_name;
    /**
     * @var string 操作名
     */
    protected $name;

    /**
     * @return string 获取消息
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * OrderOperation constructor.
     * @param Order $order_model
     */
    public function __construct(Order $order_model)
    {
        $this->order = $order_model;
    }

    /**
     * 获取不带命名空间的类名
     * @return mixed
     */
    private function _getOperationName()
    {
        $result = explode('\\', static::class);
        return end($result);
    }

    /**
     * @return string 类名的过去式
     */
    protected function _getPastTense()
    {
        return $this->past_tense_class_name;
    }

    /**
     * @return \app\common\events\order\CreatedOrderEvent
     */
    protected function _getBeforeEvent()
    {
        $event_name = '\app\common\events\order\Before' . $this->_getOperationName() . 'Event';
        return new $event_name($this->order);
    }

    /**
     * 是否满足操作条件
     * @return bool
     */
    public function enable()
    {

        $Event = $this->_getBeforeEvent();
        event($Event);
        if ($Event->hasOpinion()) {
            $this->message = $Event->getOpinion()->message;
            return $Event->getOpinion()->result;
        }


        if (!in_array($this->order['status'], $this->status_before_change)) {
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
    protected function _fireEvent()
    {
        $event_name = '\app\common\events\order\After' . $this->_getPastTense() . 'Event';
        event(new $event_name($this->order));
        return;
    }
}