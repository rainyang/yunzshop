<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/16
 * Time: 11:51
 */
namespace app\backend\modules\charts\modules\income\controllers;

use app\common\components\BaseController;
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
            ->whereRaw('sum(undividend) as undividend')
            ->with(['hasManyOrderGoods', 'hasOneStoreOrder', 'hasOneSupplierOrder', 'hasOneCashierOrder'])
            ->groupBy('order_id')
            ->paginate($pageSize);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.income.shop_income_list',[

        ])->render();
    }

}