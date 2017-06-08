<?php

namespace app\frontend\modules\order\services\message;

use app\common\services\MessageService;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/7
 * Time: 上午10:18
 */
class Message extends MessageService
{

    /**
     * @var Order
     */
    protected $order;
    protected $msg;
    protected $templateId;
    protected $notice;
    function __construct($order)
    {
        $this->order = $order;
        $app = app('wechat');
        $this->notice = $app->notice;
    }
}