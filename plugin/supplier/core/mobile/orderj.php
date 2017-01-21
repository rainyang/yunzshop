<?php

global $_W, $_GPC;
$openid = m('user')->getOpenid();
$set = $this->getSet();
$member = m('member')->getMember($openid);
$supplieruser = $this->model->getSupplierUidAndUsername($openid);
$uid = $supplieruser['uid'];
$username = $supplieruser['username'];
$_GPC['type'] = $_GPC['type'] ? $_GPC['type'] : 0;
$supplierinfo = $this->model->getSupplierInfo($uid);
$ordercount = $supplierinfo['ordercount'];
$commission_total = number_format($supplierinfo['commission_total'], 2);
$costmoney = number_format($supplierinfo['costmoney'], 2);
$expect_money = number_format($supplierinfo['expect_money'], 2);
$commission_ok = $costmoney;
$supplierinfo['costmoney_total'] = number_format($supplierinfo['costmoney_total'], 2);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($_W['isajax']) {
 	if ($operation == 'order') {
		$status = trim($_GPC['status']);
    	if ($status != ''){
        	$conditionq = '  and o.status=' . intval($status);
    	}else {
    		$conditionq = '  and o.status>=0';
    	}
        if (!empty($_GPC['id'])) {
            $condition .=' AND o.id<:id';
            $params[':id'] = intval($_GPC['id']);
        }
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
        //$sql = 'SELECT id,ordersn,price,status,address,createtime FROM ' . tablename('sz_yi_order') . ' WHERE 1 ' . $conditionq . ' AND uniacid = ' . $_W['uniacid'] . ' AND supplier_uid = ' . $uid . ' ORDER BY createtime DESC ';
        $sql = 'SELECT o.id,o.ordersn,o.price,o.status,o.address,o.createtime FROM ' . tablename('sz_yi_order') . ' o LEFT JOIN ' . tablename('sz_yi_order_refund') . ' r ON r.orderid = o.id AND ifnull(r.status, -1)<>-1 WHERE 1 ' . $condtionq . ' AND o.uniacid = ' . $_W['uniacid'] . ' AND o.supplier_uid = ' . $uid . ' ORDER BY o.createtime DESC, o.status DESC ';
    	//$sql = "select o.id,o.ordersn,o.price,o.openid,o.status,o.address,o.createtime from " . tablename('sz_yi_order') . " o " . " left join  ".tablename('sz_yi_order_goods')."  og on o.id=og.orderid left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 " . " where 1 {$conditionq} and o.uniacid=".$_W['uniacid']." and o.supplier_uid={$uid} ORDER BY o.createtime DESC,o.status DESC  ";
        $ordercount = pdo_fetchcolumn("select count(o.id) from " . tablename('sz_yi_order') . " o " . " left join  ".tablename('sz_yi_order_goods')."  og on o.id=og.orderid left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 " . " where 1 and o.uniacid=".$_W['uniacid']." and o.supplier_uid={$uid}");
        $pageno = ceil($ordercount/$psize);
    	$sql .= "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
    	$list = pdo_fetchall($sql);
    	foreach ($list as &$rowp) {
			$sql = 'SELECT og.goodsid,og.total,g.title,g.thumb,og.price,og.optionname as optiontitle,og.optionid FROM ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on og.goodsid = g.id ' . ' where og.orderid=:orderid order by og.id asc';
			$rowp['goods'] = set_medias(pdo_fetchall($sql, array(':orderid' => $rowp['id'])), 'thumb');
			$rowp['goodscount'] = count($rowp['goods']);
			$address = unserialize($rowp['address']);
	 		$rowp['address'] = $address['address'];
	 		$rowp['province'] = $address['province'];
	 		$rowp['city'] = $address['city'];
	 		$rowp['area'] = $address['area'];
	 		$rowp['createtime'] = date('Y-m-d H:i', $rowp['createtime']);
	 		$rowp['isstatus'] = $rowp['status'];
	 		if ($rowp['status'] == 0) {
	 		$rowp['status'] = '待付款';
			} else {
	 			if ($rowp['status'] == 1) {
	 				$rowp['status'] = '已付款';
	 			} else {
	 				if ($rowp['status'] == 2) {
	 					$rowp['status'] = '待收货';
	 				} else {
	 					if ($rowp['status'] == 3) {
	 						$rowp['status'] = '已完成';
	 					}
	 				}
	 			}
			}
		}
	    return show_json(2, array('list' => $list,'pagesize' => $psize,'setlevel'=>$setids,'pageno' => $pageno));
	}
	if ($operation == 'display') {
        if (empty($set['apply_day'])) {
            $is_show_check = false;
            $check_money = '';
        } else {
            $is_show_check = true;
            $check_money = "订单完成{$set['apply_day']}天后可提现{$expect_money}元";
        }
        if ($commission_ok<=0 || !empty($supplierinfo['limit_day'])) {
            $is_show_withdraw = false;
        } else {
            $is_show_withdraw = true;
        }

        return show_json(1, array(
            'avatar'            => $member['avatar'],
            'nickname'          => $member['nickname'],
            'username'          => $supplieruser['username'],
            'is_show_check'     => $is_show_check,
            'check_money'       => $check_money,
            'commission_total'  => $commission_total,
            'costmoney'         => $costmoney,
            'is_show_withdraw'  => $is_show_withdraw,
            'expect_money'      => $expect_money,
            'costmoney_total'   => $supplierinfo['costmoney_total'],
            'ordercount'        => $ordercount
        ));
    }
}
include $this->template('orderj');
