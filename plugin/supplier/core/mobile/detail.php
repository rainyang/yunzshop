<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation      = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$uniacid        = $_W['uniacid'];
$orderid        = intval($_GPC['orderid']);
$order          = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid limit 1', array(
    ':id' => $orderid,
    ':uniacid' => $uniacid
));
if ($operation == 'deal') {
    $id = intval($_GPC["id"]);
    $item = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
        ":id" => $id,
        ":uniacid" => $_W["uniacid"]
    ));
    $shopset = m("common")->getSysset("shop");
    if (empty($item)) {
        message("抱歉，订单不存在!", referer() , "error");
    }
    $to = trim($_GPC["to"]);
    if ($to == 'confirmsend') {
        return order_list_confirmsend($item);
    }
}
if (!empty($order)) {
    $order['virtual_str'] = str_replace("\n", "<br/>", $order['virtual_str']);
    $diyformfields        = "";
    if ($diyform_plugin) {
        $diyformfields = ",og.diyformfields,og.diyformdata";
    }
    $goods        = pdo_fetchall("select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.storeids{$diyformfields}  from " . tablename('sz_yi_order_goods') . " og " . " left join " . tablename('sz_yi_goods') . " g on g.id=og.goodsid " . " where og.orderid=:orderid and og.uniacid=:uniacid ", array(
        ':uniacid' => $uniacid,
        ':orderid' => $orderid
    ));
    $show         = 1;
    $diyform_flag = 0;
    foreach ($goods as &$g) {
        $g['thumb'] = tomedia($g['thumb']);
        if ($diyform_plugin) {
            $diyformdata   = iunserializer($g['diyformdata']);
            $fields        = iunserializer($g['diyformfields']);
            $diyformfields = array();
            foreach ($fields as $key => $value) {
                $tp_value = "";
                $tp_css   = "";
                if ($value['data_type'] == 1 || $value['data_type'] == 3) {
                    $tp_css .= " dline1";
                }
                if ($value['data_type'] == 5) {
                    $tp_css .= " dline2";
                }
                if ($value['data_type'] == 0 || $value['data_type'] == 1 || $value['data_type'] == 2 || $value['data_type'] == 6 || $value['data_type'] == 7) {
                    $tp_value = str_replace("\n", "<br/>", $diyformdata[$key]);
                } else if ($value['data_type'] == 3 || $value['data_type'] == 8) {
                    if (is_array($diyformdata[$key])) {
                        foreach ($diyformdata[$key] as $k1 => $v1) {
                            $tp_value .= $v1 . " ";
                        }
                    }
                } else if ($value['data_type'] == 5) {
                    if (is_array($diyformdata[$key])) {
                        foreach ($diyformdata[$key] as $k1 => $v1) {
                            $tp_value .= "<img style='height:25px;padding:1px;border:1px solid #ccc'  src='" . tomedia($v1) . "'/>";
                        }
                    }
                } else if ($value['data_type'] == 9) {
                    $tp_value = ($diyformdata[$key]['province'] != '请选择省份' ? $diyformdata[$key]['province'] : '') . " - " . ($diyformdata[$key]['city'] != '请选择城市' ? $diyformdata[$key]['city'] : '');
                }
                $diyformfields[] = array(
                    'tp_name' => $value['tp_name'],
                    "tp_value" => $tp_value,
                    'tp_css' => $tp_css
                );
            }
            $g['diyformfields'] = $diyformfields;
            $g['diyformdata']   = $diyformdata;
            if (!empty($g['diyformdata'])) {
                $diyform_flag = 1;
            }
        } else {
            $g['diyformfields'] = array();
            $g['diyformdata']   = array();
        }
        unset($g);
    }
}
if ($_W['isajax']) {
    if (empty($order)) {
        return show_json(0,'未找到订单!');
    }
    $order['virtual_str']     = str_replace("\n", "<br/>", $order['virtual_str']);
    $order['goodstotal']      = count($goods);
    $order['finishtimevalue'] = $order['finishtime'];
    $order['finishtime']      = date('Y-m-d H:i:s', $order['finishtime']);
    $order['createtime']      = date('Y-m-d H:i:s', $order['createtime']);
    $order['paytime']      = date('Y-m-d H:i:s', $order['paytime']);
    $order['address'] = iunserializer($order['address']);
    $order['address'] = json_encode($order['address']);
    $address                  = false;
    $carrier                  = false;
    $stores                   = array();
    if ($order['isverify'] == 1) {
        $storeids = array();
        foreach ($goods as $g) {
            if (!empty($g['storeids'])) {
                $storeids = array_merge(explode(',', $g['storeids']), $storeids);
            }
        }
        if (empty($storeids)) {
            $stores = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where  uniacid=:uniacid and status=1', array(
                ':uniacid' => $_W['uniacid']
            ));
        } else {
            $stores = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1', array(
                ':uniacid' => $_W['uniacid']
            ));
        }
    } else {
        if ($order['dispatchtype'] == 0) {
            $address = iunserializer($order['address']);
            if (!is_array($address)) {
                $address = pdo_fetch('select realname,mobile,address from ' . tablename('sz_yi_member_address') . ' where id=:id limit 1', array(
                    ':id' => $order['addressid']
                ));
            }
        }
    }
    if ($order['dispatchtype'] == 1 || $order['isverify'] == 1 || !empty($order['virtual'])) {
        $carrier = unserialize($order['carrier']);
    }
    $set       = set_medias(m('common')->getSysset('shop'), 'logo');
    $canrefund = false;
    if ($order['status'] == 1) {
        $canrefund = true;
    } else if ($order['status'] == 3) {
        if ($order['isverify'] != 1 && empty($order['virtual'])) {
            $tradeset   = m('common')->getSysset('trade');
            $refunddays = intval($tradeset['refunddays']);
            if ($refunddays > 0) {
                $days = intval((time() - $order['finishtimevalue']) / 3600 / 24);
                if ($days <= $refunddays) {
                    $canrefund = true;
                }
            }
        }
    }
    $order['canrefund'] = $canrefund;

	
	if("" == trim($order['address'], "\"") && isset($order['openid'])){
		$order['address'] = pdo_fetch('select realname,mobile,address from ' . tablename('sz_yi_member_address') . ' where uniacid=:uniacid and openid=:openid and deleted = 0', array(
                    ':uniacid' => $_W['uniacid'],
					':openid' => $order['openid']
                ));
		$order['address'] = iunserializer($order['address']);
		$order['address'] = json_encode($order['address']);
	}

    return show_json(1, array(

        'order' => $order,
        'goods' => $goods,
        'address' => $address,
        'carrier' => $carrier,
        'stores' => $stores,
        'isverify' => $isverify,
        'set' => $set,
        'diyform_flag' => $diyform_flag,
        'show'         => $show
    ));
}
function order_list_confirmsend($order) {
    global $_W, $_GPC;
    if (empty($order["addressid"]) && $order["isvirtual"]!=1) {
        message("无收货地址，无法发货！");
    }
    if ($order["paytype"] != 3) {
        if ($order["status"] != 1) {
            message("订单未付款，无法发货！");
        }
    }
    if (!empty($_GPC["isexpress"]) && empty($_GPC["expresssn"])) {
        message("请输入快递单号！");
    }
    if (!empty($order["transid"])) {
        changeWechatSend($order["ordersn"], 1);
    }
    pdo_update("sz_yi_order", array(
        "status" => 2,
        "express" => trim($_GPC["express"]) ,
        "expresscom" => trim($_GPC["expresscom"]) ,
        "expresssn" => trim($_GPC["expresssn"]) ,
        "sendtime" => time()
    ) , array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));
    if (!empty($order["refundid"])) {
        $refund = pdo_fetch("select * from " . tablename("sz_yi_order_refund") . " where id=:id limit 1", array(
            ":id" => $order["refundid"]
        ));
        if (!empty($refund)) {
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
    if (is_app_api()) {
        return show_json(1, array('confirmsend' => 1));
    }
    m("notice")->sendOrderMessage($order["id"]);
    plog("order.op.send", "订单发货 ID: {$order["id"]} 订单号: {$order["ordersn"]} <br/>快递公司: {$_GPC["expresscom"]} 快递单号: {$_GPC["expresssn"]}");
    message("发货操作成功！", referer() , "success");
}
include $this->template('detail');

