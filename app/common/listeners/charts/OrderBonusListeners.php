<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/6
 * Time: 11:18
 */
namespace app\common\listeners\charts;

use app\backend\modules\charts\models\OrderIncomeCount;
use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\AfterOrderCreatedImmediatelyEvent;
use app\common\events\order\AfterOrderPaidImmediatelyEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderReceivedImmediatelyEvent;
use app\common\events\order\AfterOrderRefundedEvent;
use app\common\models\order\OrderPluginBonus;
use app\Jobs\OrderBonusStatusJob;
use app\Jobs\OrderCountContentJob;
use app\Jobs\OrderCountIncomeJob;
use app\Jobs\OrderCountStatusJob;
use app\Jobs\OrderMemberMonthJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\StoreCashier\common\models\CashierOrder;
use Yunshop\StoreCashier\common\models\StoreOrder;
use Yunshop\Supplier\common\models\SupplierOrder;
use app\common\models\Order;

class OrderBonusListeners
{
    use DispatchesJobs;
    protected $orderModel;

    public function subscribe($events)
    {
        //下单
        $events->listen(AfterOrderCreatedImmediatelyEvent::class, OrderBonusListeners::class. '@addCount');

        $events->listen(AfterOrderPaidImmediatelyEvent::class, OrderBonusListeners::class . '@orderPay');

        //收货之后 更改订单状态
        $events->listen(AfterOrderReceivedEvent::class, OrderBonusListeners::class . '@updateBonus');
        //收货之后 更改订单状态
        $events->listen(AfterOrderReceivedImmediatelyEvent::class, OrderBonusListeners::class . '@receivedBonus');

        //订单取消
        $events->listen(AfterOrderCanceledEvent::class, OrderBonusListeners::class. '@cancel');

        //订单退款
        $events->listen(AfterOrderRefundedEvent::class, OrderBonusListeners::class. '@refunded');

    }

    public function orderPay(AfterOrderPaidImmediatelyEvent $event)
    {
        OrderIncomeCount::updateByOrderId($event->getOrderModel()->id, ['status' => $event->getOrderModel()->status]);
    }


    public function addCount(AfterOrderCreatedImmediatelyEvent $event)
    {
        $orderModel = Order::find($event->getOrderModel()->id);
        $this->dispatch(new OrderCountContentJob($orderModel));
    }

    public function updateBonus(AfterOrderReceivedEvent $event)
    {
        $this->dispatch(new OrderMemberMonthJob($event->getOrderModel()));
        $this->dispatch(new OrderBonusStatusJob($event->getOrderModel()->id));
//        $this->dispatch((new OrderCountIncomeJob($event->getOrderModel())));
    }
    
    public function receivedBonus(AfterOrderReceivedImmediatelyEvent $event)
    {
        $orderModel = $event->getOrderModel();

        $data = [];

        if ($orderModel->is_plugin == 1 || $orderModel->plugin_id == 92) {
            $data['supplier'] = SupplierOrder::where('order_id', $orderModel->id)->sum('supplier_profit');
        }
        if ($orderModel->plugin_id == 31) {
            $data['cost_price'] = $data['cashier'] = CashierOrder::where('order_id', $orderModel->id)->sum('amount');
        }
        if ($orderModel->plugin_id == 32) {
            $data['cost_price'] = $data['store'] = StoreOrder::where('order_id', $orderModel->id)->sum('amount');
        }
        $data['status'] = $orderModel->status;
        OrderIncomeCount::updateByOrderId($event->getOrderModel()->id, $data);
    }

    public function cancel(AfterOrderCanceledEvent $event)
    {
        OrderIncomeCount::updateByOrderId($event->getOrderModel()->id, ['status' => -1]);
    }

    public function refunded(AfterOrderRefundedEvent $event)
    {
        OrderIncomeCount::updateByOrderId($event->getOrderModel()->id, ['status' => -2]);
    }


}