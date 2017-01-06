<?php

global $_W, $_GPC;
$openid 			= m('user')->getOpenid();
$set 				= $this->getSet();
$member 			= m('member')->getMember($openid);
if (empty($member['ischannel']) && empty($member['channel_level'])) {
    return;
}
$_GPC['type'] 		= $_GPC['type'] ? $_GPC['type'] : 0;
$channelinfo 		= $this->model->getInfo($openid);
$ordercount 		= $channelinfo['channel']['ordercount'];
$channelcount		= count($channelinfo['channel']['mychannels']);
$commission_total 	= $channelinfo['channel']['commission_total'];
$commission_ok 		= $channelinfo['channel']['commission_ok'] + $channelinfo['channel']['lower_order_money'] - $channelinfo['channel']['dispatchprice'];
$order_total_price	= $channelinfo['channel']['order_total_price'];
$cansettle 			= $commission_ok >= floatval($set['setapplyminmoney']);
$commission_ok 		= number_format($commission_ok, 2);
$setapplycycle		= $set['setapplycycle'] *3600;
$time 				= time();
$last_apply			= pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_apply') . " WHERE uniacid={$_W['uniacid']} AND openid='{$openid}' AND (apply_time+{$setapplycycle}>{$time}) ORDER BY id DESC");
$purchaseid			= pdo_fetchcolumn("SELECT id FROM " . tablename('sz_yi_chooseagent') . " WHERE uniacid={$_W['uniacid']} AND isopenchannel=1 LIMIT 1");
$purchaseurl		= $this->createPluginMobileUrl('choose',array('pageid'=>$purchaseid, 'ischannelpay' => 1,'ischannelpick' => 0));
$pickingurl			= $this->createPluginMobileUrl('choose',array('pageid'=>$purchaseid, 'ischannelpay' => 0,'ischannelpick' => 1));
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
    	$sql = "SELECT o.id,o.ordersn,o.price,o.openid,o.status,o.address,o.createtime FROM " . tablename('sz_yi_order') . " o " . " left join  ".tablename('sz_yi_order_goods')."  og on o.id=og.orderid left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id AND ifnull(r.status,-1)<>-1 " . " WHERE 1 {$conditionq} AND o.uniacid=".$_W['uniacid']." AND og.channel_id={$member['id']} ORDER BY o.createtime DESC,o.status DESC  ";
    	$sql .= "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
    	$list = pdo_fetchall($sql);
    	//pdo_debug();
    	foreach ($list as $key => &$rowp) {
    		$list[$key]['price'] = 0;
			$sql = 'SELECT og.goodsid,og.total,g.title,g.thumb,og.price,og.optionname as optiontitle,og.optionid FROM ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on og.goodsid = g.id ' . " WHERE og.orderid=:orderid AND og.channel_id={$member['id']} order by og.id asc";
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
        return show_json(2, array('list' => $list,'pagesize' => $psize));
	}
    return show_json(1, array('member'=>$member,'channelinfo'=>$channelinfo));
}
include $this->template('index');
