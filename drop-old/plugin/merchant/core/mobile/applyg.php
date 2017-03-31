<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();

$set = $this->model->getSet();

if ($_W['isajax']) {
	$iscenter = intval($_GPC['iscenter']);
	$member = m('member')->getMember($openid);
    if (!empty($iscenter)) {
        $center_info = $this->model->getInfo($openid);
        $commission_ok = number_format($center_info['commission_ok'],2);
    } else {
        $uids = $this->model->getChildSupplierUids($openid);
        if ($uids == 0) {
            $cond = " o.supplier_uid < 0 ";
            $conds = " supplier_uid < 0 ";
        } else {
            $cond = " o.supplier_uid in ({$uids}) ";
            $conds = " supplier_uid in ({$uids}) ";
        }
        $commission_ok = pdo_fetchcolumn("SELECT sum(so.money) FROM " . tablename('sz_yi_merchant_order') . " so left join " . tablename('sz_yi_order') . " o on o.id=so.orderid left join " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id WHERE o.uniacid=".$_W['uniacid']." AND {$cond} AND o.merchant_apply_status=0 AND o.status=3 ORDER BY o.createtime DESC,o.status DESC ");
        $my_commissions = pdo_fetchcolumn("SELECT commissions FROM " . tablename('sz_yi_merchants') . " WHERE uniacid=:uniacid AND openid=:openid", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
        $commission_ok = $commission_ok*$my_commissions/100;
    }
	$cansettle = $commission_ok>=1;
    if (!empty($iscenter)) {
        $member['commission_ok'] = number_format($commission_ok, 2);
    } else {
        $uids = $this->model->getChildSupplierUids($openid);
        if ($uids == 0) {
            $cond = " o.supplier_uid < 0 ";
            $conds = " supplier_uid < 0 ";
        } else {
            $cond = " o.supplier_uid in ({$uids}) ";
            $conds = " supplier_uid in ({$uids}) ";
        }
        $commission_ok = pdo_fetchcolumn("SELECT sum(so.money) FROM " . tablename('sz_yi_merchant_order') . " so left join " . tablename('sz_yi_order') . " o on o.id=so.orderid left join " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id WHERE o.uniacid=".$_W['uniacid']." AND {$cond} AND o.merchant_apply_status=0 AND o.status=3 ORDER BY o.createtime DESC,o.status DESC ");
        $my_commissions = pdo_fetchcolumn("SELECT commissions FROM " . tablename('sz_yi_merchants') . " WHERE uniacid=:uniacid AND openid=:openid", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
        $commission_ok = $commission_ok*$my_commissions/100;
        $member['commission_ok']=number_format($commission_ok, 2);
    }
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
		if (empty($iscenter)) {
			$apply['iscenter'] = $iscenter;
            $uids = $this->model->getChildSupplierUids($openid);
            if ($uids == 0) {
                $cond = " o.supplier_uid < 0 ";
                $conds = " supplier_uid < 0 ";
            } else {
                $cond = " o.supplier_uid in ({$uids}) ";
                $conds = " supplier_uid in ({$uids}) ";
            }
            $commission_ok = pdo_fetchcolumn("SELECT sum(so.money) FROM " . tablename('sz_yi_merchant_order') . " so left join " . tablename('sz_yi_order') . " o on o.id=so.orderid left join " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id WHERE o.uniacid=".$_W['uniacid']." AND {$cond} AND o.merchant_apply_status=0 AND o.status=3 ORDER BY o.createtime DESC,o.status DESC ");
            $my_commissions = pdo_fetchcolumn("SELECT commissions FROM " . tablename('sz_yi_merchants') . " WHERE uniacid=:uniacid AND openid=:openid", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
            $commission_ok = $commission_ok*$my_commissions/100;
            $member['commission_ok']=$commission_ok;
            $apply['money'] = $member['commission_ok'];
		}
		pdo_insert('sz_yi_merchant_apply', $apply);
		
		if (!empty($iscenter)) {
			foreach ($center_info['order_ids'] as $value) {
				pdo_update('sz_yi_order', array('center_apply_status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $value['id']));
			}
		} else {
			//$set = $this->model->getSet();
			$apply_cond = "";
			if (!empty($set['apply_day'])) {
				$now_time = time();
				$apply_day = $now_time - $set['apply_day']*60*60*24;
				$apply_cond = " AND finishtime<{$apply_day} ";
			}

			$suppliers = pdo_fetchall("SELECT distinct supplier_uid FROM " . tablename('sz_yi_merchants') . " WHERE uniacid=:uniacid AND openid=:openid", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			$uids = array();
			foreach ($suppliers as $value) {
				$uids[] = $value['supplier_uid'];
			}
			if (!empty($uids)) {
				$uids = implode(',', $uids);
			}

			$orderids = pdo_fetchall("select id from " . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and supplier_uid in ({$uids}) and status = 3 and userdeleted = 0 and deleted = 0 and merchant_apply_status = 0 {$apply_cond} ");
			foreach ($orderids as $key => $value) {
				pdo_update('sz_yi_order', array('merchant_apply_status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $value['id']));
			}
		}
		return show_json(1, '已提交,请等待审核!');
	}
	$returnurl = urlencode($this->createPluginMobileUrl('commission/applyg'));
	$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
	return show_json(1, array('commission_ok' => $member['commission_ok'], 'cansettle' => $cansettle, 'member' => $member, 'set' => $this->set, 'infourl' => $infourl, 'noinfo' => empty($member['realname']), 'center_info' => $center_info));
}
include $this->template('applyg');
