<?php
global $_W, $_GPC;
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$params = array();
	$psize = 20;
	$condition = "";
	if (!empty($_GPC['realname'])) {
		$_GPC['realname'] = trim($_GPC['realname']);
		$condition .= ' and (mc.realname like :realname or mc.mobile like :realname)';
		$params[':realname'] = "%{$_GPC['realname']}%";
	}
	$sql = "SELECT m.avatar, mc.* FROM " . tablename('sz_yi_merchant_center') . " mc LEFT JOIN " . tablename('sz_yi_member') . " m ON mc.openid=m.openid WHERE mc.uniacid={$_W['uniacid']} {$condition} ORDER BY id DESC";
	if (empty($_GPC['export'])) {
		$sql .= '  limit ' . ($pindex - 1) * $psize . ',' . $psize;
	}
	$list = pdo_fetchall($sql, $params);
	foreach ($list as &$value) {
		if (!empty($value['center_id'])) {
			$agent = pdo_fetch("SELECT m.avatar,mc.realname FROM " . tablename('sz_yi_merchant_center') . " mc LEFT JOIN " . tablename('sz_yi_member') . " m ON mc.openid=m.openid WHERE mc.uniacid=:uniacid AND mc.id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $value['center_id']));
			$value['agent_avatar'] = $agent['avatar'];
			$value['agent_realname'] = $agent['realname'];
			$value['merchant_count'] = count($this->model->getCenterMerchants($value['id']));
		}
		if (!empty($value['level_id'])) {
			$value['level'] = pdo_fetch("SELECT * FROM " . tablename('sz_yi_merchant_level') . " WHERE uniacid=:uniacid AND id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $value['level_id']));
		}
		$member_id = pdo_fetchcolumn("SELECT id FROM " . tablename('sz_yi_member') . " WHERE uniacid=:uniacid AND openid=:openid", array(':uniacid' => $_W['uniacid'], ':openid' => $value['openid']));
		$value['commission_total'] = pdo_fetchcolumn("SELECT sum(money) FROM " . tablename('sz_yi_merchant_apply') . " WHERE uniacid=:uniacid AND member_id=:member_id", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
		$value['commission_ok'] = pdo_fetchcolumn("SELECT sum(money) FROM " . tablename('sz_yi_merchant_apply') . " WHERE uniacid=:uniacid AND member_id=:member_id AND status=1", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
	}
	unset($value);
	if ($_GPC['export'] == '1') {
		m('excel')->export($list, array('title' => '招商中心' . '数据-' . date('Y-m-d-H-i', time()), 'columns' => array(array('title' => 'ID', 'field' => 'id', 'width' => 12), array('title' => '粉丝', 'field' => 'nickname', 'width' => 12), array('title' => '姓名', 'field' => 'realname', 'width' => 12), array('title' => '手机号码', 'field' => 'mobile', 'width' => 12), array('title' => '上级', 'field' => 'agent_realname', 'width' => 12), array('title' => '累积佣金', 'field' => 'commission_total', 'width' => 12), array('title' => '打款佣金', 'field' => 'commission_ok', 'width' => 12))));
	}
	$total = count($list);
	$pager = pagination($total, $pindex, $psize);
} else if ($operation == 'add_center_post') {
	$center_id 		= intval($_GPC['center_id']);
	$center_member 	= pdo_fetch("SELECT m.avatar,mc.realname FROM " . tablename('sz_yi_merchant_center') . " mc LEFT JOIN " . tablename('sz_yi_member') . " m ON mc.openid=m.openid WHERE mc.uniacid=:uniacid AND mc.id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $center_id));
	$id 			= intval($_GPC['id']);
	$levels 		= pdo_fetchall("SELECT * FROM " . tablename('sz_yi_merchant_level') . " WHERE uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
	$centerinfo 	= pdo_fetch("SELECT * FROM " . tablename('sz_yi_merchant_center') . " WHERE uniacid=:uniacid AND id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $id));
	if(checksubmit('submit')){
        $data = is_array($_GPC['data']) ? $_GPC['data'] : array();
        if (empty($data['openid'])) {
        	message('请选择微信角色!', $this->createPluginWebUrl('merchant/center'), 'error');
        }
        if (!empty($data['openid'])) {
            $result = pdo_fetch("select * from " . tablename('sz_yi_merchant_center') . " where uniacid={$_W['uniacid']} and openid='{$data['openid']}'");
            if (!empty($result)) {
                if ($result['id'] != $centerinfo['id']) {
                    message('该微信已绑定，请更换!', $this->createPluginWebUrl('merchant/center'), 'error');
                }
            }
        }
        $data['uniacid'] 	= $_W['uniacid'];
        $data['center_id'] 	= $center_id;
        if (empty($id)) {
        	pdo_insert('sz_yi_merchant_center',$data);
        } else {
        	pdo_update('sz_yi_merchant_center', $data, array(
                'id' => $id
            ));
        }
        $url = $this->createPluginWebUrl('merchant/center');
        if (!empty($center_id)) {
        	$url = $this->createPluginWebUrl('merchant/center', array('op' => 'center_centers', 'center_id' => $center_id));
        }
        message('保存成功!', $url, 'success');
    }
} else if ($operation == 'delete') {
	$id = intval($_GPC['id']);
	if (empty($id)) {
		message('该招商中心不存在!', $this->createPluginWebUrl('merchant/center'), 'error');
	} else {
		$center_agents = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_merchant_center') . " WHERE uniacid=:uniacid AND center_id=:center_id", array(':uniacid' => $_W['uniacid'], 'center_id' => $id));
		if (!empty($center_agents)) {
			message('存在下级招商中心，不能删除!', $this->createPluginWebUrl('merchant/center'), 'error');
		} else {
			pdo_delete('sz_yi_merchant_center', array('uniacid' => $_W['uniacid'], 'id' => $id));
			message('删除成功!', $this->createPluginWebUrl('merchant/center'), 'success');
		}
	}
} else if ($operation=='center_centers') {
	$center_id = intval($_GPC['center_id']);
	if (empty($center_id)) {
		message('没有该招商中心！', $this->createPluginWebUrl('merchant/center'), 'error');
	}
	$pindex = max(1, intval($_GPC['page']));
	$params = array();
	$psize = 20;
	$sql = "SELECT m.avatar, mc.* FROM " . tablename('sz_yi_merchant_center') . " mc LEFT JOIN " . tablename('sz_yi_member') . " m ON mc.openid=m.openid WHERE mc.uniacid=:uniacid AND mc.center_id=:id";
	if (empty($_GPC['export'])) {
		$sql .= '  limit ' . ($pindex - 1) * $psize . ',' . $psize;
	}
	$list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid'], ':id' => $center_id));
	foreach ($list as &$value) {
		if (!empty($value['center_id'])) {
			$agent = pdo_fetch("SELECT m.avatar,mc.realname FROM " . tablename('sz_yi_merchant_center') . " mc LEFT JOIN " . tablename('sz_yi_member') . " m ON mc.openid=m.openid WHERE mc.uniacid=:uniacid AND mc.id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $value['center_id']));
			$value['agent_avatar'] = $agent['avatar'];
			$value['agent_realname'] = $agent['realname'];
			$value['merchant_count'] = count($this->model->getCenterMerchants($value['id']));
		}
		if (!empty($value['level_id'])) {
			$value['level'] = pdo_fetch("SELECT * FROM " . tablename('sz_yi_merchant_level') . " WHERE uniacid=:uniacid AND id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $value['level_id']));
		}
		$member_id = pdo_fetchcolumn("SELECT id FROM " . tablename('sz_yi_member') . " WHERE uniacid=:uniacid AND openid=:openid", array(':uniacid' => $_W['uniacid'], ':openid' => $value['openid']));
		$value['commission_total'] = pdo_fetchcolumn("SELECT sum(money) FROM " . tablename('sz_yi_merchant_apply') . " WHERE uniacid=:uniacid AND member_id=:member_id", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
		$value['commission_ok'] = pdo_fetchcolumn("SELECT sum(money) FROM " . tablename('sz_yi_merchant_apply') . " WHERE uniacid=:uniacid AND member_id=:member_id AND status=1", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
	}
	unset($value);
	$total = count($list);
	$pager = pagination($total, $pindex, $psize);
}
load()->func('tpl');
include $this->template('center');
