<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
if ($_W['isajax']) {
	$member = m('member')->getMember($openid);
	$uid = pdo_fetchcolumn("select uid from " . tablename('sz_yi_perm_user') . " where openid='{$openid}' and uniacid={$_W['uniacid']}");
	$time = time();
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
	$cansettle = $commission_ok >= 1;
	$member['commission_ok'] = number_format($commission_ok, 2);
	if ($_W['ispost']) {
		$orderids = array();
		$orderids = pdo_fetchall("select id from " . tablename('sz_yi_order') . " where supplier_uid={$uid} and uniacid={$_W['uniacid']}");
		$time = time();
		foreach ($orderids as $key => $value) {
			pdo_update('sz_yi_order', array('supplier_apply_status' => 1), array('id' => $value['id'], 'uniacid' => $_W['uniacid']));
		}
		$applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');
		$apply = array(
			'uid'			=> $member['uid'],
			'type'			=> $_GPC['type'],
			'applysn'		=> $applyno,
			'apply_money'	=> $member['commission_ok'],
			'apply_time'	=> $time,
			'status' 		=> 0,
			'uniacid'		=> $_W['uniacid']
			);
		pdo_insert('sz_yi_supplier_apply', $apply);
		$returnurl = urlencode($this->createMobileUrl('member/withdraw'));
		$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
		$this->model->sendMessage($openid, array('commission' => $commission_ok, 'type' => $apply['type'] == 2 ? '微信' : '线下'), TM_COMMISSION_APPLY);
		show_json(1, '已提交,请等待审核!');
	}
	$returnurl = urlencode($this->createPluginMobileUrl('commission/applyg'));
	$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
	show_json(1, array('commission_ok' => $member['commission_ok'], 'cansettle' => $cansettle, 'member' => $member, 'set' => $this->set, 'infourl' => $infourl, 'noinfo' => empty($member['realname'])));
}
include $this->template('applyg');
