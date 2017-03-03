<?php
global $_W, $_GPC;
$set = $this->getSet();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == "display") {
	message('页面加载错误！', '', 'error');
} elseif ($operation == "refund") {
	$good_id = intval($_GPC['id']);
	if(empty($good_id)){
		message('参数错误！', '', 'error');
	}
	$condition = "";
	$good_condition = (!empty($_GPC["good_name"])) ? "g.title LIKE '%{$_GPC["good_name"]}%'" : "g.id = '{$good_id}'";
    $conditionsp_goods = pdo_fetchall("select og.orderid from " . tablename('sz_yi_order_goods') . " og left join " . tablename('sz_yi_goods') . " g on (g.id=og.goodsid) where og.uniacid={$_W['uniacid']} and {$good_condition} group by og.orderid ");
    $conditionsp_goodsid = '';
    $time = time();
    $uniacid = $_W['uniacid'];
    foreach ($conditionsp_goods as $value) {
        $orderid = $value['orderid'];
        $order   = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid limit 1', array(
	            ':id' => $orderid,
	            ':uniacid' => $_W['uniacid']
	        ));
        if ($order['refundtime'] > 0) {
        	continue;
        }
        $price = $order['price'] + $order['deductcredit2'];
        $refund = array(
            'uniacid' => $_W['uniacid'],
            'applyprice' => $price,
            'rtype' => 0,
            'reason' => "其它",
            'content' => "众筹失败退款"
        );
        $refund['createtime'] = time();
        $refund['orderid']    = $orderid;
        $refund['orderprice'] = $price;
        $refund['refundno']   = m('common')->createNO('order_refund', 'refundno', 'SR');
        pdo_insert('sz_yi_order_refund', $refund);
        $refundid = pdo_insertid();
        pdo_update('sz_yi_order', array(
            'refundid' => $refundid,
            'refundstate' => 1
        ), array(
            'id' => $orderid,
            'uniacid' => $_W['uniacid']
        ));
        $refundtype = 0;
        $realprice = $price;
        $item = $order;
        $ordersn_count = 0;
        if (!empty($item['pay_ordersn'])) {
            $pay_ordersn = $item['pay_ordersn'];
        } else {
            $pay_ordersn = $ordersn;
        }
        $ordersn = $item['ordersn'];

        if (!empty($item['ordersn2'])) {
            $var = sprintf('%02d', $item['ordersn2']);
            $pay_ordersn .= 'GJ' . $var;
        }
        if ($item['paytype'] == 1) {
            m('member')->setCredit($item['openid'], 'credit2', $realprice, array(
                0,
                $shopset['name'] . "退款: {$realprice}元 订单号: " . $item['ordersn']
            ));
            $result = true;
        } else {
            if ($item['paytype'] == 21) {
                $realprice = round($realprice - $item['deductcredit2'], 2);
                $result = m('finance')->refund($item['openid'], $pay_ordersn, $refund['refundno'],
                    $item['price'] * 100, $realprice * 100);
                $refundtype = 2;
            } else {
                if ($item['paytype'] == 22) {
                    $set = m('common')->getSysset(array(
                        'pay'
                    ));
                    $realprice = round($realprice - $item['deductcredit2'], 2);
                    m('finance')->alipayrefund($item['openid'], $item['trade_no'], $refund['refundno'],
                        $realprice);

                } elseif ($item['paytype'] == 26 || $item['paytype'] == 25) {
                    $set = m('common')->getSysset(array('pay'));
                    $setting = uni_setting($_W['uniacid'], array('payment'));
                    $realprice = round($realprice - $item['deductcredit2'], 2);
                    m('finance')->yeepayrefund($item['paytype'], $item['openid'], $item['trade_no'],
                        $refund['refundno'], $realprice);
                } elseif ($item['paytype'] == 27 || $item['paytype'] == 28) {
                    $realprice = round($realprice - $item['deductcredit2'], 2);
                    m('finance')->apprefund($item['paytype'], $item['openid'], $item['trade_no'],
                        $refund['refundno'], $realprice);

                } else {
                    $realprice = round($realprice - $item['deductcredit2'], 2);
                    $result = m('finance')->pay($item['openid'], 1, $realprice * 100,
                        $refund['refundno'],
                        $shopset['name'] . "退款: {$realprice}元 订单号: " . $item['ordersn']);
                    $refundtype = 1;
                }
            }
        }
        $goods = pdo_fetchall('SELECT g.id,g.credit, o.total,o.realprice FROM ' . tablename('sz_yi_order_goods') . ' o left join ' . tablename('sz_yi_goods') . ' g on o.goodsid=g.id ' . ' WHERE o.orderid=:orderid and o.uniacid=:uniacid',
            array(
                ':orderid' => $item['id'],
                ':uniacid' => $_W['uniacid']
            ));
        $credits = 0;
        foreach ($goods as $g) {
            $gcredit = trim($g['credit']);
            if (!empty($gcredit)) {
                if (strexists($gcredit, '%')) {
                    $credits += intval(floatval(str_replace('%', '',
                            $gcredit)) / 100 * $g['realprice']);
                } else {
                    $credits += intval($g['credit']) * $g['total'];
                }
            }
        }

        if ($credits > 0) {
            m('member')->setCredit($item['openid'], 'credit1', -$credits, array(
                0,
                $shopset['name'] . "退款扣除积分: {$credits} 订单号: " . $item['ordersn']
            ));
        }
        if ($item['deductcredit'] > 0) {
            m('member')->setCredit($item['openid'], 'credit1', $item['deductcredit'], array(
                '0',
                $shopset['name'] . "购物返还抵扣积分 积分: {$item['deductcredit']} 抵扣金额: {$item['deductprice']} 订单号: {$item['ordersn']}"
            ));
        }

        if ($item['deductyunbimoney'] > 0) {
            $shopset = m('common')->getSysset('shop');

            p('yunbi')->setVirtualCurrency($item['openid'], $item['deductyunbi']);
            //虚拟币抵扣记录
            $data_log = array(
                'id' => '',
                'openid' => $item['openid'],
                'credittype' => 'virtual_currency',
                'money' => $item['deductyunbi'],
                'remark' => "购物返还抵扣" . $yunbiset['yunbi_title'] . " " . $yunbiset['yunbi_title'] . ": {$item['deductyunbi']} 抵扣金额: {$item['deductyunbimoney']} 订单号: {$item['ordersn']}"
            );
            p('yunbi')->addYunbiLog($_W["uniacid"], $data_log, '4');
        }


        if (!empty($refundtype)) {
            if ($item['deductcredit2'] > 0) {
                m('member')->setCredit($item['openid'], 'credit2', $item['deductcredit2'], array(
                    '0',
                    $shopset['name'] . "购物返还抵扣余额 积分: {$item['deductcredit2']} 订单号: {$item['ordersn']}"
                ));
            }
        }

        $data['reply'] = '';
        $data['status'] = 1;
        $data['refundtype'] = $refundtype;
        $data['price'] = $realprice;
        $data['refundtime'] = $time;
        pdo_update('sz_yi_order_refund', $data, array(
            'id' => $refundid
        ));
        m('notice')->sendOrderMessage($item['id'], true);
        pdo_update('sz_yi_order', array(
            'refundstate' => 0,
            'status' => -1,
            'refundtime' => $time
        ), array(
            'id' => $item['id'],
            'uniacid' => $uniacid
        ));
        foreach ($goods as $g) {
            $salesreal = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1',
                array(
                    ':goodsid' => $g['id'],
                    ':uniacid' => $uniacid
                ));
            pdo_update('sz_yi_goods', array(
                'salesreal' => $salesreal
            ), array(
                'id' => $g['id']
            ));
        }
    }
    pdo_update('sz_yi_fund_goods', array("allrefund" => 1), array('goodsid' => $good_id, 'uniacid' => $uniacid));
	message('全部退款成功!', referer(), 'success');
}
