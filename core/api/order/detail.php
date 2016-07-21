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
//$api->validate('username','password');
$_YZ->ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
$id = intval($_GPC["id"]);
$p = p("commission");
$order_info = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
    ":id" => $id,
    ":uniacid" => $_W["uniacid"]
));
$order_info["statusvalue"] = $order_info["status"];
$order_info = $_YZ->m('order')->getInfo(
    array(
        'id'=>intval($_GPC["id"]),
    )
);
$shopset = m("common")->getSysset("shop");
if (empty($order_info)) {
    message("抱歉，订单不存在!", referer() , "error");
}
if (!empty($order_info["refundid"])) {
    ca("order.view.status4");
} else {
    if ($order_info["status"] == - 1) {
        ca("order.view.status_1");
    } else {
        ca("order.view.status" . $order_info["status"]);
    }
}
if ($_W["ispost"]) {
    pdo_update("sz_yi_order", array(
        "remark" => trim($_GPC["remark"]) ,
    ) , array(
        "id" => $order_info["id"],
        "uniacid" => $_W["uniacid"]
    ));
    plog("order.op.saveremark", "订单保存备注  ID: {$order_info["id"]} 订单号: {$order_info["ordersn"]}");
    message("订单备注保存成功！", $this->createWebUrl("order", array(
        "op" => "detail",
        "id" => $order_info["id"]
    )) , "success");
}
$member = m("member")->getMember($order_info["openid"]);
$dispatch = pdo_fetch("SELECT * FROM " . tablename("sz_yi_dispatch") . " WHERE id = :id and uniacid=:uniacid", array(
    ":id" => $order_info["dispatchid"],
    ":uniacid" => $_W["uniacid"]
));
if (empty($order_info["addressid"])) {
    $user = unserialize($order_info["carrier"]);
} else {
    $user = iunserializer($order_info["address"]);
    if (!is_array($user)) {
        $user = pdo_fetch("SELECT * FROM " . tablename("sz_yi_member_address") . " WHERE id = :id and uniacid=:uniacid", array(
            ":id" => $order_info["addressid"],
            ":uniacid" => $_W["uniacid"]
        ));
    }
    $address_info = $user["address"];
    $user["address"] = $user["province"] . " " . $user["city"] . " " . $user["area"] . " " . $user["address"];
    $order_info["addressdata"] = array(
        "realname" => $user["realname"],
        "mobile" => $user["mobile"],
        "address" => $user["address"],
    );
}
$refund = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order_refund") . " WHERE orderid = :orderid and uniacid=:uniacid order by id desc", array(
    ":orderid" => $order_info["id"],
    ":uniacid" => $_W["uniacid"]
));
if (!empty($refund)) {
    if (!empty($refund['imgs'])) {
        $refund['imgs'] = iunserializer($refund['imgs']);
    }
}
$diyformfields = "";
$plugin_diyform = p("diyform");
if ($plugin_diyform) {
    $diyformfields = ",diyformfields,diyformdata";
}

