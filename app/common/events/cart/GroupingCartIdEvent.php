<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/5
 * Time: 上午11:33
 */

namespace app\common\events\cart;

use app\common\events\Event;

class GroupingCartIdEvent extends Event
{
    protected $cart_ids;

    public function __construct($cart_ids)
    {
        $this->cart_ids = $cart_ids;
    }
    /**
     * (监听者)获取购物车model
     * @return mixed
     */
    public function getCartIds(){
        return $this->cart_ids;
    }
}