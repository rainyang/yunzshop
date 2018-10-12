<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/18
 * Time: 下午3:46
 */

namespace app\Jobs;

use app\common\events\order\AfterOrderPaidEvent;
use app\common\models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class OrderPaidEventQueueJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Order
     */
    protected $order;

    /**
     * AdminOperationLogQueueJob constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
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
        DB::transaction(function () {

            event(new AfterOrderPaidEvent($this->order));
        });
    }
}