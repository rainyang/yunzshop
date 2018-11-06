<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/16
 * Time: 11:52
 */

namespace app\backend\modules\charts\modules\income\controllers;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\backend\modules\charts\models\Order;
use app\common\models\order\OrderPluginBonus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yunshop\StoreCashier\common\models\CashierOrder;
use Yunshop\StoreCashier\common\models\StoreOrder;
use Yunshop\Supplier\common\models\SupplierOrder;

class ShopIncomeStatisticsController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {

        $pageSize = 10;
        $search = \YunShop::request()->search;
        $list = Order::search($search)
            ->selectRaw('sum(undividend) as undividend, FROM_UNIXTIME(created_at,"%Y-%m-%d")as date, sum(price) as price')
            ->groupBy(DB::raw("FROM_UNIXTIME(UNIX_TIMESTAMP(created_at),'%Y-%m-%d')"))
            ->with([
                'hasOneOrderGoods' => function($q) {
                    $q->selectRaw('sum(goods_cost_price) as cost_price, order_id')->groupBy('order_id');
                },
                'hasOneStoreOrder',
                'hasOneSupplierOrder',
                'hasOneCashierOrder',
            ])
//            ->orderBy('order_id', 'desc')
            ->get()->toArray();
//            ->paginate($pageSize);
        dd($list);



        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.income.shop_income_statistics',[
            'list' => $list,
            'pager' => $pager,
            'search' => $search,
        ])->render();
    }

    public function statistics()
    {
        $pageSize = 10;
        $search = \YunShop::request()->search;
        $list = Order::uniacid()
            ->where('status', 3)
            ->selectRaw('FROM_UNIXTIME(created_at,"%Y-%m-%d")as date, sum(price) as price')
            ->groupBy(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"))
            ->with([
                'hasManyOrderGoods' => function($q) {
                    $q->selectRaw('sum(goods_cost_price) as cost_price, order_id')->groupBy(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"));
                },
                'hasManyStoreOrder',
                'hasManySupplierOrder',
                'hasManyCashierOrder',
                'hasManyOrderPluginBonus'
            ])
            ->orderBy("date","desc")->take(10)
//            ->orderBy('order_id', 'desc')
            ->get()->toArray();
//            ->paginate($pageSize);
//        $list = DB::table('yz_order as o')
//            ->where('o.status', 3)
//            ->selectRaw('FROM_UNIXTIME(created_at,"%Y-%m-%d") as date, sum(price) as price')
//            ->join('yz_order_goods as og', 'date', '=', 'og.FROM_UNIXTIME(o.created_at,"%Y-%m-%d")')
//            ->get();
        dd($list);



        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.income.shop_income_statistics',[
            'list' => $list,
            'pager' => $pager,
            'search' => $search,
        ])->render();
    }

    /**
     *
     */
    public function count()
    {
        $date_start = Carbon::now()->yesterday()->startOfDay()->getTimestamp();

        $date_end = Carbon::now()->yesterday()->endOfDay()->getTimestamp();

        $shopOrder = Order::uniacid()->whereIn('finish_at', [$date_start, $date_end])->where('status',3)->sum('price');
        $storeOrder = storeOrder::uniacid()->whereIn('created_at', [$date_start, $date_end])->where('has_settlement', 1)->sum('amount');
        $supplierOrder = cashierOrder::uniacid()->whereIn('created_at', [$date_start, $date_end])->where('has_settlement', 1)->sum('amount');
        $supplierOrder = supplierOrder::uniacid()->whereIn('created_at', [$date_start, $date_end])->sum('supplier_profit');
        $undividend = OrderPluginBonus::uniacid()->whereIn('created_at', [$date_start, $date_end])->where('status', 1)->sum('undividend');
        dd($date_end);
    }
}