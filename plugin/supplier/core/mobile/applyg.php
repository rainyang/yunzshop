<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
if ($_W['isajax']) {
	$member = m('member')->getMember($openid);
	$supplieruser = $this->model->getSupplierUidAndUsername($openid);
	$uid = $supplieruser['uid'];
	$supplierinfo = $this->model->getSupplierInfo($uid);
	$commission_ok = $supplierinfo['costmoney'];
	$cansettle = $commission_ok >= 1;
	$member['commission_ok'] = number_format($costmoney, 2);
	if ($_W['ispost']) {
		$time = time();
		$sp_goods = pdo_fetchall("select og.* from " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_order') . " o on (o.id=og.orderid) where og.uniacid={$_W['uniacid']} and og.supplier_uid={$uid} and o.status=3 and og.supplier_apply_status=0");
		$applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');
		$apply_ordergoods_ids = "";
        foreach ($sp_goods as $key => $value) {
            if ($key == 0) {
                $apply_ordergoods_ids .= $value['id'];
            } else {
                $apply_ordergoods_ids .= ','.$value['id'];
            }
        }
		$apply = array(
			'uid'			=> $uid,
			'type'			=> $_GPC['type'],
			'applysn'		=> $applyno,
			'apply_money'	=> $costmoney,
			'apply_time'	=> $time,
			'status' 		=> 0,
			'uniacid'		=> $_W['uniacid'],
			'apply_ordergoods_ids' => $apply_ordergoods_ids
			);
		pdo_insert('sz_yi_supplier_apply', $apply);
		@file_put_contents(IA_ROOT . "/addons/sz_yi/data/apply.log", print_r($apply, 1), FILE_APPEND);
		if( pdo_insertid() ) {
			foreach ($sp_goods as $key => $value) {
				pdo_update('sz_yi_order_goods', array('supplier_apply_status' => 2), array('id' => $value['id'], 'uniacid' => $_W['uniacid']));
			}
			$tmp_sp_goods = $sp_goods;
			$tmp_sp_goods['applyno'] = $applyno;
			@file_put_contents(IA_ROOT . "/addons/sz_yi/data/sp_goods.log", print_r($tmp_sp_goods, 1), FILE_APPEND);
		}

		$returnurl = urlencode($this->createPluginMobileUrl('supplier/orderj'));
		$infourl = $this->createPluginMobileUrl('supplier/orderj', array('returnurl' => $returnurl));
		$this->model->sendMessage($openid, array('commission' => $commission_ok, 'type' => $apply['type'] == 2 ? '微信' : '线下'), TM_COMMISSION_APPLY);
		show_json(1, '已提交,请等待审核!');
	}
	$returnurl = urlencode($this->createPluginMobileUrl('commission/applyg'));
	$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
	show_json(1, array('commission_ok' => $member['commission_ok'], 'cansettle' => $cansettle, 'member' => $member, 'set' => $this->set, 'infourl' => $infourl, 'noinfo' => empty($member['realname'])));
}
include $this->template('applyg');
