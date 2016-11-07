<?php
namespace app\api\controller\order;
@session_start();
use app\api\YZ;
use app\api\Request;

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
        $address_block_list = $this->getAddressBlockTypeId($this->json['order'], $this->variable['show']);//$address_block_list配送信息
        $button_list = $this->_getButtonList($this->json['order']);//$button_list按钮列表
        $show_diyform = $this->_canShowDiyForm();//$show_diyform显示diyform
        $order_status_str = $this->_getStatusStr($this->json['order']);//$order_status_str订单状态文字
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

    private function _getButtonList($order)
    {
        $button_list = Order::getButtonList($order);
        return $button_list;
    }

    private function _getStatusStr($order)
    {

        $status_str = Order::getStatusStr($order);
        return $status_str;
    }

    private static function getAddressBlockTypeId($order, $show)
    {
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

