<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/15
 * Time: 14:29
 */
namespace app\backend\modules\charts\modules\merchant\controllers;

use app\common\components\BaseController;
use Illuminate\Support\Facades\DB;

class StoreIncomeController extends BaseController
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
            $orderAndUnwithdraw = DB::table('yz_store as s')
                ->leftJoin('yz_plugin_store_order as so',function ($join) use ($searchTime) {
                    $join->on('s.id','so.store_id')->where('so.created_at','>=' ,$searchTime[0])->where('so.created_at','<=',$searchTime[1]);
                })
                ->leftJoin('yz_order as o', function ($join) {
                    $join->on('so.order_id','o.id')->where('o.status',3);
                })
                ->where('s.uniacid', $uniacid)
                ->selectRaw('ims_s.id,max(store_name) as name, max(thumb) as thumb_url, sum(if(has_settlement=1 and has_withdraw=0,amount,0)) as un_withdraw, sum(price) as price')
                ->groupBy('s.id')
                ->get();

            $withdraws = DB::table('yz_store as s')
                ->leftJoin('yz_member_income as sw', function ($join) use ($searchTime) {
                    $join->on('s.uid','sw.member_id')->where('sw.created_at','>=' ,$searchTime[0])->where('sw.created_at','<=',$searchTime[1]);
                })
                ->where('s.uniacid', $uniacid)
                ->selectRaw('ims_s.id, sum(if(ims_sw.status=0 and ims_sw.incometable_type like "%StoreOrder",ims_sw.amount,0)) as withdrawing, sum(if(ims_sw.status=1 and ims_sw.incometable_type like "%StoreOrder",ims_sw.amount,0)) as withdraw')
                ->groupBy('s.id')
                ->get();
        } else {
            $orderAndUnwithdraw = DB::table('yz_store as s')
                ->leftJoin('yz_plugin_store_order as so','s.id','so.store_id')
                ->leftJoin('yz_order as o', function ($join) {
                    $join->on('so.order_id','o.id')->where('o.status',3);
                })
                ->where('s.uniacid', $uniacid)
                ->selectRaw('ims_s.id,max(store_name) as name, max(thumb) as thumb_url, sum(if(has_settlement=1 and has_withdraw=0,amount,0)) as un_withdraw, sum(price) as price')
                ->groupBy('s.id')
                ->get();

            $withdraws = DB::table('yz_store as s')
                ->leftJoin('yz_member_income as sw','s.uid','sw.member_id')
                ->where('s.uniacid', $uniacid)
                ->selectRaw('ims_s.id, sum(if(ims_sw.status=0 and ims_sw.incometable_type like "%StoreOrder",ims_sw.amount,0)) as withdrawing, sum(if(ims_sw.status=1 and ims_sw.incometable_type like "%StoreOrder",ims_sw.amount,0)) as withdraw')
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

        return view('charts.merchant.store',[
            'storeTotal' => count($list),
            'unWithdrawTotal' => $unWithdrawTotal,
            'priceTotal' => $priceTotal,
            'withdrawingTotal' => $withdrawingTotal,
            'withdrawTotal' => $withdrawTotal,
            'list' => $list,
            'search' => $search,
        ])->render();
    }

}