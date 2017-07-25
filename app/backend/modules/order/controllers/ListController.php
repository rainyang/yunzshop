<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/4
 * Time: 上午9:09
 */

namespace app\backend\modules\order\controllers;

use app\backend\modules\order\models\Order;
use app\backend\modules\order\models\OrderJoinOrderGoods;
use app\backend\modules\order\services\ExportService;
use app\common\components\BaseController;

use app\common\helpers\PaginationHelper;

class ListController extends BaseController
{
    /**
     * 页码
     */
    const PAGE_SIZE = 10;
    protected $orderModel;
    public function __construct()
    {
        parent::__construct();
        $params = \YunShop::request()->get();
        $this->orderModel = Order::orders($params['search']);
    }

    public function index()
    {
        $this->export($this->orderModel);
        return view('order.index', $this->getData())->render();
    }

    public function waitPay()
    {
        $this->orderModel->waitPay();
        $this->export($this->orderModel->waitPay());
        return view('order.index', $this->getData())->render();
    }

    public function waitSend()
    {

        $this->orderModel->waitSend();
        $this->export($this->orderModel->waitSend());
        return view('order.index', $this->getData())->render();
    }

    public function waitReceive()
    {
        $this->orderModel->waitReceive();
        $this->export($this->orderModel->waitReceive());
        return view('order.index', $this->getData())->render();
    }

    public function completed()
    {

        $this->orderModel->completed();
        $this->export($this->orderModel->completed());
        return view('order.index', $this->getData())->render();
    }

    public function cancelled()
    {
        $this->orderModel->cancelled();
        $this->export($this->orderModel->cancelled());
        return view('order.index', $this->getData())->render();
    }

    protected function getData()
    {
        /*$params = [
            'search' => [
                'ambiguous' => [
                    'field' => 'order_goods',
                    'string' => '春',
                ],
                'pay_type' => 1,
                'time_range' => [
                    'field' => 'create_time',
                    'range' => [1458425047, 1498425047]
                ]
            ]
        ];*/
        $requestSearch = \YunShop::request()->get('search');
        $requestSearch['plugin'] = 'fund';
        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return !empty($item);
            });
        }

        $list['total_price'] = $this->orderModel->sum('price');
        $list += $this->orderModel->orderBy('id', 'desc')->paginate(self::PAGE_SIZE)->appends(['button_models'])->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        //dd($list);
        //exit;
//        dd($requestSearch);
//        exit;

        $data = [
            'list' => $list,
            'total_price' => $list['total_price'],
            'pager' => $pager,
            'requestSearch' => $requestSearch,
            'var' => \YunShop::app()->get(),
            'url' => \Request::query('route'),
            'include_ops' => 'order.ops',
            'detail_url' => 'order.detail'
        ];
        return $data;
    }

    public function export($orders)
    {
        if (\YunShop::request()->export == 1) {
            $orders = $orders->get();
            if (!$orders->isEmpty()) {
                $export_class = new ExportService();
                $export_class->export($orders->toArray());
            }
        }
    }
}