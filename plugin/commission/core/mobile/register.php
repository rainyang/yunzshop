<?php
/*=============================================================================
#     FileName: register.php
#         Desc:  
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-13 08:34:30
#      History:
=============================================================================*/
global $_W, $_GPC;
$openid   = m('user')->getOpenid();
$shop_set = m('common')->getSysset('shop');
$set = set_medias($this->set, 'regbg');
$member = m('member')->getMember($openid);
if ($member['isagent'] == 1 && $member['status'] == 1) {
	header('location: ' . $this->createPluginMobileUrl('commission'));
	exit;
}
if (empty($set['become'])) {
}
$mid = intval($_GPC['mid']);
if ($_W['isajax']) {
	$agent = false;
	if (!empty($member['fixagentid'])) {
		$mid = $member['agentid'];
		if (!empty($mid)) {
			$agent = m('member')->getMember($member['agentid']);
		}
	} else {
		if (!empty($member['agentid'])) {
			$mid = $member['agentid'];
			$agent = m('member')->getMember($member['agentid']);
		} else if (!empty($member['inviter'])) {
			$mid = $member['inviter'];
			$agent = m('member')->getMember($member['inviter']);
		} else if (!empty($mid)) {
			$agent = m('member')->getMember($mid);
		}
	}
	$ret = array('shop_set' => $shop_set, 'set' => $set, 'member' => $member, 'agent' => $agent);
	$ret['status'] = 0;
	$status = intval($set['become_order']) == 0 ? 1 : 3;
    //print_r($set);exit;
	if (empty($set['become'])) {
		$become_reg = intval($set['become_reg']);
		if (empty($become_reg)) {
			$become_check = intval($set['become_check']);
			$ret['status'] = $become_check;
			$data = array('isagent' => 1, 'agentid' => $mid, 'status' => $become_check, 'realname' => $_GPC['realname'], 'mobile' => $_GPC['mobile'], 'weixin' => $_GPC['weixin'], 'agenttime' => $become_check == 1 ? time() : 0);
			pdo_update('sz_yi_member', $data, array('id' => $member['id']));
			if ($become_check == 1) {
				$this->model->sendMessage($member['openid'], array('agenttime' => $data['agenttime']), TM_COMMISSION_BECOME);
				$this->model->upgradeLevelByAgent($member['id']);
			}
			if (!empty($member['uid'])) {
				load()->model('mc');
				mc_update($member['uid'], array('realname' => $data['realname'], 'mobile' => $data['mobile']));
			}
		}
	} else if ($set['become'] == '2') {
		$ordercount = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order') . " where uniacid=:uniacid and openid=:openid and status>={$status} limit 1", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		if ($ordercount < intval($set['become_ordercount'])) {
			$ret['status'] = 1;
			$ret['order'] = number_format($ordercount, 0);
			$ret['ordercount'] = number_format($set['become_ordercount'], 0);
		}
	} else if ($set['become'] == '3') {
		$moneycount = pdo_fetchcolumn('select sum(goodsprice) from ' . tablename('sz_yi_order') . " where uniacid=:uniacid and openid=:openid and status>={$status} limit 1", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		if ($moneycount < floatval($set['become_moneycount'])) {
			$ret['status'] = 2;
			$ret['money'] = number_format($moneycount, 2);
			$ret['moneycount'] = number_format($set['become_moneycount'], 2);
		}
	}
	if ($_W['ispost']) {
		if ($member['isagent'] == 1 && $member['status'] == 1) {
			show_json(0, '您已经是' . $set['texts']['become'] . '，无需再次申请!');
		}
		if ($ret['status'] == 1 || $ret['status'] == 2) {
			show_json(0, '您消费的还不够哦，无法申请' . $set['texts']['become'] . '!');
		} else {
			$become_check = intval($set['become_check']);
			$ret['status'] = $become_check;
			$data = array('isagent' => 1, 'agentid' => $mid, 'status' => $become_check, 'realname' => $_GPC['realname'], 'mobile' => $_GPC['mobile'], 'weixin' => $_GPC['weixin'], 'agenttime' => $become_check == 1 ? time() : 0);
			pdo_update('sz_yi_member', $data, array('id' => $member['id']));
			if ($become_check == 1) {
				$this->model->sendMessage($member['openid'], array('agenttime' => $data['agenttime']), TM_COMMISSION_BECOME);
				if (!empty($mid)) {
					$this->model->upgradeLevelByAgent($mid);
				}
			}
			if (!empty($member['uid'])) {
				load()->model('mc');
				mc_update($member['uid'], array('realname' => $data['realname'], 'mobile' => $data['mobile']));
				show_json(1, $ret);
			}
		}
	}
	show_json(1, $ret);
}
$this->setHeader();
include $this->template('register');
