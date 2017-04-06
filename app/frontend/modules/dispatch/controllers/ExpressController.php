<?php

namespace app\frontend\modules\dispatch\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\frontend\modules\order\models\Order;
use Ixudra\Curl\Facades\Curl;
use \Request;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/6
 * Time: 下午4:03
 */
class ExpressController extends ApiController
{
    public function index(Request $request)
    {
        $order_id = $request->query('order_id');
        $order = Order::find($order_id);
        if (!isset($order)) {
            throw new AppException('未找到订单');
        }
        if ($order->status < Order::WAIT_RECEIVE) {
            throw new AppException('订单未发货');
        }
        if (!isset($order->express)) {
            throw new AppException('未找到配送信息');
        }
        //$data
        $express = $this->getExpress($order->express->express_code, $order->express->express_sn);
        $data['express_sn'] = $order->express->express_sn;
        $data['company_name'] = $order->express->express_company_name;
        $data['data'] = $express['data'];
        $data['thumb'] = $order->hasManyOrderGoods[0]->thumb;
        $this->successJson('成功', $data);
    }

    private function getExpress($express, $express_sn)
    {
        $url = sprintf('https://m.kuaidi100.com/query?type=%s&postid=%s&id=1&valicode=&temp=%s', $express, $express_sn, time());

        $result = Curl::to($url)
            ->asJsonResponse(true)->get();
        if (empty($result)) {
            return array();
        }
        $result['status_name'] = $this->expressStatusName($result['state']);
        return $result;
    }

    private function expressStatusName($key)
    {
        $state_name_map = [
            0 => '在途',
            1 => '揽件',
            2 => '疑难',
            3 => '签收',
            4 => '退签',
            5 => '派件',
            6 => '退回',
        ];
        return $state_name_map[$key];
    }
}