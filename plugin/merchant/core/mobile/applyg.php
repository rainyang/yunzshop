<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
if ($_W['isajax']) {
	$member = m('member')->getMember($openid);
	$suppliers = pdo_fetchall("select distinct supplier_uid from " . tablename('sz_yi_merchants') . " where member_id={$member['id']} and uniacid={$_W['uniacid']}");
	$uids = '';
	foreach ($suppliers as $key => $value) {
	    if ($key == 0) {
	        $uids .= $value['supplier_uid'];
	    } else {
	        $uids .= ','.$value['supplier_uid'];
	    }
	}
	if (empty($uids)) {
	    $uids = 0;
	}
	$time = time();
	$commission_ok = 0;
	foreach ($suppliers as $key => $value) {
	    $commissions = pdo_fetchcolumn("select commissions from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and supplier_uid={$value['supplier_uid']} and member_id={$member['id']}");
	    $order_total_price = pdo_fetchcolumn("select sum(goodsprice) from " . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and status = 3 and userdeleted = 0 and deleted = 0 and supplier_uid = {$value['supplier_uid']} and merchant_apply_status = 0 ");
	    $commission_ok += $commissions * $order_total_price/100;

	}
	$commission_ok=number_format($commission_ok, 2);
	$cansettle = $commission_ok>=1;
	$member['commission_ok'] = number_format($commission_ok, 2);
	if ($_W['ispost']) {
		$time = time();
		$applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');
		$apply = array(
			'member_id'			=> $member['id'],
			'type'			=> $_GPC['type'],
			'applysn'		=> $applyno,
			'money'	=> $member['commission_ok'],
			'apply_time'	=> $time,
			'status' 		=> 0,
			'uniacid'		=> $_W['uniacid']
			);
		pdo_insert('sz_yi_merchant_apply', $apply);
		$orderids = pdo_fetchall("select id from " . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and supplier_uid in ({$uids}) and status = 3 and userdeleted = 0 and deleted = 0 and merchant_apply_status = 0 ");
		foreach ($orderids as $key => $value) {
			pdo_update('sz_yi_order', array('merchant_apply_status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $value['id']));
		}
		$returnurl = urlencode($this->createPluginMobileUrl('merchant/orderj'));
		$infourl = $this->createPluginMobileUrl('merchant/orderj', array('returnurl' => $returnurl));
		$this->model->sendMessage($openid, array('money' => $commission_ok, 'time' => $time, 'nickname' => $member['nickname']), TM_MERCHANT_APPLY);
return show_json(1, '已提交,请等待审核!');
	}
	$returnurl = urlencode($this->createPluginMobileUrl('commission/applyg'));
	$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
return show_json(1, array('commission_ok' => $member['commission_ok'], 'cansettle' => $cansettle, 'member' => $member, 'set' => $this->set, 'infourl' => $infourl, 'noinfo' => empty($member['realname'])));
}
include $this->template('applyg');
