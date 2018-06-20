<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 下午1:47
 */

namespace app\common\modules\status\listeners;


use app\common\modules\process\events\AfterProcessStatusChangedEvent;
use Illuminate\Events\Dispatcher;

class StatusListener
{
    public function afterProcessStatusChanged(AfterProcessStatusChangedEvent $event)
    {
        dd($event->getProcess()->currentStatus());
        exit;

    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterProcessStatusChangedEvent::class, self::class.'@afterProcessStatusChanged');
    }

}