<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午5:01
 */

namespace app\frontend\models\order;

use app\frontend\models\Order;

abstract class OrderOperation
{
    protected $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    public function enable(){

    }
    abstract public function getName();
    abstract public function getValue();
}