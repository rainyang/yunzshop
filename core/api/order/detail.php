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
namespace controller\api;
class orderDetail extends \api\YZ
{
    public function __construct()
    {
        parent::__construct();
        $this->ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
        //$api->validate('username','password');
        $this->run();
    }

    private function run()
    {
        $para = $this->getPara();
        $order_info = $this->getOrderInfo($para);
        $member = $this->getMember($order_info['openid'], $para["uniacid"]);
        $dispatch = $this->getDispatch($order_info["dispatchid"], $para["uniacid"]);
        $address = $this->getAddressInfo($order_info, $para["uniacid"]);

        $order_info = $this->formatOrderInfo($order_info);
        $res = compact('order_info', 'member', 'dispatch', 'address');
        dump($res);
        $this->returnSuccess($res);
    }

    private function getOrderInfo($para)
    {
        $order_model = new \model\api\order();
        $fields = 'ordersn,status,price,id as order_id,openid,addressid,dispatchid,createtime,paytime,dispatchprice,deductenough,paytype';
        $order_info = $order_model->getInfo(array(
            'id' => $para["order_id"],
            'uniacid' => $para["uniacid"]
        ), $fields);
        $order_info['price'] = array(
            'price' => $order_info['price'] - $order_info['dispatchprice'],
            'dispatchprice' => $order_info['dispatchprice'],
            'deductenough' => $order_info['deductenough'],
            'total_price' => $order_info['price'] - $order_info['dispatchprice'] - $order_info['deductenough']
        );
        $order_info['goods'] = $order_model->getOrderGoods($para["order_id"], $para["uniacid"]);
        return $order_info;
    }

    private function formatOrderInfo($order_info)
    {
        $order_info['createtime'] = date("Y-m-d H:i:s", $order_info['createtime']);
        $order_info['paytime'] = ($order_info['paytime'] > 0) ? date("Y-m-d H:i:s", $order_info['paytime']) : '';

        $res_order_info = array_part('price,goods', $order_info);
        $res_order_info['base'] = array_part('ordersn,status,status_name,order_id,createtime', $order_info);
        $res_order_info['pay'] = array(
            'name' => $order_info['pay_type_name'],
            'value' => $order_info['paytype'],
        );
        return $res_order_info;
    }

    private function getMember($openid, $uniacid)
    {
        $member_model = new \model\api\member();
        $member = $member_model->getInfo(array(
            'openid' => $openid,
            'uniacid' => $uniacid
        ),
            'id as member_id,realname,weixin,mobile,nickname,avatar');
        return $member;
    }

    private function getDispatch($dispatchid, $uniacid)
    {
        $dispatch = pdo_fetch("SELECT * FROM " . tablename("sz_yi_dispatch") . " WHERE id = :id and uniacid=:uniacid", array(
            ":id" => $dispatchid,
            ":uniacid" => $uniacid
        ));
        return $dispatch;
    }

    private function getAddressInfo($order_info, $uniacid)
    {
        if (empty($order_info["addressid"])) {
            $user = unserialize($order_info["carrier"]);
        } else {
            $user = iunserializer($order_info["address"]);
            if (!is_array($user)) {
                $user = pdo_fetch("SELECT * FROM " . tablename("sz_yi_member_address") . " WHERE id = :id and uniacid=:uniacid", array(
                    ":id" => $order_info['addressid'],
                    ":uniacid" => $uniacid
                ));
            }
            $address_info = $user["address"];
            $user["address"] = $user["province"] . " " . $user["city"] . " " . $user["area"] . " " . $user["address"];
            $order_info["addressdata"] = array(
                "addressid" => $order_info["addressid"],
                "realname" => $user["realname"],
                "mobile" => $user["mobile"],
                "address" => $user["address"],
            );
        }
        //dump($user);
        return $order_info["addressdata"];
    }

}

new orderDetail();
