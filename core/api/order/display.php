<?php

//$api->validate('username','password');
$_YZ->ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
//判断该帐号的权限
if(p('supplier')){
    $perm_role = p('supplier')->verifyUserIsSupplier($_W['uid']);
}
//END
$pindex = max(1, intval($_GPC["page"]));
$psize = 20;
$status = $_GPC["status"];
$sendtype = !isset($_GPC["sendtype"]) ? 0 : $_GPC["sendtype"];
$condition = " o.uniacid = :uniacid and o.deleted=0";
$paras = $paras1 = array(
    ":uniacid" => $_W["uniacid"]
);
if ($_GPC["paytype"] != '') {
    if ($_GPC["paytype"] == "2") {
        $condition.= " AND ( o.paytype =21 or o.paytype=22 or o.paytype=23 )";
    } else {
        $condition.= " AND o.paytype =" . intval($_GPC["paytype"]);
    }
}
$statuscondition = '';
if ($status != '') {
    if ($status == - 1) {
        ca("order.view.status_1");
    } else {
        ca("order.view.status" . intval($status));
    }
    if ($status == "-1") {
        $statuscondition = " AND o.status=-1 and o.refundtime=0";
    } else if ($status == "4") {
        $statuscondition = " AND o.refundstate>=0 AND o.refundid<>0";
    } else if ($status == "5") {
        $statuscondition = " AND o.refundtime<>0";
    } else if ($status == "1") {
        $statuscondition = " AND ( o.status = 1 or (o.status=0 and o.paytype=3) )";
    } else if ($status == '0') {
        $statuscondition = " AND o.status = 0 and o.paytype<>3";
    } else {
        $statuscondition = " AND o.status = " . intval($status);
    }
}
//是否为供应商 等于1的是
if(p('supplier')){
    $cond = "";
    if($perm_role == 1){
        $cond .= " and o.supplier_uid={$_W['uid']} ";
        $supplierapply = pdo_fetchall('select u.uid,p.realname,p.mobile,p.banknumber,p.accountname,p.accountbank,a.applysn,a.apply_money,a.apply_time,a.type,a.finish_time,a.status from ' . tablename('sz_yi_supplier_apply') . ' a ' . ' left join' . tablename('sz_yi_perm_user') . ' p on p.uid=a.uid ' . 'left join' . tablename('users') . ' u on a.uid=u.uid where u.uid=' . $_W['uid']);
        $totals['status9'] = count($supplierapply);
        $costmoney = 0;
        $sp_goods = pdo_fetchall("select og.* from " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_order') . " o on (o.id=og.orderid) where og.uniacid={$_W['uniacid']} and og.supplier_uid={$_W['uid']} and o.status=3 and og.supplier_apply_status=0");
        foreach ($sp_goods as $key => $value) {
            if ($value['goods_op_cost_price'] > 0) {
                $costmoney += $value['goods_op_cost_price'] * $value['total'];
            } else {
                $option = pdo_fetch("select * from " . tablename('sz_yi_goods_option') . " where uniacid={$_W['uniacid']} and goodsid={$value['goodsid']} and id={$value['optionid']}");
                if ($option['costprice'] > 0) {
                    $costmoney += $option['costprice'] * $value['total'];
                } else {
                    $goods_info = pdo_fetch("select * from" . tablename('sz_yi_goods') . " where uniacid={$_W['uniacid']} and id={$value['goodsid']}");
                    $costmoney += $goods_info['costprice'] * $value['total'];
                }
            }
        }
        $openid = pdo_fetchcolumn('select openid from ' . tablename('sz_yi_perm_user') . ' where uid=:uid and uniacid=:uniacid',array(':uid' => $_W['uid'],':uniacid'=> $_W['uniacid']));
        if(empty($openid)){
            message("暂未绑定微信，请联系管理员", '', "error");
        }
        //全部提现
        $applytype = intval($_GPC['applytype']);
        if(!empty($applytype)){
            $mygoodsid = pdo_fetchall('select id from ' . tablename('sz_yi_order_goods') . 'where supplier_uid=:supplier_uid and supplier_apply_status = 0',array(
                ':supplier_uid' => $_W['uid']
            ));
            if(empty($mygoodsid)){
                message("没有可提现的订单金额");
            }
            $applysn = m('common')->createNO('commission_apply', 'applyno', 'CA');
            $data = array(
                'uid' => $_W['uid'],
                'apply_money' => $costmoney,
                'apply_time' => time(),
                'status' => 0,
                'type' => $applytype,
                'applysn' => $applysn,
                'uniacid' => $_W['uniacid']
            );

            pdo_insert('sz_yi_supplier_apply',$data);
            @file_put_contents(IA_ROOT . "/addons/sz_yi/data/apply.log", print_r($data, 1), FILE_APPEND);
            if( pdo_insertid() ) {
                foreach ($mygoodsid as $ids) {
                    $arr = array(
                        'supplier_apply_status' => 1
                    );
                    pdo_update('sz_yi_order_goods', $arr, array(
                        'id' => $ids['id']
                    ));
                }
                $tmp_sp_goods = $sp_goods;
                $tmp_sp_goods['applyno'] = $applysn;
                @file_put_contents(IA_ROOT . "/addons/sz_yi/data/sp_goods.log", print_r($tmp_sp_goods, 1), FILE_APPEND);
            }
            message("提现申请已提交，请耐心等待!", $this->createWebUrl('order/list'), "success");
        }
    }
}
$sql = 'select o.* , a.realname as arealname,a.mobile as amobile,a.province as aprovince ,
a.city as acity , a.area as aarea,a.address as aaddress, d.dispatchname,m.nickname,m.id as mid,
m.realname as mrealname,m.mobile as mmobile,sm.id as salerid,sm.nickname as salernickname,s.salername,r.rtype,r.status as rstatus 
from ' . tablename("sz_yi_order") . " o" . " left join " . tablename("sz_yi_order_refund") . " r on r.id =o.refundid " . " 
left join " . tablename("sz_yi_member") . " m on m.openid=o.openid and m.uniacid =  o.uniacid " . " 
left join " . tablename("sz_yi_member_address") . " a on a.id=o.addressid " . " 
left join " . tablename("sz_yi_dispatch") . " d on d.id = o.dispatchid " . " 
left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " 
left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . "  
where {$condition} {$statuscondition} {$cond} ORDER BY o.createtime DESC,o.status DESC  ";
if (empty($_GPC["export"])) {
    $sql.= "LIMIT " . ($pindex - 1) * $psize . "," . $psize;
}
$list = pdo_fetchall($sql, $paras);
if (p('supplier')) {
    foreach ($list as &$value) {
        if ($value['supplier_uid'] == 0) {
            $value['vendor'] = '总店';
        } else {
            $sup_username = pdo_fetchcolumn("select username from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and uid={$value['supplier_uid']}");
            $value['vendor'] = '供应商：' . $sup_username;
        }
    }
}

$paytype = array(
    '0' => array(
        "css" => "default",
        "name" => "未支付"
    ) ,
    "1" => array(
        "css" => "danger",
        "name" => "余额支付"
    ) ,
    "11" => array(
        "css" => "default",
        "name" => "后台付款"
    ) ,
    "2" => array(
        "css" => "danger",
        "name" => "在线支付"
    ) ,
    "21" => array(
        "css" => "success",
        "name" => "微信支付"
    ) ,
    "22" => array(
        "css" => "warning",
        "name" => "支付宝支付"
    ) ,
    "23" => array(
        "css" => "warning",
        "name" => "银联支付"
    ) ,
    "3" => array(
        "css" => "primary",
        "name" => "货到付款"
    ) ,
);
$orderstatus = array(
    "-1" => array(
        "css" => "default",
        "name" => "已关闭"
    ) ,
    '0' => array(
        "css" => "danger",
        "name" => "待付款"
    ) ,
    "1" => array(
        "css" => "info",
        "name" => "待发货"
    ) ,
    "2" => array(
        "css" => "warning",
        "name" => "待收货"
    ) ,
    "3" => array(
        "css" => "success",
        "name" => "已完成"
    )
);
foreach ($list as & $value) {
    $s = $value["status"];
    $pt = $value["paytype"];
    $value["statusvalue"] = $s;
    $value["statuscss"] = $orderstatus[$value["status"]]["css"];
    $value["status"] = $orderstatus[$value["status"]]["name"];
    if ($pt == 3 && empty($value["statusvalue"])) {
        $value["statuscss"] = $orderstatus[1]["css"];
        $value["status"] = $orderstatus[1]["name"];
    }
    if ($s == 1) {
        if ($value["isverify"] == 1) {
            $value["status"] = "待使用";
        } else if (empty($value["addressid"])) {
            $value["status"] = "待取货";
        }
    }
    if ($s == - 1) {
        $value['status'] = $value['rstatus'];
        if (!empty($value["refundtime"])) {
            if ($value['rstatus'] == 1) {
                $value['status'] = '已' . $r_type[$value['rtype']];
            }
        }
    }
    $value["paytypevalue"] = $pt;
    $value["css"] = $paytype[$pt]["css"];
    $value["paytype"] = $paytype[$pt]["name"];
    $value["dispatchname"] = empty($value["addressid"]) ? "自提" : $value["dispatchname"];
    if (empty($value["dispatchname"])) {
        $value["dispatchname"] = "快递";
    }
    if ($value["isverify"] == 1) {
        $value["dispatchname"] = "线下核销";
    } else if ($value["isvirtual"] == 1) {
        $value["dispatchname"] = "虚拟物品";
    } else if (!empty($value["virtual"])) {
        $value["dispatchname"] = "虚拟物品(卡密)<br/>自动发货";
    } else if ($value['cashier']==1) {
        $value["dispatchname"] = "收银台支付";
    }
    if(p('cashier') && $value['cashier'] == 1){
        $value['name'] = set_medias(pdo_fetch('select cs.name,cs.thumb from ' .tablename('sz_yi_cashier_store'). 'cs '.'left join ' .tablename('sz_yi_cashier_order'). ' co on cs.id = co.cashier_store_id where co.order_id=:orderid and co.uniacid=:uniacid', array(':orderid' => $value['id'],':uniacid'=>$_W['uniacid'])), 'thumb');
    }

    if ($value["dispatchtype"] == 1 || !empty($value["isverify"]) || !empty($value["virtual"]) || !empty($value["isvirtual"])|| $value['cashier'] == 1) {
        $value["address"] = '';
        $carrier = iunserializer($value["carrier"]);
        if (is_array($carrier)) {
            $value["addressdata"]["realname"] = $value["realname"] = $carrier["carrier_realname"];
            $value["addressdata"]["mobile"] = $value["mobile"] = $carrier["carrier_mobile"];
        }
    } else {
        $address = iunserializer($value["address"]);
        $isarray = is_array($address);
        $value["realname"] = $isarray ? $address["realname"] : $value["arealname"];
        $value["mobile"] = $isarray ? $address["mobile"] : $value["amobile"];
        $value["province"] = $isarray ? $address["province"] : $value["aprovince"];
        $value["city"] = $isarray ? $address["city"] : $value["acity"];
        $value["area"] = $isarray ? $address["area"] : $value["aarea"];
        $value["address"] = $isarray ? $address["address"] : $value["aaddress"];
        $value["address_province"] = $value["province"];
        $value["address_city"] = $value["city"];
        $value["address_area"] = $value["area"];
        $value["address_address"] = $value["address"];
        $value["address"] = $value["province"] . " " . $value["city"] . " " . $value["area"] . " " . $value["address"];
        $value["addressdata"] = array(
            "realname" => $value["realname"],
            "mobile" => $value["mobile"],
            "address" => $value["address"],
        );
    }
    $commission1 = - 1;
    $commission2 = - 1;
    $commission3 = - 1;
    $m1 = false;
    $m2 = false;
    $m3 = false;
    if (!empty($level) && empty($agentid)) {
        if (!empty($value["agentid"])) {
            $m1 = m("member")->getMember($value["agentid"]);
            $commission1 = 0;
            if (!empty($m1["agentid"])) {
                $m2 = m("member")->getMember($m1["agentid"]);
                $commission2 = 0;
                if (!empty($m2["agentid"])) {
                    $m3 = m("member")->getMember($m2["agentid"]);
                    $commission3 = 0;
                }
            }
        }
    }
    $order_goods = pdo_fetchall("select g.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice,og.changeprice,og.oldprice,og.commission1,og.commission2,og.commission3,og.commissions,og.diyformdata,og.diyformfields from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(
        ":uniacid" => $_W["uniacid"],
        ":orderid" => $value["id"]
    ));
    $goods = '';
    foreach ($order_goods as & $og) {
        if (!empty($level) && empty($agentid)) {
            $commissions = iunserializer($og["commissions"]);
            if (!empty($m1)) {
                if (is_array($commissions)) {
                    $commission1+= isset($commissions["level1"]) ? floatval($commissions["level1"]) : 0;
                } else {
                    $c1 = iunserializer($og["commission1"]);
                    $l1 = $p->getLevel($m1["openid"]);
                    $commission1+= isset($c1["level" . $l1["id"]]) ? $c1["level" . $l1["id"]] : $c1["default"];
                }
            }
            if (!empty($m2)) {
                if (is_array($commissions)) {
                    $commission2+= isset($commissions["level2"]) ? floatval($commissions["level2"]) : 0;
                } else {
                    $c2 = iunserializer($og["commission2"]);
                    $l2 = $p->getLevel($m2["openid"]);
                    $commission2+= isset($c2["level" . $l2["id"]]) ? $c2["level" . $l2["id"]] : $c2["default"];
                }
            }
            if (!empty($m3)) {
                if (is_array($commissions)) {
                    $commission3+= isset($commissions["level3"]) ? floatval($commissions["level3"]) : 0;
                } else {
                    $c3 = iunserializer($og["commission3"]);
                    $l3 = $p->getLevel($m3["openid"]);
                    $commission3+= isset($c3["level" . $l3["id"]]) ? $c3["level" . $l3["id"]] : $c3["default"];
                }
            }
        }
        $goods.= "" . $og["title"] . "";
        if (!empty($og["optiontitle"])) {
            $goods.= " 规格: " . $og["optiontitle"];
        }
        if (!empty($og["option_goodssn"])) {
            $og["goodssn"] = $og["option_goodssn"];
        }
        if (!empty($og["option_productsn"])) {
            $og["productsn"] = $og["option_productsn"];
        }
        if (!empty($og["goodssn"])) {
            $goods.= " 商品编号: " . $og["goodssn"];
        }
        if (!empty($og["productsn"])) {
            $goods.= " 商品条码: " . $og["productsn"];
        }
        $goods.= " 单价: " . ($og["price"] / $og["total"]) . " 折扣后: " . ($og["realprice"] / $og["total"]) . " 数量: " . $og["total"] . " 总价: " . $og["price"] . " 折扣后: " . $og["realprice"] . "";
        if ($plugin_diyform && !empty($og["diyformfields"]) && !empty($og["diyformdata"])) {
            $diyformdata_array = $plugin_diyform->getDatas(iunserializer($og["diyformfields"]) , iunserializer($og["diyformdata"]));
            $diyformdata = "";
            foreach ($diyformdata_array as $da) {
                $diyformdata.= $da["name"] . ": " . $da["value"] . "";
            }
            $og["goods_diyformdata"] = $diyformdata;
        }
    }
    unset($og);
    if (!empty($level) && empty($agentid)) {
        $value["commission1"] = $commission1;
        $value["commission2"] = $commission2;
        $value["commission3"] = $commission3;
    }
    $value["goods"] = set_medias($order_goods, "thumb");
    $value["goods_str"] = $goods;
    if (!empty($agentid) && $level > 0) {
        $commission_level = 0;
        if ($value["agentid"] == $agentid) {
            $value["level"] = 1;
            $level1_commissions = pdo_fetchall("select commission1,commissions  from " . tablename("sz_yi_order_goods") . " og " . " left join  " . tablename("sz_yi_order") . " o on o.id = og.orderid " . " where og.orderid=:orderid and o.agentid= " . $agentid . "  and o.uniacid=:uniacid", array(
                ":orderid" => $value["id"],
                ":uniacid" => $_W["uniacid"]
            ));
            foreach ($level1_commissions as $c) {
                $commission = iunserializer($c["commission1"]);
                $commissions = iunserializer($c["commissions"]);
                if (empty($commissions)) {
                    $commission_level+= isset($commission["level" . $agentLevel["id"]]) ? $commission["level" . $agentLevel["id"]] : $commission["default"];
                } else {
                    $commission_level+= isset($commissions["level1"]) ? floatval($commissions["level1"]) : 0;
                }
            }
        } else if (in_array($value["agentid"], array_keys($agent["level1_agentids"]))) {
            $value["level"] = 2;
            if ($agent["level2"] > 0) {
                $level2_commissions = pdo_fetchall("select commission2,commissions  from " . tablename("sz_yi_order_goods") . " og " . " left join  " . tablename("sz_yi_order") . " o on o.id = og.orderid " . " where og.orderid=:orderid and  o.agentid in ( " . implode(",", array_keys($agent["level1_agentids"])) . ")  and o.uniacid=:uniacid", array(
                    ":orderid" => $value["id"],
                    ":uniacid" => $_W["uniacid"]
                ));
                foreach ($level2_commissions as $c) {
                    $commission = iunserializer($c["commission2"]);
                    $commissions = iunserializer($c["commissions"]);
                    if (empty($commissions)) {
                        $commission_level+= isset($commission["level" . $agentLevel["id"]]) ? $commission["level" . $agentLevel["id"]] : $commission["default"];
                    } else {
                        $commission_level+= isset($commissions["level2"]) ? floatval($commissions["level2"]) : 0;
                    }
                }
            }
        } else if (in_array($value["agentid"], array_keys($agent["level2_agentids"]))) {
            $value["level"] = 3;
            if ($agent["level3"] > 0) {
                $level3_commissions = pdo_fetchall("select commission3,commissions from " . tablename("sz_yi_order_goods") . " og " . " left join  " . tablename("sz_yi_order") . " o on o.id = og.orderid " . " where og.orderid=:orderid and  o.agentid in ( " . implode(",", array_keys($agent["level2_agentids"])) . ")  and o.uniacid=:uniacid", array(
                    ":orderid" => $value["id"],
                    ":uniacid" => $_W["uniacid"]
                ));
                foreach ($level3_commissions as $c) {
                    $commission = iunserializer($c["commission3"]);
                    $commissions = iunserializer($c["commissions"]);
                    if (empty($commissions)) {
                        $commission_level+= isset($commission["level" . $agentLevel["id"]]) ? $commission["level" . $agentLevel["id"]] : $commission["default"];
                    } else {
                        $commission_level+= isset($commissions["level3"]) ? floatval($commissions["level3"]) : 0;
                    }
                }
            }
        }
        $value["commission"] = $commission_level;
    }
}
unset($value);

