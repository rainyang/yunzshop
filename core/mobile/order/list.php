<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$type = $_GPC['type'];
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
$r_type = array('0' => '退款', '1' => '退货退款', '2' => '换货');
$plugin_yunbi = p('yunbi');
if ($plugin_yunbi) {
	$yunbi_set = $plugin_yunbi->getSet();
}
$plugin_fund = p('fund');
$ordertitle = "我的订单";
if($plugin_fund){
	$fund_set = $plugin_fund->getSet();
	$ordertitle = $fund_set['texts']['order'];
}
if ($_W['isajax']) {
	if ($operation == 'display') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 5;
		$status = $_GPC['status'];
		$condition = ' and openid=:openid  and userdeleted=0 and deleted=0 and uniacid=:uniacid ';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);
		if ($status != '') {
			if ($status != 4) {
				if ($status == 2) {
					$condition .= ' and (status=2 or status=0 and paytype=3)';
				} else if ($status == 0) {
					$condition .= ' and status=0 and paytype!=3';
				} else {
					$condition .= ' and status=' . intval($status);
				}
			} else {
				$condition .= ' and refundstate>0 and status!=-1';
			}
		} else {

			$condition .= ' and status<>-1';
		}
	    if (p('hotel') && $type=='hotel') {	        
	          $condition.= " AND order_type=3";
		}else{
	          $condition.= " AND order_type<>3";
	    }
		$conds = '';
		if (p('channel')) {
			$conds = ',ischannelself';
		}

		$condition.= " AND plugin='".$_GPC['plugin']."'";

		$condition .= " and order_type <> 4"; //排除 夺宝订单

	    //Author:ym Date:2016-07-20 Content:订单分组查询
		$list = pdo_fetchall('select * from ' . tablename('sz_yi_order') . " where 1 {$condition} group by ordersn_general order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order') . " where 1 {$condition}", $params);
		$tradeset = m('common')->getSysset('trade');
		$refunddays = intval($tradeset['refunddays']);
		$ordersn_general = "";
		$p_cashier = p('cashier');
		foreach ($list as $key => &$row) {
			if (p('hotel')) {
				if($type=='hotel'){
					$list[$key]['btime'] = date('Y-m-d',$row['btime']);
					$list[$key]['etime'] = date('Y-m-d',$row['etime']);
		        }
			}
			/*if($row['ordersn_general'] == $ordersn_general && !empty($row['ordersn_general']) && $row['status'] == 0){
				unset($list[$key]);
				continue;
			}*/
			if(!empty($row['ordersn_general']) && $row['status'] == 0){
				$ordersn_general = $row['ordersn_general'];
				$row['ordersn'] = $row['ordersn_general'];
				$orderids = pdo_fetchall("select distinct id from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid and openid=:openid', array(
		            ':ordersn_general' => $ordersn_general,
		            ':uniacid' => $uniacid,
		            ':openid' => $openid
		        ),'id');
		        $row['price'] = pdo_fetchcolumn("select sum(price) from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid and openid=:openid', array(
		            ':ordersn_general' => $ordersn_general,
		            ':uniacid' => $uniacid,
		            ':openid' => $openid
		        ));
		        $orderid_where_in = implode(',', array_keys($orderids));
		        $order_where = "og.orderid in ({$orderid_where_in})";
			}else{
				$order_where = "og.orderid = ".$row['id'];
			}
			$channel_cond = '';
			if (p('channel')) {
				if ($row['ischannelself'] == 1) {
					$row['ordertype'] = "自提单";
				}
				$channel_cond = ',og.ischannelpay';
			}
			$sql = 'SELECT og.goodsid,og.total,g.type,g.title,g.thumb,og.price,og.optionname as optiontitle,og.optionid' . $channel_cond . ' FROM ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on og.goodsid = g.id ' . ' where '.$order_where.' order by og.id asc';
			$row['goods'] = set_medias(pdo_fetchall($sql), 'thumb');
			foreach ($row['goods'] as $k => $value) {
				if ($value['ischannelpay'] == 1) {
					$row['ordertype'] = "采购单";
				}
				break;
			}
			if($p_cashier){
				$row['name'] = set_medias(pdo_fetch('select cs.name,cs.thumb from ' .tablename('sz_yi_cashier_store'). 'cs '.'left join ' .tablename('sz_yi_cashier_order'). ' co on cs.id = co.cashier_store_id where co.order_id=:orderid and co.uniacid=:uniacid', array(':orderid' => $row['id'],':uniacid'=>$_W['uniacid'])), 'thumb');
			}
			$row['goodscount'] = count($row['goods']);
			$row['createtime'] = date('Y-m-d H:i:s',$row['createtime']);
			switch ($row['status']) {
				case '-1':
					$status = '已取消';
					break;
				case "0":
					if ($row['paytype'] == 3) {
						$status = '待发货';
					} else {
						$status = '待付款';
					}
					break;
				case '1':
					if ($row['isverify'] == 1) {
						$status = '待使用';
					} else if (empty($row['addressid'])) {
						$status = '待取货';
					} else {
						$status = '待发货';
					}
					break;
				case '2':
					$status = '待收货';
					break;
				case '3':
					if (empty($row['iscomment'])) {
						$status = '待评价';
					} else {
						$status = '交易完成';
					}
					break;
			}
			if(p('hotel') && $type=="hotel"){
				switch ($row['status']) {
				case '-1':
					$status = '已取消';
					break;
				case "0":
					if ($row['paytype'] == 3) {
						$status = '待发货';
					} else {
						$status = '待付款';
					}
					break;
				case '1':
					if ($row['isverify'] == 1) {
						$status = '待使用';
					} else if (empty($row['addressid'])) {
						$status = '待取货';
					} else {
						$status = '待确认';
					}
					break;
				case '2':
					$status = '待入住';
					break;	
				case '6':
					$status = '待退房';
					break;
				case '3':
					if (empty($row['iscomment'])) {
						$status = '待评价';
					} else {
						$status = '交易完成';
					}
					break;
			    }
			}
			$row['statusstr'] = $status;
			if ($row['refundstate'] > 0 && !empty($row['refundid'])) {
				$refund = pdo_fetch('select * from ' . tablename('sz_yi_order_refund') . ' where id=:id and uniacid=:uniacid and orderid=:orderid limit 1', array(':id' => $row['refundid'], ':uniacid' => $uniacid, ':orderid' => $row['id']));
				if (!empty($refund)) {
					$row['statusstr'] = '待' . $r_type[$refund['rtype']];
				}
			}
			$canrefund = false;
			if (($row['status'] == 1 || $row['status'] == 2) && $_GPC['plugin'] == "") {
				if ($refunddays > 0 || $row['status'] == 1) {
					$canrefund = true;
				}
			} else if ($row['status'] == 3 && $_GPC['plugin'] == "") {
				//申请售后去除核销商品与虚拟产品不允许退货
				//if ($row['isverify'] != 1 && empty($row['virtual'])) {
					if ($refunddays > 0) {
						$days = intval((time() - $row['finishtime']) / 3600 / 24);
						if ($days <= $refunddays) {
							$canrefund = true;
						}
					}
				//}
			}

			$row['canrefund'] = $canrefund;
	
			if ($canrefund == true) {
		        if ($row['status'] == 1) {
		            $row['refund_button'] = '申请退款';
		        } else {
		            $row['refund_button'] = '申请售后';
		        }
		        if (!empty($row['refundstate'])) {
		            $row['refund_button'] .= '中';
		        }
		    }
	    }
		unset($row);
		show_json(1, array('total' => $total, 'list' => $list, 'pagesize' => $psize));
	}
}
if(p('hotel')){
	if($_GPC['type']=='hotel'){
			include $this->template('order/list_hotel');
	}else{
		include $this->template('order/list');
	}
}else{
	include $this->template('order/list');
}
