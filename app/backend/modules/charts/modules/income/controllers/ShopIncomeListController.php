<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/16
 * Time: 11:51
 */
namespace app\backend\modules\charts\modules\income\controllers;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\models\order\OrderPluginBonus;

class ShopIncomeListController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $pageSize = 10;
        $list = OrderPluginBonus::uniacid()
            ->selectRaw('sum(undividend) as undividend,order_id')
            ->groupBy('order_id')
            ->with(['hasManyOrderGoods', 'hasOneStoreOrder', 'hasOneSupplierOrder', 'hasOneCashierOrder'])
            ->get();
//            ->toArray();
//            ->paginate($pageSize);
        dd($list);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.income.shop_income_list',[

        ])->render();
    }

}