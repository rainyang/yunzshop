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
use app\common\components\BaseController;

use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;

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
        $this->orderModel = Order::orders($params['search'])->isPlugin()->pluginId();
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
            $export_page = request()->export_page ? request()->export_page : 1;
            $export_model = new ExportService($orders, $export_page);
            if (!$export_model->builder_model->isEmpty()) {
                $file_name = date('Ymdhis', time()) . '订单导出';//返现记录导出
                $export_data[0] = $this->getColumns();
                foreach ($export_model->builder_model->toArray() as $key => $item) {
                    $export_data[$key + 1] = [
                        $item['order_sn'],
                        $item['has_one_order_pay']['pay_sn'],
                        $this->getNickname($item['belongs_to_member']['nickname']),
                        $item['address']['realname'],
                        $item['address']['mobile'],
                        $item['address']['address'],
                        $this->getGoods($item, 'goods_title'),
                        $this->getGoods($item, 'goods_sn'),
                        $this->getGoods($item, 'total'),
                        $item['pay_type_name'],
                        $item['goods_price'],
                        $item['dispatch_price'],
                        $item['price'],
                        $this->getGoods($item, 'cost_price'),
                        $item['status_name'],
                        $item['create_time'],
                        !empty(strtotime($item['pay_time']))?$item['pay_time']:'',
                        !empty(strtotime($item['send_time']))?$item['send_time']:'',
                        !empty(strtotime($item['finish_time']))?$item['finish_time']:'',
                        $item['express']['express_company_name'],
                        $item['express']['express_sn'],
                        $item['has_one_order_remark']['remark'],
                    ];
                }
                $export_model->export($file_name, $export_data, 'order.list.index');
            }
        }
    }

    private function getColumns()
    {
        return ["订单编号", "支付单号", "粉丝昵称", "会员姓名", "联系电话", "收货地址", "商品名称", "商品编码", "商品数量", "支付方式", "商品小计", "运费", "应收款", "成本价", "状态", "下单时间", "付款时间", "发货时间", "完成时间", "快递公司", "快递单号", "订单备注"];
    }

    private function getGoods($order, $key)
    {
        $goods_title = '';
        $goods_sn = '';
        $total = '';
        $cost_price = 0;
        foreach ($order['has_many_order_goods'] as $goods) {
            $res_title = $goods['title'];
            $res_title = str_replace('-', '，', $res_title);
            $res_title = str_replace('+', '，', $res_title);
            $res_title = str_replace('/', '，', $res_title);
            $res_title = str_replace('*', '，', $res_title);
            $res_title = str_replace('=', '，', $res_title);

            if ($goods['goods_option_title']) {
                $res_title .= '['. $goods['goods_option_title'] .']';
            }

            $goods_title .= '【' . $res_title . '*' . $goods['total'] . '】';
            $goods_sn .= '【' . $goods['goods_sn'].'】';
            $total .= '【' . $goods['total'].'】';
            $cost_price += $goods['goods_cost_price'];
        }
        $res = [
            'goods_title' => $goods_title,
            'goods_sn' => $goods_sn,
            'total' => $total,
            'cost_price' => $cost_price
        ];
        return $res[$key];
    }

    private function getNickname($nickname)
    {
        if (substr($nickname, 0, strlen('=')) === '=') {
            $nickname = '，' . $nickname;
        }
        return $nickname;
    }
}