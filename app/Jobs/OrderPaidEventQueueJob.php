<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/18
 * Time: 下午3:46
 */

namespace app\Jobs;

use app\common\events\order\AfterOrderPaidEvent;
use app\common\facades\Setting;
use app\common\models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class OrderPaidEventQueueJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Order
     */
    protected $order;

    /**
     * OrderPaidEventQueueJob constructor.
     * @param $orderId
     */
    public function __construct($orderId)
    {
        $this->order = Order::find($orderId);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function () {
            \YunShop::app()->uniacid = $this->order->uniacid;
            Setting::$uniqueAccountId = $this->order->uniacid;
            if($this->order->orderPaidJob->status == 'finished'){
                return;
            }
            event(new AfterOrderPaidEvent($this->order));
            $this->order->orderPaidJob->status = 'finished';
            $this->order->orderPaidJob->save();
        });
    }
}