<?php
namespace app\api\controller\order;
@session_start();
use app\api\YZ;
use app\api\Request;
use yii\helpers\ArrayHelper;

class Detail extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        $result = $this->callMobile('order/detail');
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        //$result = ArrayHelper::;
        $address_block_list = $this->_getAddressBlockTypeId();//$address_block_list配送信息
        $button_list = $this->_getButtonList();//$button_list按钮列表
        $show_diyform = $this->_canShowDiyForm();//$show_diyform显示diyform
        $order_status_str = $this->_getStatusStr();//$order_status_str订单状态文字
        $res = array(
            'address_block_list' => $address_block_list,
            'button_list' => $button_list,
            'show_diyform' => $show_diyform,
            'order_status_str' => $order_status_str,
        );
        //$res = array_merge($res, $this->json);
        $res += $this->json;
        return $this->returnSuccess($res);
    }

    private function _getButtonList()
    {
        $order = $this->json['order'];

        if ($order['status'] == 0) {
            if ($order['paytype'] != 3) {
                $button_list[] = [
                    'name' => '付款',
                    'value' => 1
                ];
            }
            $button_list[] = [
                'name' => '取消订单',
                'value' => 9
            ];
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
        return $button_list;
    }

    private function _getStatusStr()
    {
        $order = $this->json['order'];
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

    private function _getAddressBlockTypeId()
    {
        $order = $this->json['order'];
        $show = $this->variable['show'];
        $id_arr = [];
        if ($show) {
            if ($order['isverify'] == 1 && $order['virtual'] != '0') {
                //地址类型1  联系人 carrier 联系电话 carrier
                $id_arr[] = 1;
            }
        }

        if ($order['isverify'] == 1) {
            if ($order['addressid'] != 0) {
                //地址类型2  收件人  address(mobile)  address
                $id_arr[] = 2;

            }
            if ($order['dispatchtype'] == 1) {
                //地址类型3  自提点  carrier(mobile)  address
                //地址类型4  提货人姓名 carrier 提货人手机
                $id_arr[] = 3;
                $id_arr[] = 4;

            }
        } else {
            if ($order['addressid'] != 0) {
                //地址类型2
                $id_arr[] = 2;

            }
        }
        return $id_arr;
    }

    private function _canShowDiyForm()
    {
        $goods = $this->variable['goods'];
        $diyform_flag = $this->variable['diyform_flag'];
        if ($diyform_flag == 1 && count($goods) == 1) {
            return true;
        }
        return false;
    }

    private function _validatePara()
    {
        $validate_fields = array(
            'uniacid' => array(
                'type' => 'required',
                'describe' => '',
            ), 'address_id' => array(
                'type' => 'required',
                'describe' => '手机号',
                'required' => false
            ),

        );
        Request::filter($validate_fields);
        $validate_messages = Request::validate($validate_fields);
        return $validate_messages;
    }
}

