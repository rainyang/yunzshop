<?php
namespace app\api\controller\order;
//use util;

use util\Str;

@session_start();

class Order
{
    const OP_ROUTE = 'order/op/';
    const PAY = 1;
    const COMPLETE = 5;
    const EXPRESS =8;
    const CANCEL = 9;
    const COMMENT = 10;
    const ADD_COMMENT = 11;
    const DELETE = 12;
    const REFUND = 13;
    const VERIFY = 14;
    const AFTER_SALES = 15;
    const IN_REFUND = 16;
    const IN_AFTER_SALE = 17;




    public static function getButtonModel($button_id)
    {
        $button = [
            static::PAY => [
                'name' => '付款',
                'api' => '/order/pay',//
                'value' => static::PAY
            ],
            static::COMPLETE => [
                'name' => '确认收货',
                'api' => 'complete',
                'value' => static::COMPLETE
            ],
            static::EXPRESS => [
                'name' => '查看物流',
                'api' => '/order/express/display',
                'value' => static::EXPRESS
            ],
            static::CANCEL => [
                'name' => '取消订单',
                'api' => 'cancel',
                'value' => static::CANCEL
            ],
            static::COMMENT=>[
                'name'=>'评价',
                'api'=>'comment',
                'value'=>static::COMMENT
            ],
            static::ADD_COMMENT=>[
                'name'=>'追加评价',
                'api'=>'comment',
                'value'=>static::ADD_COMMENT
            ],
            static::DELETE=>[
                'name'=>'删除订单',
                'api'=>'delete',
                'value'=>static::DELETE
            ],
            static::REFUND=>[
                'name'=>'申请退款',
                'api'=>'refund',
                'value'=>static::REFUND
            ],
            static::AFTER_SALES=>[
                'name'=>'refund',
                'api'=>'申请售后',
                'value'=>static::AFTER_SALES
            ],
            static::IN_REFUND=>[//todo 有问题这个状态不是一个按钮
                'name'=>'申请退款中',
                'api'=>'/',
                'value'=>static::IN_REFUND
            ],
            static::IN_AFTER_SALE=>[//todo 有问题这个状态不是一个按钮
                'name'=>'申请退款中',
                'api'=>'/',
                'value'=>static::IN_AFTER_SALE
            ],
            static::VERIFY=>[
                'name'=>'确认使用',
                'api'=>'/',//verify
                'value'=>static::VERIFY
            ],
        ];
        if (!isset($button[$button_id])) {
            echo 'button_id不存在';
            exit;
        }
        return $button[$button_id];
    }

    public static function getButtonApi($button_id)
    {
        $file_name = static::OP_ROUTE;
        $button_model = static::getButtonModel($button_id);
        if(empty($button_model['api'])){
            echo '正在写';exit;
        }
        if(Str::startsWith($button_model['api'],'/')){
            return ltrim($button_model['api'],'/');
        }
        $api = $file_name . $button_model['api'];
        return $api;
    }

    public static function getButtonName($button_id)
    {
        $button_model = static::getButtonModel($button_id);
        $name = $button_model['name'];
        if(empty($name)){
            return '正在写';
        }
        return $name;

    }

    public static function getButtonList($order)
    {
        if ($order['status'] == 0) {
            if ($order['paytype'] != 3) {
                $button_id_arr[] = static::PAY;//付款
            }

            $button_id_arr[] = static::CANCEL;//取消订单

        }
        if ($order['status'] == 2) {
            $button_id_arr[] = static::COMPLETE;//收货

            if ($order['expresssn'] != '') {
                $button_id_arr[] = static::EXPRESS;//物流信息
            }
        }
        if ($order['status'] == 3 && $order['iscomment'] == 0) {
            $button_id_arr[] = static::COMMENT;//评价
            
        }
        if ($order['status'] == 3 && $order['iscomment'] == 1) {
            $button_id_arr[] = static::ADD_COMMENT;//追加评价

        }
        if ($order['status'] == 3 || $order['status'] == -1) {
            $button_id_arr[] = static::DELETE;//删除订单
        }
        if ($order['canrefund']) {
            if ($order['status'] == 1) {
                if(!empty($order['refundstate'])){
                    $button_id_arr[] = static::IN_REFUND;//删除订单
                }else{
                    $button_id_arr[] = static::REFUND;//删除订单
                }
            } else {
                if(!empty($order['refundstate'])){
                    $button_id_arr[] = static::IN_AFTER_SALE;//删除订单
                }else{
                    $button_id_arr[] = static::REFUND;//删除订单
                }
            }
        }
        if ($order['isverify'] == '1' && $order['verified'] != '1' && $order['status'] == '1') {
            $button_id_arr[] = static::VERIFY;//使用

        }
//dump($button_id_arr);exit;
        foreach ($button_id_arr as $button_id) {
            //dump($button_id);exit;
            $button_list[] = static::getButtonModel($button_id);
        }
        return $button_list;
    }

    public static function getStatusStr($order)
    {
        if ($order['status'] == 0 && $order['paytype'] != 3) {
            $status_str = '等待付款';
        }
        if ($order['paytype'] == 3 && $order['status'] != 0) {
            $status_str = '货到付款，等待发货';
        }
        if ($order['status'] == 1) {
            $status_str = '买家已付款';
        }
        if ($order['status'] == 2) {
            $status_str = '卖家已发货';
        }
        if ($order['status'] == 3) {
            $status_str = '交易完成';
        }
        if ($order['status'] == -1) {
            $status_str = '交易关闭';
        }
        return $status_str;
    }
}

