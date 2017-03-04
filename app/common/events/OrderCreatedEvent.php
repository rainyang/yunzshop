<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/3
 * Time: 上午11:44
 */

namespace app\common\events;


class OrderCreatedEvent extends Event
{
    use SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($order=[])
    {
        $this->order=$order;
        echo 'TestFailEventFail fire';
        echo "<br/>";
        print_r($order);

        echo "<br/>";
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}