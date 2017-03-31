<?php
namespace app\api\controller\order;
@session_start();
use app\api\YZ;
use app\api\Request;
use util\Arr;

class Refund extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        //dump($result);exit;

    }

    private function _call()
    {
        $result = $this->callMobile('order/op/refund');
        if ($result['code'] == -1) {
            $this->returnError($result['json']);
        }
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function display()
    {
        global $_GPC;
        $_GPC['orderid'] = $_GPC['id'];
        $this->_call();
        $json = $this->json;
        $result['title'] = sprintf("查看%s申请", array_get($json, 'order.refund_button'));

        $result['block'][] = $this->_getRemindBlock();
        $result['block'][] = $this->_getInfoBlock();

        $result['button'] = $this->_getButtonList();
        $this->returnSuccess($result);
    }

    private function _getRemindBlock()
    {
        $order = $this->json['order'];
        $refund = $this->json['refund'];

        if ($order['status'] == 0) {
            $title = "等待商家处理{$order['refund_button']}申请";
        } elseif ($order['status'] == 3) {
            $title = "商家已经通过{$order['refund_button']}申请";
        }
        if ($refund['status'] == 0 && $refund['rtype'] == 0) {
            $row_list[] = '退款申请流程：';
            $row_list[] = '1、发起退款申请';
            $row_list[] = '2、商家确认后退款到您的账户';
            $row_list[] = '如果商家未处理：请及时与商家联系';
        }
        if ($refund['rtype'] > 0) {
            if ($refund['rtype'] == 1) {
                $row_list[] = '退款退货申请流程：';
                $row_list[] = '1、发起退款退货申请';
                $row_list[] = '2、退货需将退货商品邮寄至商家指定地址';
                $row_list[] = '3、商家后货后确认无误';
                $row_list[] = '4、退款到您的账户';
            } elseif ($refund['rtype'] == 2) {
                $row_list[] = '换货申请流程：';
                $row_list[] = '1、发起换货申请，并把快递单号录入系统';
                $row_list[] = '2、将需要换货的商品邮寄至商家指定地址，并在系统内输入快递单号';
                $row_list[] = '3、商家确认后货后重新发出商品';
                $row_list[] = '4、签收确认商品';
            }
        } elseif ($refund['status'] >= 3) {
            if ($refund['address_info'] != '') {
                $row_list[] = '退货地址: ';
                //red
                $row_list[] = $refund['address_info'];
            }
            if ($refund['message'] != '') {
                $row_list[] = '卖家留言: ';
                //red
                $row_list[] = $refund['message'];
            }
            if ($refund['rtype'] == 1) {
                $row_list[] = '退货状态: ';
                //red
                if ($refund['status'] == 3) {
                    $row_list[] = '请您填写快递单号';
                } elseif ($refund['status'] == 4) {
                    $row_list[] = '等待商家收到您的快递物品并确认';
                } elseif ($refund['status'] == 5) {
                    $row_list[] = '商家已经发货';
                }

                if ($refund['rexpresssn'] != '') {
                    $row_list[] = '退货快递公司:';
                    //red
                    $row_list[] = $refund['rexpresscom'];
                    $row_list[] = $refund['rexpresssn'];
                }
                if ($refund['rtype'] == 2) {
                    $row_list[] = '换货状态:';
                    if ($refund['status'] == 3) {
                        $row_list[] = '请您填写快递单号';

                    } elseif ($refund['status'] == 4) {
                        $row_list[] = '等待商家收到您的快递物品并确认';

                    } elseif ($refund['status'] == 5) {
                        $row_list[] = '商家已经发货';
                    }
                    if ($refund['rexpresssn'] != '') {
                        $row_list[] = '换货快递公司';
                        //red
                        $row_list[] = $refund['rexpresscom'];

                        $row_list[] = '换货快递公司';
                        //red
                        $row_list[] = $refund['rexpresssn'];
                    }

                }

            }
        }
        $res = array(
            'title' => $title,
            'row_list' => $row_list,
        );
        return $res;
    }

    private function _getInfoBlock()
    {
        $refund = $this->json['refund'];
        $rtype_name_arr = array(
            0 => '退款',
            1 => '退货退款',
            2 => '换货',
        );
        $rtype_name = array_get($rtype_name_arr, $refund['rtype']);

        $title = '协商详情';

        $row_list[] = '处理方式：' . $rtype_name;
        $row_list[] = $rtype_name . '原因：' . $refund['reason'];
        $row_list[] = $rtype_name . '说明：' . $refund['content'];
        if ($refund['applyprice'] > 0) {
            $row_list[] = '退款金额：' . $refund['applyprice'];
        }
        $row_list[] = '申请时间：' . $refund['createtime'];

        $res = array(
            'title' => $title,
            'row_list' => $row_list,
        );
        return $res;
    }

    private function _getButtonList()
    {
        $refund = $this->json['refund'];
        $order = $this->json['refund_button'];

        $button_list = array();
        if ($refund['rtype'] == 2 && $refund['status'] == 5) {
            $button_list[] = array(
                'id' => 1,
                'name' => '确认收到换货物品'
            );
            $button_list[] = array(
                'id' => 2,
                'name' => '查看换货物流'
            );
        }
        if ($refund['rtype'] == 3 && $refund['status'] == 4) {
            $button_list[] = array(
                'id' => 3,
                'name' => '填写快递单号'
            );
            //<div class="refund_sub1" id='refund_input'>填写快递单号</div>

        }
        if ($refund['status'] == 0) {
            $button_list[] = array(
                'id' => 3,
                'name' => '修改' . $order['refund_button'] . '申请'
            );
        }
        $button_list[] = array(
            'id' => 4,
            'name' => '取消' . $order['refund_button'] . '申请'
        );
        return $button_list;
    }

    public function confirm()
    {
        global $_W, $_GPC;
        $_GPC['refunddata'] = json_decode($_GPC['refunddata'], true);
        $_W['ispost'] = true;
        $this->_call();
        $this->returnSuccess($this->json);

    }

    public function cancel()
    {
        global $_W;
        $_W['ispost'] = true;

        $this->_call();
        $this->returnSuccess($this->json);
    }

}

