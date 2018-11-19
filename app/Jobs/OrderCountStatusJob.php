<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/9/19
 * Time: 下午3:37
 */

namespace app\Jobs;


use app\backend\modules\charts\models\OrderIncomeCount;
use app\common\events\order\CreatedOrderPluginBonusEvent;
use app\common\models\Order;
use app\common\models\order\OrderPluginBonus;
use app\common\models\OrderGoods;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yunshop\StoreCashier\common\models\CashierOrder;
use Yunshop\StoreCashier\common\models\StoreOrder;
use Yunshop\Supplier\common\models\SupplierOrder;

class OrderCountStatusJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;
    protected $status;

    public function __construct($orderId, $status)
    {
        $this->orderId = $orderId;
        $this->status = $status;
    }

    public function handle()
    {
        $order = OrderIncomeCount::where('order_id', $this->orderId)->first();
        if(!$order){
            return;
        }
        if ($order->status == -2) {
            return true;
        }
        $order->status = $this->status;
        $order->save();
        return true;
    }
}