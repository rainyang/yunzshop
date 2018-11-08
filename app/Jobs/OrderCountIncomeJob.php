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

class OrderCountIncomeJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        $incomeData = [];
        $incomeData['day_time'] = date('Y-m-d');
        $orderIncome = OrderIncomeCount::uniacid()->where('order_id', $this->orderId)->first();

        $orderModel = Order::find($this->orderId);
        if ($orderModel->is_plugin == 1) {
            $incomeData['supplier'] = SupplierOrder::uniacid()->where('order_id', $this->orderId)->sum('supplier_profit');
        }
        if ($orderModel->plugin_id == 31) {
            $incomeData['cashier'] = CashierOrder::uniacid()->where('order_id', $this->orderId)->sum('amount');
        }
        if ($orderModel->plugin_id == 32) {
            $incomeData['store'] = StoreOrder::uniacid()->where('order_id', $this->orderId)->sum('amount');
        }

        if ($orderIncome) {
            $orderIncome->supplier = $incomeData['supplier'];
            $orderIncome->cashier = $incomeData['cashier'];
            $orderIncome->store = $incomeData['store'];
            $orderIncome->status = $orderModel->status;
            $orderIncome->save();
            return true;
        }
    }
}