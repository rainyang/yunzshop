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
$commission_total = $supplierinfo['commission_total'];
$costmoney = $supplierinfo['costmoney'];
$commission_ok = number_format($costmoney, 2);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($_W['isajax']) {
 	if ($operation == 'order') {
		$status = trim($_GPC['status']);
    	if ($status != ''){
        	$conditionq = '  and o.status=' . intval($status);
    	}else {
    		$conditionq = '  and o.status>=0';	
    	}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
    	$sql = "select o.id,o.ordersn,o.price,o.openid,o.status,o.address,o.createtime from " . tablename('sz_yi_order') . " o " . " left join  ".tablename('sz_yi_order_goods')."  og on o.id=og.orderid left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 " . " where 1 {$conditionq} and o.uniacid=".$_W['uniacid']." and o.supplier_uid={$uid} ORDER BY o.createtime DESC,o.status DESC  ";
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
	    return show_json(2, array('list' => $list,'pagesize' => $psize,'setlevel'=>$setids));
	} else if ($operation == 'display') {
        $openid = m('user')->getOpenid();
        $set = $this->getSet();
        $member = m('member')->getMember($openid);
        $supplieruser = $this->model->getSupplierUidAndUsername($openid);
        $uid = $supplieruser['uid'];
        $username = $supplieruser['username'];
        $_GPC['type'] = $_GPC['type'] ? $_GPC['type'] : 0;
        $supplierinfo = $this->model->getSupplierInfo($uid);
        $ordercount = $supplierinfo['ordercount'];
        $commission_total = $supplierinfo['commission_total'];
        $costmoney = $supplierinfo['costmoney'];
        $commission_ok = number_format($costmoney, 2);
	    return show_json(1, array(
	        'openid' => $openid,
            'set'    => $set,
            'member' => $member,
            'supplieruser' => $supplieruser
        ));
    }
}
include $this->template('orderj');
