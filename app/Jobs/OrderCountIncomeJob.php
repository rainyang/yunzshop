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

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        $orderIncome = OrderIncomeCount::uniacid()->where('order_id', $this->order->id)->first();

        if ($orderIncome) {
            if ($this->order->is_plugin == 1 || $this->order->plugin_id == 92) {
                $supplier = SupplierOrder::where('order_id', $this->order->id)->sum('supplier_profit');
                $orderIncome->supplier = $supplier;
            }
            if ($this->order->plugin_id == 31) {
                $cashier = CashierOrder::where('order_id', $this->order->id)->sum('amount');
                $orderIncome->cashier = $cashier;
                $orderIncome->cost_price = $cashier;
            }
            if ($this->order->plugin_id == 32) {
                $store = StoreOrder::where('order_id', $this->order->id)->sum('amount');
                $orderIncome->store = $store;
                $orderIncome->cost_price = $store;
            }
            $orderIncome->save();
            return true;
        }
    }
}