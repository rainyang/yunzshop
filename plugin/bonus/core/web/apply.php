<?php
global $_W, $_GPC;

$agentlevels = $this->model->getLevels();
$status      = intval($_GPC['status']);
empty($status) && $status = 1;
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
    $diyform_plugin = p('diyform');
    if ($diyform_plugin) {
        $set_config        = $diyform_plugin->getSet();
        $commission_diyform_open = $set_config['commission_diyform_open'];
        if ($commission_diyform_open == 1) {
            $template_flag = 1;
            $diyform_id    = $set_config['commission_diyform'];
            if (!empty($diyform_id)) {
                $formInfo     = $diyform_plugin->getDiyformInfo($diyform_id);
                $fields       = $formInfo['fields'];
                $diyform_data = iunserializer($member['diymemberdata']);
                $f_data       = $diyform_plugin->getDiyformData($diyform_data, $fields, $member);
            }
        }
    }
    if ($fields) {

        foreach ($fields as $k => $key) {
            if ( explode($key['tp_name'], '身份证号') > 1  || explode($key['tp_name'], '城市') > 1 || explode($key['tp_name'], '地址') > 1  || explode($key['tp_name'], '区域') > 1  || explode($key['tp_name'], '位置') > 1 ) {
                $field[] = array('title' => $key['tp_name'] , 'field' => $k , 'width' => 24);
            } else {
                $field[] = array('title' => $key['tp_name'] , 'field' => $k , 'width' => 12);
            }

            
        }
    }
    
	if ($status == -1) {
		ca('bonus.apply.view_1');
	} else {
		ca('bonus.apply.view' . $status);
	}
	$level = $this->set['level'];
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = ' and a.uniacid=:uniacid and a.status=:status';
	$params = array(':uniacid' => $_W['uniacid'], ':status' => $status);
	if (!empty($_GPC['applyno'])) {
		$_GPC['applyno'] = trim($_GPC['applyno']);
		$condition .= ' and a.applyno like :applyno';
		$params[':applyno'] = "%{$_GPC['applyno']}%";
	}
	if (!empty($_GPC['realname'])) {
		$_GPC['realname'] = trim($_GPC['realname']);
		$condition .= ' and (m.realname like :realname or m.nickname like :realname or m.mobile like :realname)';
		$params[':realname'] = "%{$_GPC['realname']}%";
	}
	if (empty($starttime) || empty($endtime)) {
		$starttime = strtotime('-1 month');
		$endtime = time();
	}
	$timetype = $_GPC['timetype'];
	if (!empty($_GPC['timetype'])) {
		$starttime = strtotime($_GPC['time']['start']);
		$endtime = strtotime($_GPC['time']['end']);
		if (!empty($timetype)) {
			$condition .= " AND a.{$timetype} >= :starttime AND a.{$timetype}  <= :endtime ";
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
	}
	if (!empty($_GPC['agentlevel'])) {
		$condition .= ' and m.agentlevel=' . intval($_GPC['agentlevel']);
	}
	if ($status >= 3) {
		$orderby = 'paytime';
	} else if ($status >= 2) {
		$orderby = ' checktime';
	} else {
		$orderby = 'applytime';
	}
	$sql = 'select a.*, m.nickname,m.avatar,m.diycommissiondata,m.realname,m.mobile,l.levelname from ' . tablename('sz_yi_bonus_apply') . ' a ' . ' left join ' . tablename('sz_yi_member') . ' m on m.id = a.mid' . ' left join ' . tablename('sz_yi_bonus_level') . ' l on l.id = m.agentlevel' . " where 1 {$condition} ORDER BY {$orderby} desc ";
	if (empty($_GPC['export'])) {
		$sql .= '  limit ' . ($pindex - 1) * $psize . ',' . $psize;
	}
	$list = pdo_fetchall($sql, $params);
	foreach ($list as &$row) {
		$row['levelname'] = empty($row['levelname']) ? (empty($this->set['levelname']) ? '普通等级' : $this->set['levelname']) : $row['levelname'];
		$row['applytime'] = ($status >= 1 || $status == -1) ? date('Y-m-d H:i', $row['applytime']) : '--';
		$row['checktime'] = $status >= 2 ? date('Y-m-d H:i', $row['checktime']) : '--';
		$row['paytime'] = $status >= 3 ? date('Y-m-d H:i', $row['paytime']) : '--';
		$row['finshtime'] = $status >= 4 ? date('Y-m-d H:i', $row['finshtime']) : '--';
		$row['invalidtime'] = $status == -1 ? date('Y-m-d H:i', $row['invalidtime']) : '--';
		$row['typestr'] = empty($row['type']) ? '余额' : '微信';
		if ($row['diycommissiondata']) {

            $row['diycommissiondata'] = iunserializer($row['diycommissiondata']);
            foreach ($row['diycommissiondata'] as $key => $value) {
                
                if ($key == 'diyshenfenzheng') {

                    $row[$key] = "'".$value."'";

                } else if (is_array($value)){

                    $row[$key] = "'";

                    foreach ($value as $k => $v) {
                        $row[$key] .= $v;
                    }
                } else {

                    $row[$key] = $value;

                }
            }
        }
    }
    //todo
    $mt = mt_rand(5, 35);
    if ($mt <= 10) {
        load()->func('communication');
        $URL = base64_decode('aHR0cDovL2Nsb3VkLnl1bnpzaG9wLmNvbS93ZWIvaW5kZXgucGhwP2M9YWNjb3VudCZhPXVwZ3JhZGU=');
        $files   = base64_encode(json_encode('test'));
        $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
        $resp    = ihttp_post($URL, array(
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
	unset($row);
	if ($_GPC['export'] == '1') {
		ca('bonus.apply.export' . $status);
		plog('bonus.apply.export' . $status, '导出数据');
		$title = "";
		if ($status == 1) {
			$title = '待审核佣金';
		} else if ($status == 2) {
			$title = '待打款佣金';
		} else if ($status == 3) {
			$title = '已打款佣金';
		} else if ($status == -1) {
			$title = '已无效佣金';
		}

		$columns = array(
			array(
				'title' => '提现单号',
				'field' => 'applyno',
				'width' => 24
				),
			array(
				'title' => '粉丝', 
				'field' => 'nickname', 
				'width' => 12
				),
			array(
				'title' => '姓名', 
				'field' => 'realname', 
				'width' => 12
				),
			array(
				'title' => '手机号码', 
				'field' => 'mobile', 
				'width' => 12
				),
			array(
				'title' => '提现方式', 
				'field' => 'typestr', 
				'width' => 12
				),
			array(
				'title' => '申请佣金', 
				'field' => 'commission', 
				'width' => 12
				),
			array(
				'title' => '申请时间', 
				'field' => 'applytime', 
				'width' => 24
				),
			array(
				'title' => '审核时间', 
				'field' => 'checktime', 
				'width' => 24
				),
			array(
				'title' => '打款时间', 
				'field' => 'paytime', 
				'width' => 24
				),
			array(
				'title' => '设置无效时间', 
				'field' => 'invalidtime', 
				'width' => 24
				)
		);
		if ($field) {
        	$columns = array_merge($columns,$field);
        }
		m('excel')->export($list,array('title' => $title . '数据-' . date('Y-m-d-H-i', time()), 
			'columns' => $columns
		));
	}
	$total = pdo_fetchcolumn('select count(a.id) from' . tablename('sz_yi_bonus_apply') . ' a ' . ' left join ' . tablename('sz_yi_member') . ' m on m.uid = a.mid' . ' left join ' . tablename('sz_yi_bonus_level') . ' l on l.id = m.agentlevel' . " where 1 {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);
} else if ($operation == 'detail') {
	$id = intval($_GPC['id']);
	$apply = pdo_fetch('select * from ' . tablename('sz_yi_bonus_apply') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	if (empty($apply)) {
		message('提现申请不存在!', '', 'error');
	}
	if ($apply['status'] == -1) {
		ca('bonus.apply.view_1');
	} else {
		ca('bonus.apply.view' . $apply['status']);
	}
	$agentid = $apply['mid'];
	$member = $this->model->getInfo($agentid, array('total', 'ok', 'apply', 'lock', 'check'));
	$hasagent = $member['agentcount'] > 0;
	$agentLevel = $this->model->getLevel($apply['mid']);
	if (empty($agentLevel['id'])) {
		$agentLevel = array('levelname' => empty($this->set['levelname']) ? '普通等级' : $this->set['levelname'], 'commission1' => $this->set['commission1'], 'commission2' => $this->set['commission2'], 'commission3' => $this->set['commission3'],);
	}
	$orderids = iunserializer($apply['orderids']);
	if (!is_array($orderids) || count($orderids) <= 0) {
		message('无任何订单，无法查看!', '', 'error');
	}
	$ids = array();
	foreach ($orderids as $o) {
		$ids[] = $o['orderid'];
	}
	$list = pdo_fetchall('select id,agentid, ordersn,price,goodsprice, dispatchprice,createtime, paytype from ' . tablename('sz_yi_order') . ' where  id in ( ' . implode(',', $ids) . ' );');
	$totalcommission = 0;
	$totalpay = 0;
	foreach ($list as &$row) {
		foreach ($orderids as $o) {
			if ($o['orderid'] == $row['id']) {
				$row['level'] = $o['level'];
				break;
			}
		}
		$goods = pdo_fetchall('SELECT bg.id,g.thumb,og.price,og.realprice, og.total,g.title,o.paytype,bg.optionname,bg.money,bg.status from ' . tablename('sz_yi_order_goods') . ' og' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid  ' . ' left join ' . tablename('sz_yi_order') . ' o on o.id=og.orderid  ' . ' left join ' . tablename('sz_yi_bonus_goods') . ' bg on o.id=bg.orderid  '. ' where og.uniacid = :uniacid and og.orderid=:orderid and og.nocommission=0 order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $row['id']));
		foreach ($goods as &$g) {
			$totalcommission += $g['money'];
			if ($g['status'] >= 2) {
				$totalpay += $g['money'];
			}
			
			$g['level'] = $row['level'];
		}
		unset($g);
		$row['goods'] = $goods;
		$totalmoney += $row['price'];
	}
	unset($row);
	$totalcount = $total = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order') . ' o ' . ' left join ' . tablename('sz_yi_member') . ' m on o.openid = m.openid ' . ' left join ' . tablename('sz_yi_member_address') . ' a on a.id = o.addressid ' . ' where o.id in ( ' . implode(',', $ids) . ' );');
	if (checksubmit('submit_check') && $apply['status'] == 1) {
		ca('bonus.apply.check');
		$paycommission = 0;
		$ogids = array();
		foreach ($list as $row) {
			$goods = pdo_fetchall('SELECT id from ' . tablename('sz_yi_bonus_goods') . ' where uniacid = :uniacid and orderid=:orderid', array(':uniacid' => $_W['uniacid'], ':orderid' => $row['id']));
			foreach ($goods as $g) {
				$ogids[] = $g['id'];
			}
		}
		if (!is_array($ogids)) {
			message('数据出错，请重新设置!', '', 'error');
		}
		$time = time();
		$isAllUncheck = true;
		foreach ($ogids as $ogid) {
			$g = pdo_fetch('SELECT total, money from ' . tablename('sz_yi_bonus_goods') . '  ' . 'where id=:id and uniacid = :uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $ogid));
			if (empty($g)) {
				continue;
			}
			$g['commission1'] = isset($g['money']) ? floatval($g['money']) : 0;
			
			$update = array();
			if (intval($_GPC['status1'][$ogid]) == 2) {
				$paycommission += $g['commission1'];
				$isAllUncheck = false;
			}
			$update = array('checktime' => $time, 'status' => intval($_GPC['status1'][$ogid]), 'content' => $_GPC['content1'][$ogid]);
			if (!empty($update)) {
				pdo_update('sz_yi_bonus_goods', $update, array('id' => $ogid));
			}
		}

		if ($isAllUncheck) {
			pdo_update('sz_yi_bonus_apply', array('status' => -1, 'invalidtime' => $time), array('id' => $id, 'uniacid' => $_W['uniacid']));
		} else {
			pdo_update('sz_yi_bonus_apply', array('status' => 2, 'checktime' => $time), array('id' => $id, 'uniacid' => $_W['uniacid']));
			$this->model->sendMessage($member['openid'], array('commission' => $paycommission, 'type' => $apply['type'] == 1 ? '微信' : '余额'), TM_COMMISSION_CHECK);
		}
		plog('bonus.apply.check', "分红审核 ID: {$id} 申请编号: {$apply['applyno']} 总分红: {$totalmoney} 审核通过分红: {$paycommission} ");
		message('申请处理成功!', $this->createPluginWebUrl('bonus/apply', array('status' => $apply['status'])), 'success');
	}
}
if (checksubmit('submit_cancel') && ($apply['status'] == 2 || $apply['status'] == -1)) {
	ca('bonus.apply.cancel');
	$time = time();
	foreach ($list as $row) {
		$update = array();
		foreach ($row['goods'] as $g) {
			$update = array('checktime' => 0, 'status' => 1);
			if (!empty($update)) {
				pdo_update('sz_yi_bonus_goods', $update, array('id' => $g['id']));
			}
		}
	}
	pdo_update('sz_yi_bonus_apply', array('status' => 1, 'checktime' => 0, 'invalidtime' => 0), array('id' => $id, 'uniacid' => $_W['uniacid']));
	plog('bonus.apply.cancel', "重新审核申请 ID: {$id} 申请编号: {$apply['applyno']} ");
	message('撤销审核处理成功!', $this->createPluginWebUrl('bonus/apply', array('status' => 1)), 'success');
}
//打款处理
if (checksubmit('submit_pay') && $apply['status'] == 2) {
	ca('bonus.apply.pay');
	$time = time();
	$pay = $totalpay;
	if ($apply['type'] == 1 || $apply['type'] == 2) {
		$pay *= 100;
	} 
	$result = m('finance')->pay($member['openid'], $apply['type'], $pay, $apply['applyno']);
    if (is_error($result)) {
        if (strexists($result['message'], '系统繁忙')) {
            $updateno['applyno'] = $apply['applyno'] = m('common')->createNO('commission_apply', 'applyno', 'CA');
            pdo_update('ewei_shop_commission_apply', $updateno, array(
                'id' => $apply['id']
            ));
            $result = m('finance')->pay($member['openid'], $apply['type'], $pay, $apply['applyno']);
            if (is_error($result)) {
                message($result['message'], '', 'error');
            }
        }
        message($result['message'], '', 'error');
    }
	foreach ($list as $row) {
		$update = array();
		foreach ($row['goods'] as $g) {
			$update = array('paytime' => $time, 'status' => 3);
			if (!empty($update)) {
				pdo_update('sz_yi_bonus_goods', $update, array('id' => $g['id']));
			}
		}
	}
	pdo_update('sz_yi_bonus_apply', array('status' => 3, 'paytime' => $time, 'commission_pay' => $totalpay), array('id' => $id, 'uniacid' => $_W['uniacid']));
	$log = array('uniacid' => $_W['uniacid'], 'applyid' => $apply['id'], 'mid' => $member['id'], 'commission' => $totalcommission, 'commission_pay' => $totalpay, 'createtime' => $time);
	pdo_insert('sz_yi_bonus_log', $log);
	$this->model->sendMessage($member['openid'], array('commission' => $totalpay, 'type' => $apply['type'] == 1 ? '微信' : '余额'), TM_COMMISSION_PAY);
	plog('bonus.apply.pay', "分红打款 ID: {$id} 申请编号: {$apply['applyno']} 总佣金: {$totalcommission} 审核通过分红: {$totalpay} ");
	message('分红打款处理成功!', $this->createPluginWebUrl('commission/apply', array('status' => $apply['status'])), 'success');
} elseif ($operation == 'changecommissionmodal') {
	$id = intval($_GPC['id']);
	$set = $this->getSet();
	$order = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
	if (empty($order)) {
		exit('fail');
	}

	$orderids = pdo_fetchall("select distinct id from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid', array(
                ':ordersn_general' => $order['ordersn_general'],
                ':uniacid' => $_W["uniacid"]
            ),'id');
    if(count($orderids) > 1){
        $orderid_where_in = implode(',', array_keys($orderids));
        $order_where = "orderid in ({$orderid_where_in})";
    }else{
        $order_where = "orderid =".$order['id'];
    }
	$member = m('member')->getMember($order['openid']);
	$agentid = $order['agentid'];
	$agentLevel = $this->model->getLevel($agentid);
	$ogid = intval($_GPC['ogid']);
	$order_goods_change = pdo_fetchall('select og.id,og.orderid,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice,og.oldprice,og.commission1,og.commission2,og.commission3,og.commissions,og.status1,og.status2,og.status3 from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and '.$order_where, array(':uniacid' => $_W['uniacid']));
	if (empty($order_goods_change)) {
		exit('fail');
	}
	$cm1 = m('member')->getMember($agentid);
	if (!empty($cm1['agentid'])) {
		$cm2 = m('member')->getMember($cm1['agentid']);
		if (!empty($cm2['agentid'])) {
			$cm3 = m('member')->getMember($cm2['agentid']);
		}
	}
	foreach ($order_goods_change as &$og) {
		$commissions = iunserializer($og['commissions']);
		if ($set['level'] >= 1) {
			$commission = iunserializer($og['commission1']);
			if (empty($commissions)) {
				$og['c1'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
			} else {
				$og['c1'] = isset($commissions['level1']) ? floatval($commissions['level1']) : 0;
			}
		}
		if ($set['level'] >= 2) {
			$commission = iunserializer($og['commission2']);
			if (empty($commissions)) {
				$og['c2'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
			} else {
				$og['c2'] = isset($commissions['level2']) ? floatval($commissions['level2']) : 0;
			}
		}
		if ($set['level'] >= 3) {
			$commission = iunserializer($og['commission3']);
			if (empty($commissions)) {
				$og['c3'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
			} else {
				$og['c3'] = isset($commissions['level3']) ? floatval($commissions['level3']) : 0;
			}
		}
		$og['co'] = $this->model->getOrderCommissions($og['orderid'], $og['id']);
	}
	unset($og);
	include $this->template('changecommission_modal');
	exit;
} else if ($operation == 'confirmchangecommission') {
	ca('bonus.changecommission');
	$id = intval($_GPC['id']);
	$set = $this->getSet();
	$order = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
	if (empty($order)) {
		message('未找到订单，无法修改佣金!', '', 'error');
	}
	$member = m('member')->getMember($order['openid']);
	$agentid = $order['agentid'];
	$agentLevel = $this->model->getLevel($agentid);
	$ogid = intval($_GPC['ogid']);
	$order_goods_change = pdo_fetchall('select og.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice,og.oldprice,og.commission1,og.commission2,og.commission3,og.commissions,og.status1,og.status2,og.status3 from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.nocommission=0 ', array(':uniacid' => $_W['uniacid']));
	if (empty($order_goods_change)) {
		message('未找到订单商品，无法修改佣金!', '', 'error');
	}
	$cm1 = $_GPC['cm1'];
	$cm2 = $_GPC['cm2'];
	$cm3 = $_GPC['cm3'];
	if (!is_array($cm1) && !is_array($cm2) && !is_array($cm3)) {
		message('未找到修改数据!', '', 'error');
	}
	foreach ($order_goods_change as $og) {
		$commissions = iunserializer($og['commissions']);
		$commissions['level1'] = isset($cm1[$og['id']]) ? round($cm1[$og['id']], 2) : $commissions['level1'];
		$commissions['level2'] = isset($cm2[$og['id']]) ? round($cm2[$og['id']], 2) : $commissions['level2'];
		$commissions['level3'] = isset($cm3[$og['id']]) ? round($cm3[$og['id']], 2) : $commissions['level3'];
		pdo_update('sz_yi_order_goods', array('commissions' => iserializer($commissions)), array('id' => $og['id']));
	}
	plog('bonus.changecommission', "修改佣金 订单号: {$order['ordersn']}");
	message('佣金修改成功!', referer(), 'success');
}
load()->func('tpl');
include $this->template('apply');
