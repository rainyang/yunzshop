<?php

namespace app\backend\modules\charts\modules\member\controllers;

use app\backend\modules\charts\modules\member\models\DistributionOrder;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;

class OfflineCommissionOrderController extends BaseController
{
    public function index()
    {
        $pageSize = 10;
        $search = \YunShop::request()->search;

        $list = DistributionOrder::search($search)->orderBy('team_commission_order_prices', 'desc')->paginate($pageSize);
        $page = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.member.offline_team_commission_order.blade', [
            'page' => $page,
            'search' => $search,
            'list' => $list,
        ])->render();
    }
}
