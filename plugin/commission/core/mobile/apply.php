<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$member = $this->model->getInfo($openid, array('ok'));
$settingalipay =   m('common')->getSysset(array(
        'shop',
        'pay'
));
if ($_W['isajax']) {
	$level = $this->set['level'];
	$closewithdrawcheck = $this->set['closewithdrawcheck'];
	$creditnocheck = $this->set['creditnocheck'];
	$member = $this->model->getInfo($openid, array('ok'));
	$time = time();
	$day_times = intval($this->set['settledays']) * 3600 * 24;
	$commission_ok = $member['commission_ok'];
	$cansettle = $commission_ok >= floatval($this->set['withdraw']);
	$member['commission_ok'] = number_format($commission_ok, 2);
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	if ($_W['ispost']) {
		if($commission_ok <= 0){
			return show_json('0', '提现金额不能为0！');
		}
		if ($_GPC['type'] == 3 && ($member['alipay'] == "" || $member['alipayname'] == "")) {
			return show_json(0, "您个人中心我的资料中未填写支付宝账号或姓名");
		}
		$orderids = array();
		if ($level >= 1) {
			$level1_orders = pdo_fetchall('select distinct o.id from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid=:agentid and o.status>=3  and og.status1=0 and og.nocommission=0 and ({$time} - o.createtime > {$day_times}) and o.uniacid=:uniacid  group by o.id", array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
			if(p('hotel')){
			$level1_orders = pdo_fetchall('select distinct o.id from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid=:agentid and o.status>=3  and o.status<>4 and o.status<>5 and  o.status<>6 and og.status1=0 and og.nocommission=0 and ({$time} - o.createtime > {$day_times}) and o.uniacid=:uniacid  group by o.id", array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
			}
			foreach ($level1_orders as $o) {
				if (empty($o['id'])) {
					continue;
				}
				$orderids[] = array('orderid' => $o['id'], 'level' => 1);
			}
		}
		if ($level >= 2) {
			if ($member['level1'] > 0) {
				$level2_orders = pdo_fetchall('select distinct o.id from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid in( " . implode(',', array_keys($member['level1_agentids'])) . ")  and o.status>=3  and og.status2=0 and og.nocommission=0 and ({$time} - o.createtime > {$day_times}) and o.uniacid=:uniacid  group by o.id", array(':uniacid' => $_W['uniacid']));
				if(p('hotel')){
				$level2_orders = pdo_fetchall('select distinct o.id from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid in( " . implode(',', array_keys($member['level1_agentids'])) . ")  and o.status>=3 and o.status<>4 and o.status<>5 and  o.status<>6  and og.status2=0 and og.nocommission=0 and ({$time} - o.createtime > {$day_times}) and o.uniacid=:uniacid  group by o.id", array(':uniacid' => $_W['uniacid']));
				}
				foreach ($level2_orders as $o) {
					if (empty($o['id'])) {
						continue;
					}
					$orderids[] = array('orderid' => $o['id'], 'level' => 2);
				}
			}
		}
		if ($level >= 3) {
			if ($member['level2'] > 0) {
				$level3_orders = pdo_fetchall('select distinct o.id from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid in( " . implode(',', array_keys($member['level2_agentids'])) . ")  and o.status>=3  and  og.status3=0 and og.nocommission=0 and ({$time} - o.createtime > {$day_times})   and o.uniacid=:uniacid  group by o.id", array(':uniacid' => $_W['uniacid']));
				if(p('hotel')){
				$level3_orders = pdo_fetchall('select distinct o.id from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid in( " . implode(',', array_keys($member['level2_agentids'])) . ")  and o.status>=3  and o.status<>4 and o.status<>5 and  o.status<>6 and  og.status3=0 and og.nocommission=0 and ({$time} - o.createtime > {$day_times})   and o.uniacid=:uniacid  group by o.id", array(':uniacid' => $_W['uniacid']));
				}
				foreach ($level3_orders as $o) {
					if (empty($o['id'])) {
						continue;
					}
					$orderids[] = array('orderid' => $o['id'], 'level' => 3);
				}
			}
		}
		$settingalipay =   m('common')->getSysset(array(
        'shop',
        'pay'
		));
		if( $_GPC['type'] =='2'){
			if($settingalipay['pay']['weixin']!='1' || $settingalipay['pay']['weixin_withdrawals']!='1' ){
				return show_json('0', '商家未开启微信支付或微信红包提现功能！!');
			}

		}
		if( $_GPC['type'] =='3'){
			if($settingalipay['pay']['alipay']!='1' || $settingalipay['pay']['alipay_withdrawals']!='1' ){
				return show_json('0', '商家未开启支付宝支付或支付宝提现功能！!');
			}
		}
		$time = time();
		foreach ($orderids as $o) {
			pdo_update('sz_yi_order_goods', array('status' . $o['level'] => 1, 'applytime' . $o['level'] => $time), array('orderid' => $o['orderid'], 'uniacid' => $_W['uniacid']));
		}
		$applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');

		$apply = array(
		    	'uniacid' => $_W['uniacid'], 
		    	'applyno' => $applyno, 
		    	'orderids' => iserializer($orderids), 
		    	'mid' => $member['id'], 
		    	'commission' => $commission_ok, 
		    	'type' => intval($_GPC['type']), 
		    	'status' => 1, 
		    	'applytime' => $time,
		    	'alipay' => $member['alipay'],
		    	'alipayname' => $member['alipayname']
		    );
	
		//Author:ym Date:2016-07-15 Content:减去已消费的佣金
		if ($member['credit20'] > 0) {
			$credit20 = - $member['credit20'];
			m('member')->setCredit($openid, 'credit20', $credit20);
			$apply['credit20'] = $member['credit20'];
		}
		pdo_insert('sz_yi_commission_apply', $apply);
		$id = pdo_insertid();
		switch ($apply['type']) {
			case 1:
				$typename = "微信钱包";
				break;
			case 2:
				$typename = "微信红包";
				break;
			case 3:
				$typename = "支付宝";
				break;
			default:
				$typename = "余额";
				break;
		}
		//佣金提现免审核自动打款
		//提现到支付宝需手动打款
		if ($creditnocheck == 1 && $_GPC['type'] !='3') {
			//填写免审核限额则开启自动打款
			if ($commission_ok <= $closewithdrawcheck || $apply['type'] == 0) {
				//提现金额在0-审核金额之内则自动打款或开启余额自动打款
				$time = time();
				$pay = $commission_ok;
				if ($apply['type'] == 1 || $apply['type'] == 2) {
					//微信支付方式 钱包或者红包 金额乘100  1为钱包，2为红包
					$pay *= 100;
				} 

				if ($apply['type'] == 2) {
					//红包提现发送红包
					if ($pay <= 20000 && $pay >= 1) {
						$result = m('finance')->sendredpack($openid, $pay, 0, $desc = '佣金提现', $act_name = '佣金提现', $remark = '佣金提现金额以红包形式发送');
					} else {
						message('红包提现金额限制1-200元！', '', 'error');
					}
				} else {
					//微信钱包或余额
					$result = m('finance')->pay($openid, $apply['type'], $pay, $apply['applyno'], '佣金提现');
				}
				
				if (is_error($result)) {
					if (strexists($result['message'], '系统繁忙')) {
						$updateno['applyno'] = $apply['applyno'] = m('common')->createNO('commission_apply', 'applyno', 'CA');
						pdo_update('sz_yi_commission_apply', $updateno, array('id' => $apply['id']));
						$result = m('finance')->pay($openid, $apply['type'], $pay, $apply['applyno']);
						if (is_error($result)) {
							message($result['message'], '', 'error');
						}
					}
					message($result['message'], '', 'error');
				}

				pdo_update('sz_yi_commission_apply', array('status' => 3, 'paytime' => $time, 'commission_pay' => $commission_ok), array('id' => $id, 'uniacid' => $_W['uniacid']));
				$log = array('uniacid' => $_W['uniacid'], 'applyid' => $id, 'mid' => $member['id'], 'commission' => $commission_ok, 'commission_pay' => $commission_ok, 'createtime' => $time);
				pdo_insert('sz_yi_commission_log', $log);
				$this->model->sendMessage($openid, array('commission' => $commission_ok, 'type' => $typename), TM_COMMISSION_PAY);
				$this->model->upgradeLevelByCommissionOK($openid);
				plog('commission.apply.pay', "佣金打款 ID: {$id} 申请编号: {$apply['applyno']} 总佣金: {$commission_ok} 审核通过佣金: {$commission_ok} ");
				return show_json(1, '已自动打款!');
			} else {
				//开启审核走正常流程
				$returnurl = urlencode($this->createMobileUrl('member/withdraw'));
				$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
				$this->model->sendMessage($openid, array('commission' => $commission_ok, 'type' => $typename), TM_COMMISSION_APPLY);
				return show_json(1, '已提交,请等待审核!');
			}
			

		} else {
			//开启审核走正常流程
			$returnurl = urlencode($this->createMobileUrl('member/withdraw'));
			$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
			$this->model->sendMessage($openid, array('commission' => $commission_ok, 'type' => $typename), TM_COMMISSION_APPLY);
			return show_json(1, '已提交,请等待审核!');
			
		}
	}
	$returnurl = urlencode($this->createPluginMobileUrl('commission/apply'));
	$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
	return show_json(1, array('commission_ok' => $commission_ok, 'cansettle' => $cansettle, 'member' => $member, 'set' => $this->set, 'infourl' => $infourl, 'noinfo' => empty($member['realname']), 'settingalipay' => $settingalipay));
}
include $this->template('apply');
