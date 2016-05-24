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
            $userdata = $_GPC['userdata'];
            $member = array();
			$username = trim($userdata['username']);
			if(empty($username)) {
				show_json(0);
			}
			$member['username'] = $username;
			$member['password'] = $userdata['password'];
			if(empty($member['password'])) {
				show_json(0);
			}
			$record = user_single($member);
			$record['uniacid'] = pdo_fetchcolumn("select uniacid from " . tablename('sz_yi_perm_user') . " where uid={$record['uid']}");
			$record['supplier_status'] = pdo_fetchcolumn("select status1 from " . tablename('sz_yi_perm_role') . " where id=(select roleid from " . tablename('sz_yi_perm_user') . " where uid={$record['uid']})");
			if(!empty($record)) {
				if($record['status'] == 1) {
					show_json(0);
				}
				$founders = explode(',', $_W['config']['setting']['founder']);
				$_W['isfounder'] = in_array($record['uid'], $founders);
				if (!empty($_W['siteclose']) && empty($_W['isfounder'])) {
					show_json(0);
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
					show_json(1, array(
	                    'preurl' => $preUrl
	                ));
				} else {
					show_json(0);
				}
			} else {
				show_json(0);
			} 
        }
    }
}
include $this->template('shop/login');
