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
namespace controller\api\order;
class Detail extends \api\YZ
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
        $res = compact('order_info', 'member', 'dispatch', 'address','refund');
        dump($res);
        $this->returnSuccess($res);
    }

    private function getOrderInfo($para)
    {
        $order_model = new \model\api\order();
        $fields = 'ordersn,status,price,id as order_id,openid,addressid,dispatchid,createtime,paytime,dispatchprice,deductenough,paytype,changeprice,changedispatchprice,goodsprice,olddispatchprice,address';
        $order_info = $order_model->getInfo(array(
            'id' => $para["order_id"],
            'uniacid' => $para["uniacid"]
        ), $fields);
        //$this->order_info = $order_info;
        $order_price = $order_model->getPriceInfo($order_info);
        $order_info["price"] = $order_price;

        $order_info['goods'] = $order_model->getOrderGoods($para["order_id"], $para["uniacid"]);
        dump($order_info);
        return $order_info;
    }
    private function getRefundInfo($order_id,$uniacid){
        $order_model = new \model\api\order();
        $refund = $order_model->getRefundInfo($order_id, $uniacid);
        $res = array_part('refundtype,applyprice,reason,content,status,refund_name',$refund);
        //type,类型 applyprice 金额  原因 reason 说明 content 
        return $res ? $res : (object) array();
    }
    private function formatOrderInfo($order_info)
    {
        $order_info['createtime'] = date("Y-m-d H:i:s", $order_info['createtime']);
        $order_info['paytime'] = ($order_info['paytime'] > 0) ? date("Y-m-d H:i:s", $order_info['paytime']) : '';

        $price = $order_info['price'];
        $goods = $order_info['goods'];
        $base = array_part('ordersn,order_id,createtime', $order_info);
        $status = array(
            'name' => $order_info['status_name'],
            'value' => $order_info['status'],
        );
        $pay = array(
            'name' => $order_info['pay_type_name'],
            'value' => $order_info['paytype'],
        );
        $res_order_info = compact('price', 'goods', 'base', 'status', 'pay','refundstate');
        return $res_order_info;
    }

    private function getMember($openid, $uniacid)
    {
        $member_model = new \model\api\user();
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
        /*if (empty($order_info["addressid"])) {
            $user = unserialize($order_info["carrier"]);
        } else {
            $user = iunserializer($order_info["address"]);
            if (!is_array($user)) {
                $user = pdo_fetch("SELECT * FROM " . tablename("sz_yi_member_address") . " WHERE id = :id and uniacid=:uniacid", array(
                    ":id" => $order_info['addressid'],
                    ":uniacid" => $uniacid
                ));
            }
            //$address_info = $user["address"];
            $user["address"] = array_part('province,city,area,address', $user);
            //dump($user["address"]);
            $order_info["addressdata"] = array(
                "addressid" => $order_info["addressid"],
                "realname" => $user["realname"],
                "mobile" => $user["mobile"],
                "address" => $user["address"],
            );
        }*/
        $address_info = unserialize($order_info['address']);
        if(empty($address_info)){
            return (object)array();
        }
        $address = array_part('province,city,area,address', $address_info);
        $address_info = array(
            "addressid" => $address_info["id"],
            "realname" => $address_info["realname"],
            "mobile" => $address_info["mobile"],
            "address" => $address
        );
        return $address_info;
    }

}

//new orderDetail();
