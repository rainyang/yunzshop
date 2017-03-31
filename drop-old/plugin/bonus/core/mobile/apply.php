<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
if ($_W['isajax']) {
	$level = $this->set['level'];
	$member = $this->model->getInfo($openid, array('ok'));
	$time = time();
	$day_times = intval($this->set['settledays']) * 3600 * 24;
	$commission_ok = $member['commission_ok'];
	$cansettle = $commission_ok >= floatval($this->set['withdraw']);
	$member['commission_ok'] = number_format($commission_ok, 2);
	if ($_W['ispost']) {
		$orderids = array();
		//取代理商分红订单
        $sql = "select o.id from " . tablename('sz_yi_order') . " o  left join  " . tablename('sz_yi_bonus_goods') . " cg on cg.orderid=o.id  where cg.mid ={$member['id']} and o.status>=3  and cg.status=0 and ({$time} - o.createtime > {$day_times}) and o.uniacid=:uniacid";
        $orders = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
        foreach ($orders as $o) {
            if (empty($o['id'])) {
                continue;
            }
            $orderids[] = array('orderid' => $o['id'], 'level' => 1);
        }
        //可提现

		$applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');

		$apply = array(
			'uniacid' => $_W['uniacid'], 
			'applyno' => $applyno, 
			'orderids' => iserializer($orderids), 
			'mid' => $member['id'], 
			'commission' => $commission_ok, 
			'type' => intval($_GPC['type']), 
			'status' => 1, 
			'applytime' => $time
			);
		pdo_insert('sz_yi_bonus_apply', $apply);

		foreach ($orderids as $o) {
			pdo_update('sz_yi_bonus_goods', array('status' => 1, 'applytime' => $time), array('mid' => $member['id'], 'orderid' => $o['orderid'], 'uniacid' => $_W['uniacid']));
		}

		$returnurl = urlencode($this->createMobileUrl('member/withdrawg'));
		$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
		$this->model->sendMessage($openid, array('commission' => $commission_ok, 'type' => $apply['type'] == 1 ? '微信' : '余额'), TM_COMMISSION_APPLY);
		return show_json(1, '已提交,请等待审核!');
	}
	$returnurl = urlencode($this->createPluginMobileUrl('bonus/apply'));
	$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
	return show_json(1, array('commission_ok' => $member['commission_ok'], 'cansettle' => $cansettle, 'member' => $member, 'set' => $this->set, 'infourl' => $infourl, 'noinfo' => empty($member['realname'])));
}
include $this->template('apply');
