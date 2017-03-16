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
    public static function export($order)
    {
        $columns = [
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
                "title" => "商品规格",
                "field" => "option_title",
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
        $columns = [
            [
                "title" => "订单编号",
                "field" => "order_sn",
                "width" => 24
            ]
        ];
        $excel = new ExcelModel();
        $excel->export($order, [
            'title'     => '订单-' . date("Y-m-d-H-i", time()),
            'columns'   => $columns
        ]);
    }
}