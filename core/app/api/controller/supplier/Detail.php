<?php
namespace app\api\controller\supplier;
@session_start();
use app\api\YZ;
use app\api\Request;

class Detail extends YZ
{
    private $json;

    public function __construct()
    {
        parent::__construct();
        global $_GPC;
        $_GPC['id'] = $_GPC['orderid'];
        $result = $this->callPlugin('supplier/detail');
        $this->json = $result['json'];
    }

    public function index()
    {
        if ($this->json['order']['status'] == 0 && $this->json['order']['paytype'] != 3) {
            $order_status = '等待付款';
        } else if ($this->json['order']['paytype'] == 3 && $this->json['order']['status'] == 0) {
            $order_status = '货到付款，等待发货';
        } else if ($this->json['order']['status'] == 1) {
            $order_status = '已付款';
        } else if ($this->json['order']['status'] == 2) {
            $order_status = '已发货';
        } else if ($this->json['order']['status'] == 3) {
            $order_status = '交易完成';
        } else if ($this->json['order']['status'] == -1) {
            $order_status = '交易关闭';
        }
        if ($this->json['order']['addressid'] != 0) {
            $address_address = $this->json['address']['province'] . "/" . $this->json['address']['city'] . "/" . $this->json['address']['area'] . "/" . $this->json['address']['address'];
            $address = array(
                'address_realname'  => $this->json['address']['realname'],
                'address_mobile'    => $this->json['address']['mobile'],
                'address_address'   => $address_address
            );
        } else {
            $address = '';
        }
        if ($this->json['order']['status'] == 3) {
            $status_and_time = array(
                'status'    => "订单号".$this->json['order']['ordersn'],
                'time'      => "交易完成时间".$this->json['order']['finishtime']
            );
        } else if ($this->json['order']['status'] == 1) {
            $status_and_time = array(
                'status'    => "订单号".$this->json['order']['ordersn'],
                'time'      => "支付时间".$this->json['order']['paytime']
            );
        } else if ($this->json['order']['status'] == 0) {
            $status_and_time = array(
                'status'    => "订单号".$this->json['order']['ordersn'],
                'time'      => "下单时间".$this->json['order']['createtime']
            );
        }
        if ($this->json['order']['status'] == 1) {
            $is_show_send = true;
        } else {
            $is_show_send = false;
        }
        $express = array(
            'express' => array(
                0 => "其他快递",
                1 => "顺丰",
                2 => "申通",
                3 => "韵达快运",
                4 => "天天快递",
                5 => "圆通速递",
                6 => "中通速递",
                7 => "ems快递",
                8 => "汇通快运",
                9 => "全峰快递",
                10 => "宅急送",
                11 => "aae全球专递",
                12 => "安捷快递",
                13 => "安信达快递",
                14 => "彪记快递",
                15 => "bht",
                16 => "百福东方国际物流",
                17 => "中国东方（COE）",
                18 => "长宇物流",
                19 => "大田物流",
                20 => "德邦物流",
                21 => "dhl",
                22 => "dpex",
                23 => "d速快递",
                24 => "递四方",
                25 => "fedex（国外）",
                26 => "飞康达物流",
                27 => "凤凰快递",
                28 => "飞快达",
                29 => "国通快递",
                30 => "港中能达物流",
                31 => "广东邮政物流",
                32 => "共速达",
                33 => "恒路物流",
                34 => "华夏龙物流",
                35 => "海红",
                36 => "海外环球",
                37 => "佳怡物流",
                38 => "京广速递",
                39 => "急先达",
                40 => "佳吉物流",
                41 => "加运美物流",
                42 => "金大物流",
                43 => "嘉里大通",
                44 => "晋越快递",
                45 => "快捷速递",
                46 => "联邦快递（国内）",
                47 => "联昊通物流",
                48 => "龙邦物流",
                49 => "立即送",
                50 => "乐捷递",
                51 => "民航快递",
                52 => "美国快递",
                53 => "门对门",
                54 => "OCS",
                55 => "配思货运",
                56 => "全晨快递",
                57 => "全际通物流",
                58 => "全日通快递",
                59 => "全一快递",
                60 => "如风达",
                61 => "三态速递",
                62 => "盛辉物流",
                63 => "速尔物流",
                64 => "盛丰物流",
                65 => "赛澳递",
                66 => "天地华宇",
                67 => "tnt",
                68 => "ups",
                69 => "万家物流",
                70 => "文捷航空速递",
                71 => "伍圆",
                72 => "万象物流",
                73 => "新邦物流",
                74 => "信丰物流",
                75 => "亚风速递",
                76 => "一邦速递",
                77 => "优速物流",
                78 => "邮政包裹挂号信",
                79 => "邮政国际包裹挂号信",
                80 => "远成物流",
                81 => "源伟丰快递",
                82 => "元智捷诚快递",
                83 => "运通快递",
                84 => "越丰物流",
                85 => "源安达",
                86 => "银捷速递",
                87 => "中铁快运",
                88 => "中邮物流",
                89 => "忠信达",
                90 => "芝麻开门"
            )
        );
        $res = array(
            'order_status'          => $order_status,//订单状态
            'order_price'           => $this->json['order']['price'],//订单金额(含运费)
            'order_dispatchprice'   => $this->json['order']['dispatchprice'],//运费
            'address'               => $address,//收件人与收货地址
            'goods'                 => $this->json['goods'],
            'is_show_send'          => $is_show_send
        );
        //$res = array_merge($res, $this->json);
        $res += $this->json;
        if (!empty($status_and_time)) {
            $res += $status_and_time;
        }
        $res += $express;
        return $this->returnSuccess($res);
    }
}

