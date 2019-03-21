<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 24/03/2017
 * Time: 18:10
 */

namespace app\common\events;


class WechatMessage extends Event
{
    protected $message;

    protected $wechatApp;

    public function __construct($wechatApp,$message)
    {
        $this->message = $message;
        $this->wechatApp = $wechatApp;
    }

    public function getWechatApp()
    {
        return $this->wechatApp;
    }

    public function getMessage()
    {
        return $this->message;
    }
}