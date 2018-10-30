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
use app\common\models\order\OrderPluginBonus;
use Illuminate\Support\Facades\DB;

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
        $list = OrderPluginBonus::search($search)
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
}