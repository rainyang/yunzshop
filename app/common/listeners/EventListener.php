<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 23/02/2017
 * Time: 21:48
 */

namespace app\common\listeners;


use app\common\events\TestFailEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventListener implements ShouldQueue
{

    public function __construct()
    {
        //
    }

    public function handle(TestFailEvent $event)
    {
        echo "<br/>";
        var_dump($event->messages);

        echo "这是第一个事件!";
        echo "<br/>";
    }

}