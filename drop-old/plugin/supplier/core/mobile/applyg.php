<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$shopset   = m('common')->getSysset('pay');
$set = $this->model->getSet();

if ($_W['isajax']) {
	$member = m('member')->getMember($openid);
	$supplieruser = $this->model->getSupplierUidAndUsername($openid);
	$uid = $supplieruser['uid'];
	$supplierinfo = $this->model->getSupplierInfo($uid);
	$costmoney = number_format($supplierinfo['costmoney'], 2);
	$cansettle = $costmoney >= 1;
	$member['commission_ok'] = $costmoney;
	if ($_W['ispost']) {
		$supplierinfo = $this->model->getSupplierInfo($uid);
		$costmoney = $supplierinfo['costmoney'];
		$time = time();
		$applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');
		$apply_ordergoods_ids = "";
        if (!empty($supplierinfo['sp_goods'])) {
            foreach ($supplierinfo['sp_goods'] as $key => $value) {
                if ($key == 0) {
                    $apply_ordergoods_ids .= $value['ogid'];
                } else {
                    $apply_ordergoods_ids .= ','.$value['ogid'];
                }
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
			foreach ($supplierinfo['sp_goods'] as $key => $value) {
				pdo_update('sz_yi_order_goods', array('supplier_apply_status' => 2), array('id' => $value['ogid'], 'uniacid' => $_W['uniacid']));

			}
			$tmp_sp_goods = $supplierinfo['sp_goods'];
			$tmp_sp_goods['applyno'] = $applyno;
			@file_put_contents(IA_ROOT . "/addons/sz_yi/data/sp_goods.log", print_r($tmp_sp_goods, 1), FILE_APPEND);
		}

		$returnurl = urlencode($this->createPluginMobileUrl('supplier/orderj'));
		$infourl = $this->createPluginMobileUrl('supplier/orderj', array('returnurl' => $returnurl));
		$this->model->sendMessage($openid, array('commission' => $commission_ok, 'type' => $apply['type'] == 2 ? '微信' : '线下'), TM_COMMISSION_APPLY);
		return show_json(1, '已提交,请等待审核!');
	}
	$closetocredit = $this->set['closetocredit'];
	$returnurl = urlencode($this->createPluginMobileUrl('commission/applyg'));
	$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
	return show_json(1, array('commission_ok' => $member['commission_ok'], 'cansettle' => $cansettle, 'member' => $member, 'set' => $this->set, 'infourl' => $infourl, 'noinfo' => empty($member['realname']), 'supplierinfo' => $supplierinfo, 'closetocredit' => $closetocredit, 'shopset' => $shopset));
}
include $this->template('applyg');
