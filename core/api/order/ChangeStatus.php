<?php
/**
 * 管理后台APP API订单状态改变
 *
 * PHP version 5.6.15
 *
 * @package   订单模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace controller\api\order;
class ChangeStatus extends \api\YZ
{
    private $order_info;
    public function __construct()
    {
        parent::__construct();
        $para = $this->getPara();

        $order_model = new \model\api\order();

        $this->order_info = $order_model->getInfo(array(
            'id' => $para["order_id"],
            'uniacid' => $para["uniacid"]
        ));
    }
    public function close(){
        global $_W, $_GPC;
        $this->ca("order.op.close");
        $order = $this->order_info;
        if ($order["status"] == - 1) {
            $this->returnError("订单已关闭，无需重复关闭！");
        } else if ($order["status"] >= 1) {
            $this->returnError("订单已付款，不能关闭！");
        }
        if (!empty($order["transid"])) {
            changeWechatSend($order["ordersn"], 0, $_GPC["reson"]);
        }
        $time = time();
        if ($order['refundstate'] > 0 && !empty($order['refundid'])) {
            $data               = array();
            $data['status']     = -1;
            $data['refundtime'] = $time;
            pdo_update('sz_yi_order_refund', $data, array(
                'id' => $order['refundid'],
                'uniacid' => $_W['uniacid']
            ));
        }
        pdo_update("sz_yi_order", array(
            "status" => - 1,
            'refundstate' => 0,
            "canceltime" => time() ,
            "remark" => $order["remark"] . "" . $_GPC["remark"]
        ) , array(
            "id" => $order["id"],
            "uniacid" => $_W["uniacid"]
        ));
        if ($order["deductcredit"] > 0) {
            $shopset = m("common")->getSysset("shop");
            m("member")->setCredit($order["openid"], "credit1", $order["deductcredit"], array(
                '0',
                $shopset["name"] . "购物返还抵扣积分 积分: {$order["deductcredit"]} 抵扣金额: {$order["deductprice"]} 订单号: {$order["ordersn"]}"
            ));
        }
        if (p("coupon") && !empty($order["couponid"])) {
            p("coupon")->returnConsumeCoupon($order["id"]);
        }
        plog("order.op.close", "订单关闭 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
        $this->returnSuccess("订单关闭操作成功！");
    }
    public function cancelSend(){
        global $_W, $_GPC;
        $this->ca("order.op.sendcancel");
        $order = $this->order_info;

        if ($order["status"] != 2) {
            $this->returnError("订单未发货，不需取消发货！");
        }
        if (!empty($order["transid"])) {
            changeWechatSend($order["ordersn"], 0, $_GPC["cancelreson"]);
        }
        pdo_update("sz_yi_order", array(
            "status" => 1,
            "sendtime" => 0
        ) , array(
            "id" => $order["id"],
            "uniacid" => $_W["uniacid"]
        ));
        plog("order.op.sencancel", "订单取消发货 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
        $this->returnSuccess("取消发货操作成功！");
    }
    public function finish(){
        global $_W;
        $this->ca("order.op.finish");
        $order = $this->order_info;

        pdo_update("sz_yi_order", array(
            "status" => 3,
            "finishtime" => time()
        ) , array(
            "id" => $order["id"],
            "uniacid" => $_W["uniacid"]
        ));
        m("member")->upgradeLevel($order["openid"]);
        m("notice")->sendOrderMessage($order["id"]);
        if (p("coupon") && !empty($order["couponid"])) {
            p("coupon")->backConsumeCoupon($order["id"]);
        }

        if (p("commission")) {
            p("commission")->checkOrderFinish($order["id"]);
        }

        if (p("return")) {
            p("return")->cumulative_order_amount($order["id"]);
        }

        // 订单确认收货后自动发送红包
        if ($order["redprice"] > 0) {
            m('finance')->sendredpack($order['openid'], $order["redprice"]*100, $order["id"], $desc = '购买商品赠送红包', $act_name = '购买商品赠送红包', $remark = '购买商品确认收货发送红包');
        }

        plog("order.op.finish", "订单完成 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
        $this->returnSuccess("订单操作成功！");
    }
    public function getArea(){
        $xml_string = require __API_ROOT__.'/area.php';
        //dump($xml_string);exit;
        $xml = simplexml_load_string($xml_string);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        dump($array);
    }
    public function getShippingInfo(){
        $order = $this->order_info;
        dump($order);exit;

    }
    public function getExpressInfo(){
        //$para = $this->getPara();
        $order = $this->order_info;
//dump($order);
        $address = unserialize($order['address']);
        $address = array(
            "addressid" => $address["id"],
            "realname" => $address["realname"],
            "mobile" => $address["mobile"],
            "address" => array_part('province,city,area,address', $address)
        );
        $company_list = json_decode(require __API_ROOT__.'/expresscom.php',true);
        //exit;
        $res=compact('company_list','address');
        dump($res);
        $this->returnSuccess($res);
    }
    public function confirmSend(){
        $this->ca("order.op.send");
        global $_W;
        $para = $this->getPara();

        $order = $this->order_info;
        if (empty($order["addressid"])) {
            $this->returnError("无收货地址，无法发货！");
        }
        if ($order["paytype"] != 3) {
            if ($order["status"] != 1) {
                $this->returnError("订单未付款，无法发货！");
            }
        }
        if (!empty($para["isexpress"]) && empty($para["expresssn"])) {
            $this->returnError("请输入快递单号！");
        }
        if (!empty($order["transid"])) {
            changeWechatSend($order["ordersn"], 1);
        }
        pdo_update("sz_yi_order", array(
            "status" => 2,
            "express" => trim($para["express"]) ,
            "expresscom" => trim($para["expresscom"]) ,
            "expresssn" => trim($para["expresssn"]) ,
            "sendtime" => time()
        ) , array(
            "id" => $order["id"],
            "uniacid" => $_W["uniacid"]
        ));
        if (!empty($order["refundid"])) {
            $zym_var_35 = pdo_fetch("select * from " . tablename("sz_yi_order_refund") . " where id=:id limit 1", array(
                ":id" => $order["refundid"]
            ));
            if (!empty($zym_var_35)) {
                pdo_update("sz_yi_order_refund", array(
                    "status" => - 1
                ) , array(
                    "id" => $order["refundid"]
                ));
                pdo_update("sz_yi_order", array(
                    "refundid" => 0
                ) , array(
                    "id" => $order["id"]
                ));
            }
        }
        m("notice")->sendOrderMessage($order["id"]);
        plog("order.op.send", "订单发货 ID: {$order["id"]} 订单号: {$order["ordersn"]} <br/>快递公司: {$para["expresscom"]} 快递单号: {$para["expresssn"]}");
        $this->returnSuccess("发货操作成功！");
    }
    public function confirmPay()
    {
        $this->ca("order.op.pay");
        global $_W;
        $order = $this->order_info;
        if ($order["status"] > 1) {
            $this->returnError("订单已付款，不需重复付款！");
        }
        $virtual = p("virtual");
        if (!empty($order["virtual"]) && $virtual) {
            $virtual->pay($order);
        } else {
            /*pdo_update("sz_yi_order", array(
            "status" => 1,
            "paytype" => 11,
            "paytime" => time()
            ) , array(
            "id" => $order["id"],
            "uniacid" => $_W["uniacid"]
            ));
            m("order")->setStocksAndCredits($order["id"], 1);
            m("notice")->sendOrderMessage($order["id"]);
            if (p("coupon") && !empty($order["couponid"])) {
            p("coupon")->backConsumeCoupon($order["id"]);
            }
            if (p("commission")) {
            p("commission")->checkOrderPay($order["id"]);
            }*/
            $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(
                ':uniacid' => $_W['uniacid'],
                ':module' => 'sz_yi',
                ':tid' => $order['ordersn']
            ));
            pdo_update("sz_yi_order", array('paytype' => '11'), array('uniacid' => $_W['uniacid'], 'id' => $order['id']));
            $ret = array();
            $ret['result'] = 'success';
            $ret['from'] = 'return';
            $ret['tid'] = $log['tid'];
            $ret['user'] = $order['openid'];
            $ret['fee'] = $order['price'];
            $ret['weid'] = $_W['uniacid'];
            $ret['uniacid'] = $_W['uniacid'];
            $payresult = m('order')->payResult($ret);
        }
        plog("order.op.pay", "订单确认付款 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
        $this->returnSuccess("确认订单付款操作成功！");
    }
    function changeWechatSend($zym_var_2, $zym_var_4, $zym_var_1 = '') {
        global $_W;
        $zym_var_3 = pdo_fetch("SELECT plid, openid, tag FROM " . tablename("core_paylog") . " WHERE tid = '{$zym_var_2}' AND status = 1 AND type = 'wechat'");
        if (!empty($zym_var_3["openid"])) {
            $zym_var_3["tag"] = iunserializer($zym_var_3["tag"]);
            $zym_var_8 = $zym_var_3["tag"]["acid"];
            load()->model("account");
            $zym_var_17 = account_fetch($zym_var_8);
            $zym_var_6 = uni_setting($zym_var_17["uniacid"], "payment");
            if ($zym_var_6["payment"]["wechat"]["version"] == "2") {
                return true;
            }
            $zym_var_7 = array(
                "appid" => $zym_var_17["key"],
                "openid" => $zym_var_3["openid"],
                "transid" => $zym_var_3["tag"]["transaction_id"],
                "out_trade_no" => $zym_var_3["plid"],
                "deliver_timestamp" => TIMESTAMP,
                "deliver_status" => $zym_var_4,
                "deliver_msg" => $zym_var_1,
            );
            $zym_var_14 = $zym_var_7;
            $zym_var_14["appkey"] = $zym_var_6["payment"]["wechat"]["signkey"];
            ksort($zym_var_14);
            $zym_var_19 = '';
            foreach ($zym_var_14 as $zym_var_33 => $zym_var_31) {
                $zym_var_33 = strtolower($zym_var_33);
                $zym_var_19.= "{$zym_var_33}={$zym_var_31}&";
            }
            $zym_var_7["app_signature"] = sha1(rtrim($zym_var_19, "&"));
            $zym_var_7["sign_method"] = "sha1";
            $zym_var_17 = WeAccount::create($zym_var_8);
            $zym_var_29 = $zym_var_17->changeOrderStatus($zym_var_7);
            if (is_error($zym_var_29)) {
                $this->returnError($zym_var_29["message"]);
            }
        }
    }
}