$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition $statuscondition " . $cond , $paras);
$totalmoney = pdo_fetchcolumn('SELECT ifnull(sum(o.price),0) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition $statuscondition $cond ", $paras);
$totals['all'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE o.uniacid = :uniacid and o.deleted=0 $cond ", $paras);
$totals['status_1'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=-1 and o.refundtime=0 $cond ", $paras);
$totals['status0'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=0 and o.paytype<>3 $cond ", $paras);
$totals['status1'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and ( o.status=1 or ( o.status=0 and o.paytype=3) ) $cond ", $paras);
$totals['status2'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=2 $cond ", $paras);
$totals['status3'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=3 $cond ", $paras);
$totals['status4'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.refundid<>0 and o.refundstate>=0 $cond ", $paras);
$totals['status5'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id  order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.refundtime<>0 $cond ", $paras);


$stores = pdo_fetchall("select id,storename from " . tablename("sz_yi_store") . " where uniacid=:uniacid ", array(
    ":uniacid" => $_W["uniacid"]
));
if(p('cashier')){
    $cashier_stores = pdo_fetchall("select id,name from " . tablename("sz_yi_cashier_store") . " where uniacid=:uniacid ", array(
        ":uniacid" => $_W["uniacid"]
    ));
}
dump($list);
$_YZ->returnSuccess($list);