$goods = pdo_fetchall("SELECT g.*, o.goodssn as option_goodssn, o.productsn as option_productsn,o.total,g.type,o.optionname,o.optionid,o.price as orderprice,o.realprice,o.changeprice,o.oldprice,o.commission1,o.commission2,o.commission3,o.commissions{$diyformfields} FROM " . tablename("sz_yi_order_goods") . " o left join " . tablename("sz_yi_goods") . " g on o.goodsid=g.id " . " WHERE o.orderid=:orderid and o.uniacid=:uniacid", array(
    ":orderid" => $id,
    ":uniacid" => $_W["uniacid"]
));
if(p('cashier') && $order_info['cashier'] == 1){
    $cashier_stores = set_medias(pdo_fetch("select * from " .tablename('sz_yi_cashier_store'). " where id = ".$order_info['cashierid']." and uniacid=".$_W['uniacid']),'thumb');
}
foreach ($goods as & $r) {
    if (!empty($r["option_goodssn"])) {
        $r["goodssn"] = $r["option_goodssn"];
    }
    if (!empty($r["option_productsn"])) {
        $r["productsn"] = $r["option_productsn"];
    }
    if ($plugin_diyform) {
        $r["diyformfields"] = iunserializer($r["diyformfields"]);
        $r["diyformdata"] = iunserializer($r["diyformdata"]);
    }
}
unset($r);
$order_info["goods"] = $goods;
$agents = array();
if ($p) {
    $agents = $p->getAgents($id);
    $m1 = isset($agents[0]) ? $agents[0] : false;
    $m2 = isset($agents[1]) ? $agents[1] : false;
    $m3 = isset($agents[2]) ? $agents[2] : false;
    $commission1 = 0;
    $commission2 = 0;
    $commission3 = 0;
    foreach ($goods as & $og) {
        $oc1 = 0;
        $oc2 = 0;
        $oc3 = 0;
        $commissions = iunserializer($og["commissions"]);
        if (!empty($m1)) {
            if (is_array($commissions)) {
                $oc1 = isset($commissions["level1"]) ? floatval($commissions["level1"]) : 0;
            } else {
                $c1 = iunserializer($og["commission1"]);
                $l1 = $p->getLevel($m1["openid"]);
                $oc1 = isset($c1["level" . $l1["id"]]) ? $c1["level" . $l1["id"]] : $c1["default"];
            }
            $og["oc1"] = $oc1;
            $commission1+= $oc1;
        }
        if (!empty($m2)) {
            if (is_array($commissions)) {
                $oc2 = isset($commissions["level2"]) ? floatval($commissions["level2"]) : 0;
            } else {
                $c2 = iunserializer($og["commission2"]);
                $l2 = $p->getLevel($m2["openid"]);
                $oc2 = isset($c2["level" . $l2["id"]]) ? $c2["level" . $l2["id"]] : $c2["default"];
            }
            $og["oc2"] = $oc2;
            $commission2+= $oc2;
        }
        if (!empty($m3)) {
            if (is_array($commissions)) {
                $oc3 = isset($commissions["level3"]) ? floatval($commissions["level3"]) : 0;
            } else {
                $c3 = iunserializer($og["commission3"]);
                $l3 = $p->getLevel($m3["openid"]);
                $oc3 = isset($c3["level" . $l3["id"]]) ? $c3["level" . $l3["id"]] : $c3["default"];
            }
            $og["oc3"] = $oc3;
            $commission3+= $oc3;
        }
    }
    unset($og);
}
$condition = " o.uniacid=:uniacid and o.deleted=0";
$paras = array(
    ":uniacid" => $_W["uniacid"]
);
$totals = array();
$totals["all"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition", $paras);
$totals["status_1"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=-1 and o.refundtime=0", $paras);
$totals["status0"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=0 and o.paytype<>3", $paras);
$totals["status1"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and ( o.status=1 or ( o.status=0 and o.paytype=3) )", $paras);
$totals["status2"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=2", $paras);
$totals["status3"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=3", $paras);
$totals["status4"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.refundid<>0  and o.refundstate>=0", $paras);
$totals["status5"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.refundtime<>0", $paras);
$coupon = false;
if (p("coupon") && !empty($order_info["couponid"])) {
    $coupon = p("coupon")->getCouponByDataID($order_info["couponid"]);
}
if (p("verify")) {
    if (!empty($order_info["verifyopenid"])) {
        $saler = m("member")->getMember($order_info["verifyopenid"]);
        $saler["salername"] = pdo_fetchcolumn("select salername from " . tablename("sz_yi_saler") . " where openid=:openid and uniacid=:uniacid limit 1 ", array(
            ":uniacid" => $_W["uniacid"],
            ":openid" => $order_info["verifyopenid"]
        ));
    }
    if (!empty($order_info["verifystoreid"])) {
        $store = pdo_fetch("select * from " . tablename("sz_yi_store") . " where id=:storeid limit 1 ", array(
            ":storeid" => $order_info["verifystoreid"]
        ));
    }
}
$show = 1;
$diyform_flag = 0;
$diyform_plugin = p("diyform");
$order_fields = false;
$order_data = false;
if ($diyform_plugin) {
    $diyform_set = $diyform_plugin->getSet();
    foreach ($goods as $g) {
        if (!empty($g["diyformdata"])) {
            $diyform_flag = 1;
            break;
        }
    }
    if (!empty($order_info["diyformid"])) {
        $orderdiyformid = $order_info["diyformid"];
        if (!empty($orderdiyformid)) {
            $diyform_flag = 1;
            $order_fields = iunserializer($order_info["diyformfields"]);
            $order_data = iunserializer($order_info["diyformdata"]);
        }
    }
}
$refund_address = pdo_fetchall('select * from ' . tablename('sz_yi_refund_address') . ' where uniacid=:uniacid', array(
    ':uniacid' => $_W['uniacid']
));
load()->func("tpl");
include $this->template("web/order/detail");
exit;
