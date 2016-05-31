<?php

global $_W, $_GPC;
$openid = m('user')->getOpenid();
$set = $this->getSet();
$member = m('member')->getMember($openid);
$user = pdo_fetch("select uid,username from " . tablename('sz_yi_perm_user') . " where openid='{$openid}' and uniacid={$_W['uniacid']}");
$uid = $user['uid'];
$username = $user['username'];
$_GPC['type'] = $_GPC['type'] ? $_GPC['type'] : 0;
//订单数量
$ordercount0 = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order') . " where supplier_uid={$uid} and userdeleted=0 and deleted=0 and uniacid={$_W['uniacid']} ");
//已提现佣金总和
$commission_total=number_format(pdo_fetchcolumn("select sum(apply_money) from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and uid={$uid} and status=1"), 2);
//已申请的佣金
//$commission_apply=number_format($member['commission_apply'], 2);
//待处理的佣金
//$commission_check=number_format($member['commission_check'], 2);
//已提现佣金
//$commission_lock=number_format($member['commission_lock'], 2);
//可提现佣金
$costmoney = 0;
$sp_goods = pdo_fetchall("select og.* from " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_order') . " o on (o.id=og.orderid) where og.uniacid={$_W['uniacid']} and og.supplier_uid={$uid} and o.status=3 and og.supplier_apply_status=0");
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
$commission_ok=number_format($costmoney, 2);
//预计佣金
$commission_totaly=number_format($member['commission_totaly'], 2);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

if($_W['isajax']) {
 	if ($operation == 'order') {
 		//echo "<pre>"; print_r(1);exit;
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
    	$listsd = pdo_fetchall($sql);
    	foreach ($listsd as &$rowp) {
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
	show_json(2, array('list' => $listsd,'pagesize' => $psize,'setlevel'=>$setids));
	
	
	} elseif ($operation == 'order_cancel') {
		$orderid = $_GPC['orderid'];
		pdo_update('sz_yi_order', array('agentuid' => 0, 'ownerid' => 0), array('id' => $orderid, 'uniacid' => $_W['uniacid']));
		$data = array(
	        'uniacid'       => $_W['uniacid'],
	        'orderid'       => $orderid,
	        'from_agentuid' => $member['uid']
        );
    	pdo_insert("sz_yi_cancel_goods", $data);
		show_json(1,'取消订单成功');
	} elseif ($operation == 'order_send') {
		$orderid = $_GPC['orderid'];
		pdo_update('sz_yi_order', array('status' => 2), array('id' => $orderid, 'uniacid' => $_W['uniacid']));
		m('notice')->sendOrderMessage($orderid);
		show_json(1,"");
	}
}
include $this->template('orderj');
