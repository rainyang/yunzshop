<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/11
 * Time: 11:11
 */

namespace app\backend\modules\charts\modules\order\controllers;


use app\backend\modules\charts\controllers\ChartsController;
use app\backend\modules\charts\models\Order;
use Illuminate\Support\Facades\DB;

class TransactionAmountController extends ChartsController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function count()
    {
        $waitPayOrder = 0.00;
        $waitSendOrder = 0.00;
        $waitReceiveOrder = 0.00;
        $completedOrder = 0.00;
        $uniacid = \YunShop::app()->uniacid;
        $search = \YunShop::request()->search;
        if ($search['is_time']) {
            $searchTime['start'] = strtotime($search['time']['start']);
            $searchTime['end'] = strtotime($search['time']['end']);
            $orderData = DB::select('select sum(if(plugin_id=31,price,0)) as cashier, sum(if(plugin_id=32,price,0)) as store, sum(if(is_plugin=1,price,0)) as supplier, sum(if(is_plugin=0 && plugin_id=0,price,0)) as shop, status from ims_yz_order where uniacid='.$uniacid. ' and created_at >= '.$searchTime['start'].' and created_at <= '.$searchTime['end'].' GROUP BY status');
            $totalOrder = DB::select('select sum(if(plugin_id=31,price,0)) as cashier, sum(if(plugin_id=32,price,0)) as store, sum(if(is_plugin=1,price,0)) as supplier, sum(if(is_plugin=0 && plugin_id=0,price,0)) as shop from ims_yz_order where uniacid='.$uniacid . ' and created_at >= '.$searchTime['start'].' and created_at <= '.$searchTime['end']);
        } else {
            $orderData = DB::select('select sum(if(plugin_id=31,price,0)) as cashier, sum(if(plugin_id=32,price,0)) as store, sum(if(is_plugin=1,price,0)) as supplier, sum(if(is_plugin=0 && plugin_id=0,price,0)) as shop, status from ims_yz_order where uniacid='.$uniacid. ' GROUP BY status');
            $totalOrder = DB::select('select sum(if(plugin_id=31,price,0)) as cashier, sum(if(plugin_id=32,price,0)) as store, sum(if(is_plugin=1,price,0)) as supplier, sum(if(is_plugin=0 && plugin_id=0,price,0)) as shop from ims_yz_order where uniacid='.$uniacid);
        }
        foreach ($orderData as $order)
        {
            switch ($order['status']) {
                case 0:$waitPayOrder = $order;break;
                case 1:$waitSendOrder = $order;break;
                case 2:$waitReceiveOrder = $order;break;
                case 3:$completedOrder = $order;break;
                default : break;
            }
        }
//        $shopWaitPayOrder = Order::IsPlugin()->PluginId(0)->WaitPay()->sum('price');
//        $storeWaitPayOrder = Order::IsPlugin()->PluginId(32)->WaitPay()->sum('price');
//        $cashierWaitPayOrder = Order::IsPlugin()->PluginId(31)->WaitPay()->sum('price');
//        $supplierWaitPayOrder = Order::where('is_plugin', 1)->PluginId(0)->WaitPay()->sum('price');
//        $shopWaitSendOrder = Order::IsPlugin()->PluginId(0)->WaitSend()->sum('price');
//        $storeWaitSendOrder = Order::IsPlugin()->PluginId(32)->WaitSend()->sum('price');
//        $supplierWaitSendOrder = Order::where('is_plugin', 1)->PluginId(0)->WaitSend()->sum('price');
//        $shopWaitReceiveOrder = Order::IsPlugin()->PluginId(0)->WaitReceive()->sum('price');
//        $storeWaitReceiveOrder = Order::IsPlugin()->PluginId(32)->WaitReceive()->sum('price');
//        $supplierWaitReceiveOrder = Order::where('is_plugin', 1)->PluginId(0)->WaitReceive()->sum('price');
//        $shopCompletedOrder = Order::IsPlugin()->PluginId(0)->Completed()->sum('price');
//        $storeCompletedOrder = Order::IsPlugin()->PluginId(32)->Completed()->sum('price');
//        $cashierCompletedOrder = Order::IsPlugin()->PluginId(31)->Completed()->sum('price');
//        $supplierCompletedOrder = Order::where('is_plugin', 1)->PluginId(0)->Completed()->sum('price');
//        $shopTotalOrder = Order::IsPlugin()->PluginId(0)->sum('price');
//        $storeTotalOrder = Order::IsPlugin()->PluginId(32)->sum('price');
//        $cashierTotalOrder = Order::IsPlugin()->PluginId(31)->sum('price');
//        $supplierTotalOrder = Order::where('is_plugin', 1)->PluginId(0)->sum('price');
//        dd($shopWaitPayOrder, $storeWaitPayOrder, $cashierWaitPayOrder, $supplierWaitPayOrder);
        return view('charts.order.transaction_amount', [
//            'shopWaitPayOrder' => $shopWaitPayOrder,
//            'storeWaitPayOrder' => $storeWaitPayOrder,
//            'cashierWaitPayOrder' => $cashierWaitPayOrder,
//            'supplierWaitPayOrder' => $supplierWaitPayOrder,
//            'shopWaitSendOrder' => $shopWaitSendOrder,
//            'storeWaitSendOrder' => $storeWaitSendOrder,
//            'supplierWaitSendOrder' => $supplierWaitSendOrder,
//            'shopWaitReceiveOrder' => $shopWaitReceiveOrder,
//            'storeWaitReceiveOrder' => $storeWaitReceiveOrder,
//            'supplierWaitReceiveOrder' => $supplierWaitReceiveOrder,
//            'shopCompletedOrder' => $shopCompletedOrder,
//            'storeCompletedOrder' => $storeCompletedOrder,
//            'cashierCompletedOrder' => $cashierCompletedOrder,
//            'supplierCompletedOrder' => $supplierCompletedOrder,
//            'shopTotalOrder' => $shopTotalOrder,
//            'storeTotalOrder' => $storeTotalOrder,
//            'cashierTotalOrder' => $cashierTotalOrder,
//            'supplierTotalOrder' => $supplierTotalOrder,
            'waitPayOrder' => $waitPayOrder,
            'waitSendOrder' => $waitSendOrder,
            'waitReceiveOrder' => $waitReceiveOrder,
            'completedOrder' => $completedOrder,
            'totalOrder' => $totalOrder[0],
            'search' => $search,
        ])->render();
    }
}