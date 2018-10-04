<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/4
 * Time: 11:07
 */

namespace app\backend\modules\charts\modules\order\controllers;


use app\backend\modules\charts\controllers\ChartsController;
use app\common\helpers\PaginationHelper;
use app\backend\modules\charts\models\Order;

class OrderDividendController extends ChartsController
{
    const PAGE_SIZE = 10;

    public function count()
    {
        $params = \YunShop::request()->get();
        $orderModel = Order::orders($params['search']);
        $requestSearch = \YunShop::request()->get('search');
        $requestSearch['plugin'] = 'fund';
        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return !empty($item);
            });
        }

        $list['total_price'] = $orderModel->sum('price');
        $list += $orderModel->orderBy('id', 'desc')->paginate(self::PAGE_SIZE)->appends(['button_models'])->toArray();
dd($list);
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        return view('charts.order.order_dividend', [
            'list' => $list,
            'pager' => $pager,
            'search' => $requestSearch,
        ])->render();
    }

}