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

            $total = OrderIncomeCount::uniacid()->search($search)
                ->selectRaw('sum(price) as price, sum(cost_price) as cost_price')
                ->selectRaw('sum(commission) as commission, sum(dispatch_price) as dispatch_price')
                ->selectRaw('sum(team_dividend) as team_dividend, sum(area_dividend) as area_dividend')
                ->selectRaw('sum(micro_shop) as micro_shop, sum(merchant) as merchant')
                ->selectRaw('sum(merchant_center) as merchant_center, sum(love) as love')
                ->selectRaw('sum(point) as point')->get()->toArray();
            $total['count'] = $list['total'];
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
        ])->render();
    }

}