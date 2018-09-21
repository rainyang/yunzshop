<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/18
 * Time: 下午3:46
 */

namespace app\Jobs;


use app\common\events\order\AfterOrderCreatedEvent;
use app\frontend\modules\order\models\PreOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class OrderCreatedEventQueueJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var PreOrder
     */
    protected $order;

    /**
     * AdminOperationLogQueueJob constructor.
     * @param PreOrder $order
     */
    public function __construct(PreOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        event(new AfterOrderCreatedEvent($this->order));
    }
}