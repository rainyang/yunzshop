<?php
namespace app\api\controller\order;
@session_start();

class Order
{
    public static function getButtonModel($button_id)
    {
        $button = [
            1 => [
                'name' => '付款',
                'api' => 'pay',
                'value' => 1
            ],
            5 => [
                'name' => '确认收货',
                'api' => 'complete',
                'value' => 5
            ],
            9 => [
                'name' => '取消订单',
                'api' => 'pay',
                'value' => 9
            ],
            10=>[
                'name'=>'评价',
                'api'=>'pay',
                'value'=>10
            ],
            11=>[
                'name'=>'追加评价',
                'api'=>'pay',
                'value'=>11
            ],
            12=>[
                'name'=>'删除订单',
                'api'=>'pay',
                'value'=>12
            ],
            1=>[
                'name'=>'付款',
                'api'=>'pay',
                'value'=>1
            ],
            14=>[
                'name'=>'确认使用',
                'api'=>'pay',
                'value'=>14
            ],
        ];
        if (!Arr::has($button_id)) {
            echo 'button_id不存在';
            exit;
        }
        return $button[$button_id];
    }

    public static function getButtonApi($button_id)
    {
        $file_name = 'order/op/';
        $button_model = static::getButtonModel($button_id);
        $api = $file_name . $button_model['api'];
        return $api;
    }

    public static function getButtonName($button_id)
    {
        $button_model = static::getButtonModel($button_id);
        $name = $button_model['name'];
        return $name;

    }

    public static function getButtonList($order)
    {
        if ($order['status'] == 0) {
            if ($order['paytype'] != 3) {
                $button_id_arr[] = 1;//付款
            }
            
            $button_id_arr[] = 9;//取消订单

        }
        if ($order['status'] == 2) {
            $button_list[] = [
                'name' => '确认收货',
                'value' => 5
            ];

            if ($order['expresssn'] != '') {
                $button_list[] = [
                    'name' => '查看物流',
                    'value' => 8
                ];
            }
        }
        if ($order['status'] == 3 && $order['iscomment'] == 0) {
            $button_list[] = [
                'name' => '评价',
                'value' => 10
            ];
        }
        if ($order['status'] == 3 && $order['iscomment'] == 1) {
            $button_list[] = [
                'name' => '追加评价',
                'value' => 11
            ];
        }
        if ($order['status'] == 3 || $order['status'] == -1) {
            $button_list[] = [
                'name' => '删除订单',
                'value' => 12
            ];
        }
        if ($order['canrefund']) {
            $button_list[] = [
                'name' => $order['refund_button'],
                'value' => 13
            ];
        }
        if ($order['isverify'] == '1' && $order['verified'] != '1' && $order['status'] == '1') {
            $button_list[] = [
                'name' => '确认使用',
                'value' => 14
            ];
        }
        $button_list = [
            'name' => '付款',
            'value' => 1
        ];
        foreach ($button_id_arr as $button_id) {
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

