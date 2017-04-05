<?php
/**
 * 管理后台APP API订单接口
 *
 * PHP version 5.6.15
 *
 * @package   订单模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace admin\api\controller\order;
class Detail extends \admin\api\YZ
{
    //private $order_info;
    public function __construct()
    {
        parent::__construct();
        $this->ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
        //$api->validate('username','password');
    }

    public function index()
    {
        $para = $this->getPara();
        $order_info = $this->getOrderInfo($para);
        $member = $this->getMember($order_info['openid'], $para["uniacid"]);
        $dispatch = $this->getDispatch($order_info);
        $address = $this->getAddressInfo($order_info, $para["uniacid"]);
        $refund = $this->getRefundInfo($order_info["order_id"], $para["uniacid"]);
        $order_info = $this->formatOrderInfo($order_info);
        $res = array(
            'order_info' => $order_info,
            'member' => $member,
            'dispatch' => $dispatch,
            'address' => $address,
            'refund' => $refund
        );
        dump($res);
        $this->returnSuccess($res);
    }

    private function getOrderInfo($para)
    {
        $order_model = new \admin\api\model\order();
        $fields = 'ordersn_general as ordersn,status,price,id as order_id,openid,addressid,dispatchid,createtime,paytime,dispatchprice,deductenough,paytype,changeprice,changedispatchprice,goodsprice,olddispatchprice,address,isverify,isvirtual,virtual,dispatchtype,redstatus';
        $order_info = $order_model->getInfo(array(
            'id' => $para["order_id"],
            'uniacid' => $para["uniacid"]
        ), $fields);
        //$this->order_info = $order_info;
        $order_price = $order_model->getPriceInfo($order_info);
        $order_info["price"] = $order_price;

        $order_info['goods'] = $order_model->getOrderGoods($para["order_id"], $para["uniacid"]);
        unset($order_info['button_info']);
        $order_info['buttons'] = $this->_getButton($order_info["paytype"], $order_info["status"], $order_info['addressid'], $order_info['isverify'], $order_info['redstatus']);

        dump($order_info);
        return $order_info;
    }

    private function _getButton($pay_type, $order_status, $address_id, $is_verify, $red_status = 0)
    {
        $button_mapping = array(
            '' => '',
            '确认付款' => 1,// order/ChangeStatus/confirmPay
            '确认发货' => 2,// order/ChangeStatus/confirmSend
            '确认核销' => 3,// order/ChangeStatus/confirmFetch
            '确认取货' => 4,// order/ChangeStatus/confirmFetch
            '确认收货' => 5,// order/ChangeStatus/order/ChangeStatus/finish
            '取消发货' => 6,// order/ChangeStatus/order/ChangeStatus/cancelSend
            '补发红包' => 7,// order/ChangeStatus/sendRedPack
            '查看物流' => 8,
            '关闭订单' => 9,
        );
        $button_name_array = array();
        $button_array = array();
        if (empty($order_status)) {
            if (cv('order.op.pay')) {
                if ($pay_type == 3) {
                    $button_name_array[] = '确认发货';
                } else {
                    $button_name_array[] = '确认付款';
                }
            }
            if (cv('order.op.close')) {
                $button_name_array[] = '关闭订单';
            }
        } elseif ($order_status == 1) {
            if (!empty($address_id)) {
                if (cv('order.op.send')) {
                    $button_name_array[] = '确认发货';
                }
            } else {
                if ($is_verify) {
                    if (cv('order.op.verify')) {
                        $button_name_array[] = '确认核销';
                    }
                } else {
                    if (cv('order.op.fetch')) {
                        $button_name_array[] = '确认取货';
                    }
                }
            }

        } elseif ($order_status == 2) {
            if (cv('order.op.finish')) {
                $button_name_array[] = '确认收货';
            }
            if (!empty($address_id)) {
                if (cv('order.op.sendcancel')) {
                    $button_name_array[] = '取消发货';
                }
            }
        } elseif ($order_status == 3) {

        }
        foreach ($button_name_array as $button_name) {
            $value = $button_mapping[$button_name];
            $name = $button_name;
            $button_array[] = array(
                'name' => $name,
                'value' => $value
            );
        }
        return $button_array;
    }

    private function getRefundInfo($order_id, $uniacid)
    {
        $order_model = new \admin\api\model\order();
        $refund = $order_model->getRefundInfo($order_id, $uniacid);
        $res = array_part('refundtype,applyprice,reason,content,status,refund_name', $refund);
        //type,类型 applyprice 金额  原因 reason 说明 content 
        return $res ? $res : (object)array();
    }

    private function formatOrderInfo($order_info)
    {
        $order_info['createtime'] = date("Y-m-d H:i:s", $order_info['createtime']);
        $order_info['paytime'] = ($order_info['paytime'] > 0) ? date("Y-m-d H:i:s", $order_info['paytime']) : '';

        $price = $order_info['price'];
        $goods = $order_info['goods'];
        $base = array_part('ordersn,order_id,createtime,isverify,isvirtual', $order_info);
        $status = array(
            'name' => $order_info['status_name'],
            'value' => $order_info['status'],
        );
        $pay = array(
            'name' => $order_info['pay_type_name'],
            'value' => $order_info['paytype'],
        );
        $buttons = $order_info['buttons'];
        $res_order_info = array(
            'price'=>$price,
            'goods'=>$goods,
            'base'=>$base,
            'status'=>$status,
            'pay'=>$pay,
            'refundstate'=>$refundstate,
            'buttons'=>$buttons
        );
        return $res_order_info;
    }

    private function getMember($openid, $uniacid)
    {
        $member_model = new \admin\api\model\user();
        $member = $member_model->getInfo(
            array(
                'openid' => $openid,
                'uniacid' => $uniacid
            ),
            'id as member_id,realname,weixin,mobile,nickname,avatar'
        );
        return $member;
    }

    private function getDispatch($order_info)
    {
        //dump($order_info);
        if (empty($order_info['addressid'])) {
            if ($order_info['isverify'] == 1) {
                $dispatch = '线下核销';
            } elseif ($order_info['isvirtual'] == 1) {
                $dispatch = '虚拟物品';
            } elseif ($order_info['virtual'] == 1) {
                $dispatch = '虚拟物品(卡密)自动发货';
            } elseif ($order_info['dispatchtype'] == 1) {
                $dispatch = '自提';
            }
        } else {
            if (empty($dispatchtype)) {
                $dispatch = '快递';
            }
        }
        return $dispatch;
    }

    private function getAddressInfo($order_info)
    {
        $uniacid = $this->uniacid;
        $address_info = unserialize($order_info['address']);
        if (!is_array($address_info)) {
            $address_info = pdo_fetchcolumn("SELECT * FROM " . tablename("sz_yi_member_address") . " WHERE id = :id and uniacid=:uniacid", array(
                ":id" => $order_info['addressid'],
                ":uniacid" => $uniacid
            ));
        }
        $address = array_part('province,city,area,address', $address_info);
        $address_info = array(
            "addressid" => $address_info["id"],
            "realname" => $address_info["realname"],
            "mobile" => $address_info["mobile"],
            "address" => $address ? $address : ''
        );


        return $address_info;
    }

}

//new orderDetail();
