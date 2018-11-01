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

class OrderBonusStatusJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        OrderPluginBonus::updateStatus($this->orderId);

        $incomeData = [];
        $incomeData['day_time'] = date('Y-m-d');
        $orderIncome = OrderIncomeCount::uniacid()->where('day_time', $incomeData['day_time'])->first();

        $orderModel = Order::find($this->orderId);

        $incomeData['undividend'] = OrderPluginBonus::uniacid()->where('order_id', $this->orderId)->sum('undividend');

        $incomeData['shop'] = $orderModel->price - OrderGoods::uniacid()->where('order_id', $this->orderId)->sum('goods_cost_price');
        if ($orderModel->is_plugin == 1) {
            $incomeData['supplier'] = SupplierOrder::uniacid()->where('order_id', $this->orderId)->first()->supplier_profit;
            $incomeData['shop'] = 0;
        }
        if ($orderModel->plugin_id == 31) {
            $incomeData['cashier'] = CashierOrder::uniacid()->where('order_id', $this->orderId)->first()->amount;
            $incomeData['shop'] = 0;
        }
        if ($orderModel->plugin_id == 32) {
            $incomeData['store'] = StoreOrder::uniacid()->where('order_id', $this->orderId)->first()->amount;
            $incomeData['shop'] = 0;
        }

        if ($orderIncome) {
            $orderIncome->shop += $incomeData['shop'];
            $orderIncome->supplier += $incomeData['supplier'];
            $orderIncome->cashier += $incomeData['cashier'];
            $orderIncome->store += $incomeData['store'];
            $orderIncome->undividend += $incomeData['undividend'];
            $orderIncome->save();
            return true;
        }
        $incomeData['uniacid'] = $orderModel->uniacid;
        OrderIncomeCount::create($incomeData);

    }
}