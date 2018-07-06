<?php
/**
 * 订单详情
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/4
 * Time: 上午11:16
 */

namespace app\backend\modules\order\controllers;

use app\backend\modules\member\models\Member;
use app\backend\modules\order\models\Order;
use app\common\components\BaseController;
use app\common\services\DivFromService;

class DetailController extends BaseController
{
    public function index(\Request $request)
    {
        Debugbar::info($request);
        $orderId = $request->query('id');
        $order = Order::getOrderDetailById($orderId);
        if (!empty($order->express)) {
            $express = $order->express->getExpress($order->express->express_code, $order->express->express_sn);
//            dd($express);
//            exit;
            $dispatch['express_sn'] = $order->express->express_sn;
            $dispatch['company_name'] = $order->express->express_company_name;
            $dispatch['data'] = $express['data'];
            $dispatch['thumb'] = $order->hasManyOrderGoods[0]->thumb;
            $dispatch['tel'] = '95533';
            $dispatch['status_name'] = $express['status_name'];
        }

        return view('order.detail', [
            'order' => $order ? $order->toArray() : [],
            'dispatch' => $dispatch,
            'div_from' => $this->getDivFrom($order),
            'var' => \YunShop::app()->get(),
            'ops' => 'order.ops',
            'edit_goods' => 'goods.goods.edit'
        ])->render();
    }

    private function getDivFrom($order)
    {
        if (!$order || !$order->hasManyOrderGoods) {
            return ['status' => false];
        }
        $goods_ids = [];
        foreach ($order->hasManyOrderGoods as $key => $goods) {
            $goods_ids[] = $goods['goods_id'];
        }

        $memberInfo = Member::select('realname', 'idcard')->where('uid', $order->uid)->first();

        $result['status'] = DivFromService::isDisplay($goods_ids);
        $result['member_name'] = $memberInfo->realname;
        $result['member_card'] = $memberInfo->idcard;

        return $result;
    }
}