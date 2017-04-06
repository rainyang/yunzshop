<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/13
 * Time: 下午2:04
 */

namespace app\backend\modules\order\services;
use app\backend\modules\order\services\models\ExcelModel;

class ExportService
{
    public function export($orders)
    {
        foreach ($orders as &$order) {
            $order = $this->getOrder($order);
        }
        unset($order);
        $excel = new ExcelModel();
        $excel->export($orders, [
            'title'     => '订单-' . date("Y-m-d-H-i", time()),
            'columns'   => $this->getColumns()
        ]);
    }

    protected function getOrder($order){
        $address = json_decode($order['address']['address']);
        $order['pay_sn'] = $order['has_one_order_pay']['pay_sn'];
        $order['nickname'] = $order['belongs_to_member']['nickname'];
        $order['realname'] = $order['belongs_to_member']['realname'];
        $order['mobile'] = $order['belongs_to_member']['mobile'];
        $order['address'] = $address->province . $address->city . $address->area . $address->address;

        $order += $this->getGoods($order);

        $order += $this->getStatus($order);

        $order['pay_type'] = $order['has_one_pay_type']['name'];

        $order['remark'] = $order['has_one_order_remark']['remark'];
        $order['express_company_name'] = $order['has_one_order_express']['express_company_name'];
        $order['express_sn'] = $order['has_one_order_express']['express_sn'];
        return $order;
    }

    protected function getStatus($order)
    {
        if ($order['status'] == 0) {
            $order['status'] = '待付款';
        } else if ($order['status'] == 1) {
            $order['status'] = '已支付';
        } else if ($order['status'] == 2) {
            $order['status'] = '待收货';
        } else if ($order['status'] == 3) {
            $order['status'] = '已完成';
        } else if ($order['status'] == -1) {
            $order['status'] = '已关闭';
        }
        return $order;
    }

    protected function getGoods($order)
    {
        foreach ($order['has_many_order_goods'] as $key => $goods) {
            if ($key == 0) {
                $order['goods_title'] = $goods['title'];
                $order['goods_sn'] = $goods['goods_sn'];
                $order['total'] = $goods['total'];
            }
        }
        return $order;
    }

    protected function getColumns()
    {
        return [
            [
                "title" => "订单编号",
                "field" => "order_sn",
                "width" => 24
            ] ,
            [
                "title" => "支付单号",
                "field" => "pay_sn",
                "width" => 24
            ] ,
            [
                "title" => "粉丝昵称",
                "field" => "nickname",
                "width" => 12
            ] ,
            [
                "title" => "会员姓名",
                "field" => "realname",
                "width" => 12
            ] ,
            [
                "title" => "联系电话",
                "field" => "mobile",
                "width" => 12
            ] ,
            [
                "title" => "收货地址",
                "field" => "address",
                "width" => 30
            ] ,
            [
                "title" => "商品名称",
                "field" => "goods_title",
                "width" => 24
            ] ,
            [
                "title" => "商品编码",
                "field" => "goods_sn",
                "width" => 12
            ] ,
            [
                "title" => "商品数量",
                "field" => "total",
                "width" => 12
            ] ,
            [
                "title" => "支付方式",
                "field" => "pay_type",
                "width" => 12
            ] ,
            [
                "title" => "商品小计",
                "field" => "goods_price",
                "width" => 12
            ] ,
            [
                "title" => "运费",
                "field" => "dispatch_price",
                "width" => 12
            ] ,
            [
                "title" => "应收款",
                "field" => "price",
                "width" => 12
            ] ,
            [
                "title" => "状态",
                "field" => "status",
                "width" => 12
            ] ,
            [
                "title" => "下单时间",
                "field" => "create_time",
                "width" => 24
            ] ,
            [
                "title" => "付款时间",
                "field" => "pay_time",
                "width" => 24
            ] ,
            [
                "title" => "发货时间",
                "field" => "send_time",
                "width" => 24
            ] ,
            [
                "title" => "完成时间",
                "field" => "finish_time",
                "width" => 24
            ] ,
            [
                "title" => "快递公司",
                "field" => "express_company_name",
                "width" => 24
            ] ,
            [
                "title" => "快递单号",
                "field" => "express_sn",
                "width" => 24
            ] ,
            [
                "title" => "订单备注",
                "field" => "remark",
                "width" => 36
            ]
        ];
    }
}