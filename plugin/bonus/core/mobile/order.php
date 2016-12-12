<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$member = $this->model->getInfo($openid, array('ordercount', 'totaly'));
$agentLevel = $this->model->getLevel($openid);
$level = intval($this->set['level']);
$commissioncount = 0;
$ordercount = $member['ordercount'];
$commissioncount = number_format($member['commission_totaly'], 2);

if ($_W['isajax']) {
	$status = trim($_GPC['status']);
	if ($status != '')
        $conditionq = '  and o.status=' . intval($status);
    else $conditionq = '  and o.status>=0';	

	if ($level >= 1) {
		$conditionb= ")  ";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$list = array();
	$sql = "select o.id,o.ordersn,o.openid,o.status,o.createtime,cg.money from " . tablename('sz_yi_order') . " o" . " left join " . tablename('sz_yi_bonus_goods') . " cg on cg.orderid=o.id where 1 {$conditionq} and bonus_area=0 and o.uniacid=".$_W['uniacid']." and cg.mid ={$member['id']} ORDER BY o.createtime DESC,o.status DESC  ";
	if(p('hotel')){
	$sql = "select o.id,o.ordersn,o.openid,o.status,o.createtime,o.order_type,cg.money from " . tablename('sz_yi_order') . " o" . " left join " . tablename('sz_yi_bonus_goods') . " cg on cg.orderid=o.id where 1 {$conditionq} and bonus_area=0 and o.uniacid=".$_W['uniacid']." and cg.mid ={$member['id']}  and o.status<>4 and  o.status<>5   ORDER BY o.createtime DESC,o.status DESC  ";
	}
	$sql .= "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
	$list = pdo_fetchall($sql);
	if (!empty($list)) {
		
		foreach ($list as &$row) {
			$row['commission'] = number_format($row['money'], 2);
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
			if(!p('hotel') ||  $row['order_type']!='3'){
			if ($row['status'] == 0) {
				$row['status'] = '待付款';
			} else if ($row['status'] == 1) {
				$row['status'] = '已付款';
			} else if ($row['status'] == 2) {
				$row['status'] = '待收货';
			} else if ($row['status'] == 3) {
				$row['status'] = '已完成';
			}
			}
			if(p('hotel') && $row['order_type']=='3'){
	 			if ($row['status'] == 0) {
					$row['status'] = '待付款';
				} else if ($row['status'] == 1) {
					$row['status'] = '已付款';
				} else if ($row['status'] == 2) {
					$row['status'] = '待入住';
				} else if ($row['status'] == 3) {
					$row['status'] = '已完成';
				}else if ($row['status'] == 4) {
					$row['status'] = '待退款';
				}else if ($row['status'] == 6) {
					$row['status'] = '待退房';
				}
 			}
			if (!empty($this->set['openorderdetail'])) {
				$goods = pdo_fetchall("SELECT og.id,og.ordergoodid,og.money,g.thumb,g.title,og.total,og.optionname from " . tablename('sz_yi_bonus_goods') . " og" . " left join " . tablename('sz_yi_goods') . " g on g.id=og.ordergoodid  " . " where og.orderid=:orderid and og.uniacid = :uniacid and og.bonus_area=0 and og.mid =:mid order by og.createtime desc ", array(':uniacid' => $_W['uniacid'], ':orderid' => $row['id'], ':mid' => $member['id']));
				$goods = set_medias($goods, 'thumb');
				foreach ($goods as &$g) {
					$g['commission'] = $g['money'];
					$commissions = iunserializer($g['commissions']);
					$g['commission'] = number_format($g['commission'], 2);
				}
				unset($g);
				$row['order_goods'] = set_medias($goods, 'thumb');
			}
			if (!empty($this->set['openorderbuyer'])) {
				$row['buyer'] = m('member')->getMember($row['openid']);
			}
		}
		unset($row);
	}
	
    return show_json(1, array('list' => $list, 'ordercount' => $ordercount, 'commissioncount' => $commissioncount, 'pagesize' => $psize));
}


include $this->template('order');
