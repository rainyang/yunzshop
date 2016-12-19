<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
	exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'query';
$openid = m('user')->getOpenid();
if ($operation == 'query') {
    $this->model->getAllCard($openid);
    $sql = 'SELECT * FROM ' . tablename('sz_yi_card_data');
    $sql .= ' WHERE uniacid=:uniacid AND openid=:openid AND balance>0 AND isoverdue=0';
    $list = pdo_fetchall($sql, array(':openid' => $openid, ':uniacid' => $_W['uniacid']));
    foreach ($list as &$row) {
        $row['timestr'] = '永久有效';
        if (empty($row['timelimit'])) {
            if (!empty($row['timedays'])) {
                $row['timestr'] = date('Y-m-d H:i', $row['gettime'] + $row['timedays'] * 86400);
            }
        } else {
            if ($row['timestart'] >= $time) {
                $row['timestr'] = date('Y-m-d H:i', $row['timestart']) . '-' . date('Y-m-d H:i', $row['timeend']);
            } else {
                $row['timestr'] = date('Y-m-d H:i', $row['timeend']);
            }
        }
        if ($row['isday'] == 1) {
            $row['timestr'] = date('Y-m-d H:i',$row['bindtime'] + $row['validity_period']);
        } else if ($row['isday'] == 2) {
            $row['timestr'] = date('Y-m-d H:i',$row['timestart']) . '至' . date('Y-m-d H:i',$row['timeend']);
        }
    }
	unset($row);
	return show_json(1, array('cards' => $list));
} 