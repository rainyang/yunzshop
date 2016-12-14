<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
load()->model('user');
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
    if ($_W['isajax']) {
        if ($_W['ispost']) {
        	if (!p('supplier')) {
        		return show_json(3);
        	}
            $userdata = $_GPC['userdata'];
            $member = array();
			$username = trim($userdata['username']);
			if(empty($username)) {
				return show_json(0);
			}
			$member['username'] = $username;
			$member['password'] = $userdata['password'];
			if(empty($member['password'])) {
				return show_json(0);
			}
			$record = user_single($member);
			if(empty($record['uid'])) {
				return show_json(0);
			}

			$perm_user = pdo_fetch("select roleid,uniacid from " . tablename('sz_yi_perm_user') . " where uid={$record['uid']}");
			if(empty($perm_user['roleid'])) {
				return show_json(0);
			}
			$record['uniacid'] = $perm_user['uniacid'];
			$record['supplier_status'] = pdo_fetchcolumn("select status1 from " . tablename('sz_yi_perm_role') . " where id=".$perm_user['roleid']);
			if(!empty($record)) {
				if($record['status'] == 1) {
					return show_json(0);
				}
				$founders = explode(',', $_W['config']['setting']['founder']);
				$_W['isfounder'] = in_array($record['uid'], $founders);
				if (!empty($_W['siteclose']) && empty($_W['isfounder'])) {
					return show_json(0);
				}
				$cookie = array();
				$cookie['uid'] = $record['uid'];
				$cookie['lastvisit'] = $record['lastvisit'];
				$cookie['lastip'] = $record['lastip'];
				$cookie['hash'] = md5($record['password'] . $record['salt']);
				$session = base64_encode(json_encode($cookie));
				isetcookie('__session', $session, !empty($_GPC['rember']) ? 7 * 86400 : 0);
				$status = array();
				$status['uid'] = $record['uid'];
				$_W['uid'] = $record['uid'];
				$status['lastip'] = CLIENT_IP;
				user_update($status);
				if(empty($userdata)) {
					$userdata = $_GPC['userdata'];
				}
				if(empty($userdata)) {
					$userdata = './index.php?c=account&a=display';
				}
				if ($record['uid'] != $_GPC['__uid']) {
					isetcookie('__uniacid', '', -7 * 86400);
					isetcookie('__uid', '', -7 * 86400);
				}
				pdo_delete('users_failed_login', array('id' => $failed['id']));
				if($record['supplier_status'] == 1) {
					//$preUrl = $_W['siteroot']."/web/index.php?c=account&a=switch&uniacid={$record['uniacid']}";
					$preUrl = $this->createWebUrl('order/list');
					return show_json(1, array(
	                    'preurl' => $preUrl
	                ));
				} else {
					return show_json(0);
				}
			} else {
				return show_json(0);
			} 
        }
    }
}
include $this->template('shop/login');
