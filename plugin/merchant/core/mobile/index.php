<?php

global $_W, $_GPC;
$openid 			= m('user')->getOpenid();
$member 			= m('member')->getMember($openid);
$_GPC['type'] 		= $_GPC['type'] ? $_GPC['type'] : 0;
$center_info		= $this->model->getInfo($openid);
$ordercount 		= $center_info['ordercount'];
$centercount		= $center_info['centercount'];
$merchantcount		= $center_info['merchantcount'];
$commission_total 	= $center_info['commission_total'];
$commission_ok 		= $center_info['commission_ok'];
$order_total_price	= $center_info['order_total_price'];
$operation 			= !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($_W['isajax']) {
 	if ($operation == 'order') {
		$status = trim($_GPC['status']);
    	if ($status != ''){
        	$conditionq = '  AND o.status=' . intval($status);
    	}else {
    		$conditionq = '  AND o.status>=0';	
    	}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$center_info = $this->model->getInfo($openid);
		$supplier_uids = $center_info['supplier_uids'];
		$supplier_cond = " AND o.supplier_uid in ({$supplier_uids})";
		if ($supplier_uids == 0) {
			$supplier_cond = " AND o.supplier_uid < 0";
		}
    	$sql = "select o.id,o.ordersn,o.price,o.openid,o.status,o.address,o.createtime from " . tablename('sz_yi_order') . " o " . " left join  ".tablename('sz_yi_order_goods')."  og on o.id=og.orderid left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 " . " where 1 {$conditionq} {$supplier_cond} and o.uniacid=".$_W['uniacid']."  ORDER BY o.createtime DESC,o.status DESC  ";
    	$sql .= "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
    	$list = pdo_fetchall($sql);
    	foreach ($list as $key => &$rowp) {
    		$list[$key]['price'] = 0;
			$sql = 'SELECT og.goodsid,og.total,g.title,g.thumb,og.price,og.optionname as optiontitle,og.optionid FROM ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on og.goodsid = g.id ' . ' where og.orderid=:orderid order by og.id asc';
			$rowp['goods'] 		= set_medias(pdo_fetchall($sql, array(':orderid' => $rowp['id'])), 'thumb');
			foreach ($rowp['goods'] as $value) {
				$list[$key]['price'] += $value['price'];
			}
			$rowp['goodscount'] = count($rowp['goods']);
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
	return show_json(2, array('list' => $list,'pagesize' => $psize));
	}
}
include $this->template('index');
