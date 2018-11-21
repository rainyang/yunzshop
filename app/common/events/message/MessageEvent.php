<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/25
 * Time: 下午2:57
 */

namespace app\common\events\message;

use app\common\events\Event;

abstract class MessageEvent extends Event
{
    protected $message_data;

    public function __construct($message_data)
    {
        $this->message_data = $message_data;
    }

    public function getMessageData(){
        return $this->message_data;
    }
}