<?php
global $_W, $_GPC;

//print_r($channellevels);exit;
$status      = intval($_GPC['status']);
empty($status) && $status = 1;
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
	$channellevels = pdo_fetchall('SELECT * FROM ' . tablename('sz_yi_channel_level') . ' WHERE uniacid = :uniacid ORDER BY level_num DESC', array(':uniacid' => $_W['uniacid']));//渠道商等级
	if ($status == -1) {
		ca('channel.withdraw.view_1');
	} else {
		ca('channel.withdraw.view' . $status);
	}
	$level = $this->set['level'];
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = ' and a.uniacid=:uniacid and a.status=:status';
	$params = array(':uniacid' => $_W['uniacid'], ':status' => $status);
	if (!empty($_GPC['applyno'])) {
		$_GPC['applyno'] = trim($_GPC['applyno']);
		$condition .= ' AND a.applyno LIKE :applyno';
		$params[':applyno'] = "%{$_GPC['applyno']}%";
	}
	if (!empty($_GPC['realname'])) {
		$_GPC['realname'] = trim($_GPC['realname']);
		$condition .= ' AND (m.realname LIKE :realname OR m.nickname LIKE :realname OR m.mobile LIKE :realname)';
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
	if (!empty($_GPC['channel_level'])) {
		$condition .= ' AND m.channel_level=' . intval($_GPC['channel_level']);
	}
	if ($status >= 3) {
		$orderby = 'finish_time';
	} else {
		$orderby = 'apply_time';
	}
	$sql = 'SELECT a.*, m.nickname,m.avatar,m.realname,m.mobile,l.level_name,l.level_num FROM ' . tablename('sz_yi_channel_apply') . ' a ' . ' left join ' . tablename('sz_yi_member') . ' m on m.id = a.mid' . ' left join ' . tablename('sz_yi_channel_level') . ' l on l.id = m.channel_level' . " WHERE 1  {$condition} ORDER BY {$orderby} DESC";
	if (empty($_GPC['export'])) {
		$sql .= '  limit ' . ($pindex - 1) * $psize . ',' . $psize;
	}
	$list = pdo_fetchall($sql, $params);
	foreach ($list as &$row) {
		$row['apply_time'] = ($status >= 1 || $status == -1) ? date('Y-m-d H:i', $row['apply_time']) : '--';
		$row['finish_time'] = $status >= 3 ? date('Y-m-d H:i', $row['finish_time']) : '--';
		$row['typestr'] = empty($row['type']) ? '余额' : '微信';
	}
	unset($row);
	if ($_GPC['export'] == '1') {
		ca('channel.withdraw.export' . $status);
		plog('channel.withdraw.export' . $status, '导出数据');
		$title = "";
		if ($status == 1) {
			$title = '待审核佣金';
		} else if ($status == 3) {
			$title = '已打款佣金';
		}

		m('excel')->export($list, array('title' => $title . '数据-' . date('Y-m-d-H-i', time()), 'columns' => array(array('title' => 'ID', 'field' => 'id', 'width' => 12), array('title' => '提现单号', 'field' => 'applyno', 'width' => 24), array('title' => '粉丝', 'field' => 'nickname', 'width' => 12), array('title' => '姓名', 'field' => 'realname', 'width' => 12), array('title' => '手机号码', 'field' => 'mobile', 'width' => 12), array('title' => '提现方式', 'field' => 'typestr', 'width' => 12),array('title' => '申请佣金', 'field' => 'apply_money', 'width' => 12), array('title' => '申请时间', 'field' => 'apply_time', 'width' => 24), array('title' => '打款时间', 'field' => 'finish_time', 'width' => 24))));
	}
	$total = pdo_fetchcolumn('SELECT count(a.id) FROM' . tablename('sz_yi_channel_apply') . ' a ' . ' left join ' . tablename('sz_yi_member') . ' m on m.uid = a.mid' . ' left join ' . tablename('sz_yi_channel_level') . ' l on l.id = m.channel_level' . " WHERE 1 {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);
} else if ($operation == 'detail') {
	$id = intval($_GPC['id']);
	$apply = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_channel_apply') . ' WHERE uniacid=:uniacid AND id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	if (empty($apply)) {
		message('提现申请不存在!', '', 'error');
	}
	if ($apply['status'] == -1) {
		ca('channel.withdraw.view_1');
	} else {
		ca('channel.withdraw.view' . $apply['status']);
	}
	$applyid = $apply['mid'];
	$member = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_member') . ' WHERE id = :id' , array(':id' => $applyid));
	$hasagent = $member['agentcount'] > 0;
	$agentLevel = $this->model->getLevel($apply['mid']);
	if (empty($agentLevel['id'])) {
		$agentLevel = array('levelname' => empty($this->set['levelname']) ? '普通等级' : $this->set['levelname'], 'channel1' => $this->set['channel1'], 'channel2' => $this->set['channel2'], 'channel3' => $this->set['channel3'],);
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
	$totalchannel = 0;
	$totalpay = 0;
	foreach ($list as &$row) {
		foreach ($orderids as $o) {
			if ($o['orderid'] == $row['id']) {
				$row['level'] = $o['level'];
				break;
			}
		}
		$goods = pdo_fetchall('SELECT og.id,g.thumb,og.price,og.realprice, og.total,g.title,o.paytype,og.optionname,og.channel1,og.channel2,og.channel3,og.channels,og.status1,og.status2,og.status3,og.content1,og.content2,og.content3 from ' . tablename('sz_yi_order_goods') . ' og' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid  ' . ' left join ' . tablename('sz_yi_order') . ' o on o.id=og.orderid  ' . ' where og.uniacid = :uniacid and og.orderid=:orderid and og.nochannel=0 order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $row['id']));
		foreach ($goods as &$g) {
			$channels = iunserializer($g['channels']);
			if ($this->set['level'] >= 1) {
				$channel = iunserializer($g['channel1']);
				if (empty($channels)) {
					$g['channel1'] = isset($channel['level' . $agentLevel['id']]) ? $channel['level' . $agentLevel['id']] : $channel['default'];
				} else {
					$g['channel1'] = isset($channels['level1']) ? floatval($channels['level1']) : 0;
				}
				if ($row['level'] == 1) {
					$totalchannel += $g['channel1'];
					if ($g['status1'] >= 2) {
						$totalpay += $g['channel1'];
					}
				}
			}
			if ($this->set['level'] >= 2) {
				$channel = iunserializer($g['channel2']);
				if (empty($channels)) {
					$g['channel2'] = isset($channel['level' . $agentLevel['id']]) ? $channel['level' . $agentLevel['id']] : $channel['default'];
				} else {
					$g['channel2'] = isset($channels['level2']) ? floatval($channels['level2']) : 0;
				}
				if ($row['level'] == 2) {
					$totalchannel += $g['channel2'];
					if ($g['status2'] >= 2) {
						$totalpay += $g['channel2'];
					}
				}
			}
			if ($this->set['level'] >= 3) {
				$channel = iunserializer($g['channel3']);
				if (empty($channels)) {
					$g['channel3'] = isset($channel['level' . $agentLevel['id']]) ? $channel['level' . $agentLevel['id']] : $channel['default'];
				} else {
					$g['channel3'] = isset($channels['level3']) ? floatval($channels['level3']) : 0;
				}
				if ($row['level'] == 3) {
					$totalchannel += $g['channel3'];
					if ($g['status3'] >= 2) {
						$totalpay += $g['channel3'];
					}
				}
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
		ca('channel.apply.check');
		$paychannel = 0;
		$ogids = array();
		foreach ($list as $row) {
			$goods = pdo_fetchall('SELECT id from ' . tablename('sz_yi_order_goods') . ' where uniacid = :uniacid and orderid=:orderid and nochannel=0', array(':uniacid' => $_W['uniacid'], ':orderid' => $row['id']));
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
			$g = pdo_fetch('SELECT total, channel1,channel2,channel3,channels from ' . tablename('sz_yi_order_goods') . '  ' . 'where id=:id and uniacid = :uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $ogid));
			if (empty($g)) {
				continue;
			}
			$channels = iunserializer($g['channels']);
			if ($this->set['level'] >= 1) {
				$channel = iunserializer($g['channel1']);
				if (empty($channels)) {
					$g['channel1'] = isset($channel['level' . $agentLevel['id']]) ? $channel['level' . $agentLevel['id']] : $channel['default'];
				} else {
					$g['channel1'] = isset($channels['level1']) ? floatval($channels['level1']) : 0;
				}
			}
			if ($this->set['level'] >= 2) {
				$channel = iunserializer($g['channel2']);
				if (empty($channels)) {
					$g['channel2'] = isset($channel['level' . $agentLevel['id']]) ? $channel['level' . $agentLevel['id']] : $channel['default'];
				} else {
					$g['channel2'] = isset($channels['level2']) ? floatval($channels['level2']) : 0;
				}
			}
			if ($this->set['level'] >= 3) {
				$channel = iunserializer($g['channel3']);
				if (empty($channels)) {
					$g['channel3'] = isset($channel['level' . $agentLevel['id']]) ? $channel['level' . $agentLevel['id']] : $channel['default'];
				} else {
					$g['channel3'] = isset($channels['level3']) ? floatval($channels['level3']) : 0;
				}
			}
			$update = array();
			if (isset($_GPC['status1'][$ogid])) {
				if (intval($_GPC['status1'][$ogid]) == 2) {
					$paychannel += $g['channel1'];
					$isAllUncheck = false;
				}
				$update = array('checktime1' => $time, 'status1' => intval($_GPC['status1'][$ogid]), 'content1' => $_GPC['content1'][$ogid]);
			} else if (isset($_GPC['status2'][$ogid])) {
				if (intval($_GPC['status2'][$ogid]) == 2) {
					$paychannel += $g['channel2'];
					$isAllUncheck = false;
				}
				$update = array('checktime2' => $time, 'status2' => intval($_GPC['status2'][$ogid]), 'content2' => $_GPC['content2'][$ogid]);
			} else if (isset($_GPC['status3'][$ogid])) {
				if (intval($_GPC['status3'][$ogid]) == 2) {
					$paychannel += $g['channel3'];
					$isAllUncheck = false;
				}
				$update = array('checktime3' => $time, 'status3' => intval($_GPC['status3'][$ogid]), 'content3' => $_GPC['content3'][$ogid]);
			}
			if (!empty($update)) {
				pdo_update('sz_yi_order_goods', $update, array('id' => $ogid));
			}
		}
		if ($isAllUncheck) {
			pdo_update('sz_yi_channel_apply', array('status' => -1, 'invalidtime' => $time), array('id' => $id, 'uniacid' => $_W['uniacid']));
		} else {
			pdo_update('sz_yi_channel_apply', array('status' => 2, 'checktime' => $time), array('id' => $id, 'uniacid' => $_W['uniacid']));
			$this->model->sendMessage($member['openid'], array('channel' => $paychannel, 'type' => $apply['type'] == 1 ? '微信' : '余额'), TM_channel_CHECK);
		}
		plog('channel.apply.check', "佣金审核 ID: {$id} 申请编号: {$apply['applyno']} 总佣金: {$totalmoney} 审核通过佣金: {$paychannel} ");
		message('申请处理成功!', $this->createPluginWebUrl('channel/apply', array('status' => $apply['status'])), 'success');
	}
}
if (checksubmit('submit_cancel') && ($apply['status'] == 2 || $apply['status'] == -1)) {
	ca('channel.apply.cancel');
	$time = time();
	foreach ($list as $row) {
		$update = array();
		foreach ($row['goods'] as $g) {
			$update = array();
			if ($row['level'] == 1) {
				$update = array('checktime1' => 0, 'status1' => 1);
			} else if ($row['level'] == 2) {
				$update = array('checktime2' => 0, 'status2' => 1);
			} else if ($row['level'] == 3) {
				$update = array('checktime3' => 0, 'status3' => 1);
			}
			if (!empty($update)) {
				pdo_update('sz_yi_order_goods', $update, array('id' => $g['id']));
			}
		}
	}
	pdo_update('sz_yi_channel_apply', array('status' => 1, 'checktime' => 0, 'invalidtime' => 0), array('id' => $id, 'uniacid' => $_W['uniacid']));
	plog('channel.apply.cancel', "重新审核申请 ID: {$id} 申请编号: {$apply['applyno']} ");
	message('撤销审核处理成功!', $this->createPluginWebUrl('channel/apply', array('status' => 1)), 'success');
}
if (checksubmit('submit_pay') && $apply['status'] == 2) {
	ca('channel.apply.pay');
	$time = time();
	$pay = $totalpay;
	if ($apply['type'] == 1 || $apply['type'] == 2) {
		$pay *= 100;
	} 

	if ($apply['type'] == 2) {
		if ($pay <= 20000 && $pay >= 1) {
			$result = m('finance')->sendredpack($member['openid'], $pay, 0, $desc = '佣金提现金额', $act_name = '佣金提现金额', $remark = '佣金提现金额以红包形式发送');
		} else {
			message('红包提现金额限制1-200元！', '', 'error');
		}
	} else {
		$result = m('finance')->pay($member['openid'], $apply['type'], $pay, $apply['applyno']);
	}
	
	if (is_error($result)) {
		if (strexists($result['message'], '系统繁忙')) {
			$updateno['applyno'] = $apply['applyno'] = m('common')->createNO('channel_apply', 'applyno', 'CA');
			pdo_update('sz_yi_channel_apply', $updateno, array('id' => $apply['id']));
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
			$update = array();
			if ($row['level'] == 1 && $g['status1'] == 2) {
				$update = array('paytime1' => $time, 'status1' => 3);
			} else if ($row['level'] == 2 && $g['status2'] == 2) {
				$update = array('paytime2' => $time, 'status2' => 3);
			} else if ($row['level'] == 3 && $g['status3'] == 2) {
				$update = array('paytime3' => $time, 'status3' => 3);
			}
			if (!empty($update)) {
				pdo_update('sz_yi_order_goods', $update, array('id' => $g['id']));
			}
		}
	}
	pdo_update('sz_yi_channel_apply', array('status' => 3, 'paytime' => $time, 'channel_pay' => $totalpay), array('id' => $id, 'uniacid' => $_W['uniacid']));
	$log = array('uniacid' => $_W['uniacid'], 'applyid' => $apply['id'], 'mid' => $member['id'], 'channel' => $totalchannel, 'channel_pay' => $totalpay, 'createtime' => $time);
	pdo_insert('sz_yi_channel_log', $log);
	$this->model->sendMessage($member['openid'], array('channel' => $totalpay, 'type' => $apply['type'] == 1 ? '微信' : '余额'), TM_channel_PAY);
	$this->model->upgradeLevelBychannelOK($member['openid']);
	plog('channel.apply.pay', "佣金打款 ID: {$id} 申请编号: {$apply['applyno']} 总佣金: {$totalchannel} 审核通过佣金: {$totalpay} ");
	message('佣金打款处理成功!', $this->createPluginWebUrl('channel/apply', array('status' => $apply['status'])), 'success');
} 
load()->func('tpl');
include $this->template('withdraw');
