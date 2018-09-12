<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/13 下午2:48
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\controllers;

use app\common\helpers\PaginationHelper;
use app\common\models\statistic\MemberRelationOrderStatisticsModel;

class OfflineOrderController extends OfflineCountController
{
    public function index()
    {
        $pageSize = 10;
        $search = \YunShop::request()->search;

        $list = MemberRelationOrderStatisticsModel::getMember($search)->orderBy('team_order_quantity', 'desc')->paginate($pageSize);
        $page = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.member.offline_order', [
            'page' => $page,
            'search' => $search,
            'list' => $list,
        ])->render();
    }
}
