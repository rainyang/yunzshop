<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/15
 * Time: 14:29
 */
namespace app\backend\modules\charts\modules\merchant\controllers;


use app\common\components\BaseController;
use app\backend\modules\charts\models\Supplier;
use Illuminate\Support\Facades\DB;
use Yunshop\Supplier\supplier\models\SupplierOrderJoinOrder;

class SupplierIncomeController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $searchTime = [];
        $search = \YunShop::request()->search;
        if ($search['is_time'] && $search['time']) {
            $searchTime[] = strtotime($search['time']['start']);
            $searchTime[] = strtotime($search['time']['end']);
        }
        $uniacid = \YunShop::app()->uniacid;
        $list = [];
        if ($searchTime) {
            $orderAndUnwithdraw = DB::table('yz_supplier as s')
                ->leftJoin('yz_supplier_order as so', function ($join)use ($searchTime) {
                    $join->on('s.id','so.supplier_id')->where('so.created_at','>=' ,$searchTime[0])->where('so.created_at','<=',$searchTime[1]);
                })
                ->leftJoin('yz_order as o', function ($join) use ($searchTime) {
                    $join->on('so.order_id','o.id')->where('o.status',3);
                })

                ->where('s.uniacid', $uniacid)
                ->selectRaw('ims_s.id,max(username) as name, sum(if(apply_status=0,supplier_profit,0)) as un_withdraw, sum(price) as price')
                ->groupBy('s.id')
                ->get();
            $withdraws = DB::table('yz_supplier as s')
                ->leftJoin('yz_supplier_withdraw as sw', function ($join) use ($searchTime) {
                    $join->on('s.id','sw.supplier_id')->where('sw.created_at','>=' ,$searchTime[0])->where('sw.created_at','<=',$searchTime[1]);
                })
                ->where('s.uniacid', $uniacid)
                ->selectRaw('ims_s.id, sum(if(ims_sw.status in (1,2),ims_sw.money,0)) as withdrawing, sum(if(ims_sw.status=3,ims_sw.money,0)) as withdraw')
                ->groupBy('s.id')
                ->get();
        } else {
            $orderAndUnwithdraw = DB::table('yz_supplier as s')
            ->leftJoin('yz_supplier_order as so','s.id','so.supplier_id')
                ->leftJoin('yz_order as o', function ($join) {
                    $join->on('so.order_id','o.id')->where('o.status',3);
                })

                ->where('s.uniacid', $uniacid)
                ->selectRaw('ims_s.id,max(username) as name, sum(if(apply_status=0,supplier_profit,0)) as un_withdraw, sum(price) as price')
                ->groupBy('s.id')
                ->get();
            $withdraws = DB::table('yz_supplier as s')
            ->leftJoin('yz_supplier_withdraw as sw','s.id','sw.supplier_id')
                ->where('s.uniacid', $uniacid)
                ->selectRaw('ims_s.id, sum(if(ims_sw.status in (1,2),ims_sw.money,0)) as withdrawing, sum(if(ims_sw.status=3,ims_sw.money,0)) as withdraw')
                ->groupBy('s.id')
                ->get();
        }

        foreach($orderAndUnwithdraw as $key=>$vo){
            $list[] = array_merge($vo, $withdraws[$key]);
        }
        $totalAmount = collect($list);
        $unWithdrawTotal = $totalAmount->sum('un_withdraw');
        $priceTotal = $totalAmount->sum('price');
        $withdrawingTotal = $totalAmount->sum('withdrawing');
        $withdrawTotal = $totalAmount->sum('withdraw');
//        SupplierOrderJoinOrder::IsPlugin()->where('uniacid', 3)->status(3)->whereHas('beLongsToSupplierOrder', function ($query) {
//            $query->where('supplier_id', 1);
//        });
//        $supplierTotal = Supplier::uniacid()
////            ->selectRaw('count(id) as supplierTotal')
//            ->with([
//                'hasManySupplierOrder' => function($q) {
//                    $q->selectRaw('sum(if(apply_status=0,supplier_profit,0)) as un_withdraw_amount, supplier_id')->groupBy('supplier_id');
//                },
//                'hasManySupplierWithdraw' => function($q) {
//                    $q->selectRaw('sum(if(status in (3),money,0)) as withdraw_amount, sum(if(status in (1,2),money,0)) as withdrawing_amount, supplier_id')->groupBy('supplier_id');
//                },
//                'hasManySupplierOrderCount' => function($q) {
////                    $q->whereHas('hasOneOrder',function ($q){
////                        $q->where('status', 3);
////                    });
//                    $q->hasOneOrder->price;
//                }
//            ])
//            ->get()->toArray();
        return view('charts.merchant.supplier',[
            'supplierTotal' => count($list),
            'unWithdrawTotal' => $unWithdrawTotal,
            'priceTotal' => $priceTotal,
            'withdrawingTotal' => $withdrawingTotal,
            'withdrawTotal' => $withdrawTotal,
            'list' => $list,
            'search' => $search,
        ])->render();
    }
}