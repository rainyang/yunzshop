<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/4
 * Time: 11:07
 */

namespace app\backend\modules\charts\modules\order\controllers;


use app\backend\modules\charts\controllers\ChartsController;
use app\backend\modules\charts\models\OrderIncomeCount;
use app\common\helpers\PaginationHelper;
use app\backend\modules\charts\models\Order;

class OrderDividendController extends ChartsController
{
    const PAGE_SIZE = 10;

    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $pageSize = 20;
        $search = \YunShop::request()->get('search');
        $list = OrderIncomeCount::uniacid()->search($search)->orderBy('id','desc')->paginate($pageSize);
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        if ($search['statistics']) {
            $total = $list['total'];
            $commission_total = OrderIncomeCount::uniacid()->search($search)->sum('price');
        }

        if(!$search['time']){
            $search['time']['start'] = date("Y-m-d H:i:s",time());
            $search['time']['end'] = date("Y-m-d H:i:s",time());
        }
        return view('charts.order.order_dividend', [
            'list' => $list,
            'pager' => $pager,
            'search' => $search,
            'total' => $total,
            'commission_total' => $commission_total,
        ])->render();
    }

}