<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/22 下午5:53
 * Email: livsyitian@163.com
 */

namespace app\common\listeners;


use app\common\events\MessageEvent;
use app\Jobs\MessageJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

class MessageListener implements ShouldQueue
{

    use DispatchesJobs;


    public function subscribe(MessageEvent $event)
    {
        $event->listen(MessageEvent::class, MessageListener::class . "@handel");
    }


    public function handel($event)
    {
        /**
         * @var $event MessageEvent
         */
        $this->dispatch(new MessageJob($event));
    }

}
