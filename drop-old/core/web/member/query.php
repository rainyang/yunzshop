<?php
/*=============================================================================
#     FileName: query.php
#         Desc:  
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:25:08
#      History:
=============================================================================*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$kwd = trim($_GPC['keyword']);
$params = array();
$params[':uniacid'] = $_W['uniacid'];
$condition = " and uniacid=:uniacid";
$op     = $operation = $_GPC['op'] ? $_GPC['op'] : 'query';
if ($op == 'query') {

	if (!empty($kwd)) {
		$condition .= " AND ( `nickname` LIKE :keyword or `realname` LIKE :keyword or `mobile` LIKE :keyword )";
		$params[':keyword'] = "%{$kwd}%";
	}
	$ds = pdo_fetchall('SELECT id,avatar,nickname,openid,realname,mobile FROM ' . tablename('sz_yi_member') . " WHERE 1 {$condition} order by createtime desc", $params);
    //print_r($ds);exit;
	include $this->template('web/member/query');
} else if ($op == 'delbindmobile') {
	/*$member_ids = pdo_fetchall('SELECT id, openid FROM ' . tablename('sz_yi_member') . " WHERE 1 {$condition} order by id asc", $params);
	foreach ($member_ids as $key => $val) {
		$params[':openid'] = $val['openid'];
		$mcount = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('mc_mapping_fans') . " WHERE 1 {$condition} and openid=:openid", $params);
		if($mcount > 1){
			$myid = pdo_fetchcolumn('SELECT fanid FROM ' . tablename('mc_mapping_fans') . " WHERE 1 {$condition} and openid=:openid order by fanid asc", $params);
			$mydels = pdo_fetchall('SELECT uid FROM ' . tablename('mc_mapping_fans') . " WHERE 1 {$condition} and openid=:openid and fanid != ".$myid." order by fanid asc", $params);
			foreach ($mydels as $k => $v) {
				pdo_delete('mc_members', array('uid' => $v['uid']));
			}
			pdo_query('DELETE FROM ' . tablename('mc_mapping_fans') . " WHERE 1 {$condition} and openid=:openid and fanid != ".$myid, $params);
		}

	}*/
	pdo_update('sz_yi_member', array('isbindmobile' => 0), array('uniacid' => $_W['uniacid']));
	message('清除手机绑定记录成功', $this->createWebUrl('sysset', array(
            'op' => 'member'
        )), 'success');
}

