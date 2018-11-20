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
use app\common\services\ExportService;
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
        array_multisort(array_column($list,'price'),SORT_DESC,$list);
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

    //todo 以后有问题再用关联关系
    public function count()
    {
        $supplierTotal = Supplier::uniacid()
            ->select(['id','username'])
            ->where('status', 1)
            ->with([
                'hasOneSupplierOrder' => function($q) {
                    $q->selectRaw('sum(if(apply_status=0,supplier_profit,0)) as un_withdraw_amount, supplier_id')->groupBy('supplier_id');
                },
                'hasOneSupplierWithdraw' => function($q) {
                    $q->selectRaw('sum(if(status in (3),money,0)) as withdraw_amount, sum(if(status in (1,2),money,0)) as withdrawing_amount, supplier_id')->groupBy('supplier_id');
                },
                'hasManyOrder' => function($q) {
                    $q->where('status', 3);
                }
            ])
            ->get()->toArray();
    }

    public function export()
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
        array_multisort(array_column($list,'price'),SORT_DESC, $list);
        $builder = Supplier::uniacid();
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);
        $file_name = date('Ymdhis', time()) . '供应商收益列表导出';
        $export_data[0] = ['排行','供应商','交易完成总额','未提现收入','提现中收入','已提现收入'];
        foreach ($list as $key => $item) {
            $export_data[$key+1] = [
                $key + 1,
                $item['name'],
                $item['price'] ?: '0.00',
                $item['un_withdraw'] ?: '0.00',
                $item['withdrawing'] ?: '0.00',
                $item['withdraw'] ?: '0.00',
            ];
        }
        $export_model->export($file_name, $export_data, 'charts.merchant.supplier-income.index');

    }
}