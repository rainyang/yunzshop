<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
if ($_W['isajax']) {
	$iscenter = intval($_GPC['iscenter']);
	$member = m('member')->getMember($openid);
	$center_info = $this->model->getInfo($openid);
	$commission_ok = $center_info['commission_ok'];
	$cansettle = $commission_ok>=1;
	$member['commission_ok'] = number_format($commission_ok, 2);
	if ($_W['ispost']) {
		$center_info = $this->model->getInfo($openid);
		$time = time();
		$applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');
		$apply = array(
			'member_id'		=> $member['id'],
			'type'			=> $_GPC['type'],
			'applysn'		=> $applyno,
			'money'			=> $center_info['commission_ok'],
			'apply_time'	=> $time,
			'status' 		=> 0,
			'uniacid'		=> $_W['uniacid']
			);
		if (!empty($iscenter)) {
			$apply['iscenter'] = $iscenter;
		}
		pdo_insert('sz_yi_merchant_apply', $apply);
		
		if (!empty($iscenter)) {
			foreach ($center_info['order_ids'] as $value) {
				pdo_update('sz_yi_order', array('center_apply_status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $value['id']));
			}
		} else {
			$orderids = pdo_fetchall("select id from " . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and supplier_uid in ({$uids}) and status = 3 and userdeleted = 0 and deleted = 0 and merchant_apply_status = 0 ");
			foreach ($orderids as $key => $value) {
				pdo_update('sz_yi_order', array('merchant_apply_status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $value['id']));
			}
		}
		show_json(1, '已提交,请等待审核!');
	}
	$returnurl = urlencode($this->createPluginMobileUrl('commission/applyg'));
	$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
	show_json(1, array('commission_ok' => $member['commission_ok'], 'cansettle' => $cansettle, 'member' => $member, 'set' => $this->set, 'infourl' => $infourl, 'noinfo' => empty($member['realname']), 'center_info' => $center_info));
}
include $this->template('applyg');
