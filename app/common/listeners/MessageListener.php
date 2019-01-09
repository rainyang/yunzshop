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
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

class MessageListener implements ShouldQueue
{

    use DispatchesJobs;


    public function subscribe(Dispatcher $event)
    {
        $event->listen(MessageEvent::class, MessageListener::class . "@handle");
    }


    public function handle($event)
    {
        /**
         * @var $event MessageEvent
         */
        $this->dispatch(new MessageJob($event));
    }

}
