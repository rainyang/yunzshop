<?php
if (!defined('IN_IA')) {
	die('Access Denied');
}
global $_W, $_GPC;
$op     = $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
if ($op == 'display') {
		$sql = 'SELECT * FROM ' . tablename('sz_yi_category') . ' WHERE `uniacid` = :uniacid ORDER BY `parentid`, `displayorder` DESC';
		$category = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']), 'id');
		$parent = $children = array();
		if (!empty($category)) {
			foreach ($category as $cid => $cate) {
				if (!empty($cate['parentid'])) {
					$children[$cate['parentid']][] = $cate;
				} else {
					$parent[$cate['id']] = $cate;
				}
			}
		}
		$shopset = m('common')->getSysset('shop');
} else if ($op == 'fetch') {
		set_time_limit(0);
		$ret = array();
		$url = $_GPC['url'];
		$pcate = intval($_GPC['pcate']);
		$ccate = intval($_GPC['ccate']);
		$tcate = intval($_GPC['tcate']);
		if (is_numeric($url)) {
			$itemid = $url;
		} else {
			preg_match('/(\\d+).html/i', $url, $matches);
			if (isset($matches[1])) {
				$itemid = $matches[1];
			}
		}
		if (empty($itemid)) {
			die(json_encode(array('result' => 0, 'error' => '未获取到 itemid!')));
		}
		$ret = $this->model->get_item_one688($itemid, $_GPC['url'], $pcate, $ccate, $tcate);
		plog('taobao.one688', '1688抓取宝贝 1688id:' . $itemid);
		die(json_encode($ret));
}
load()->func('tpl');
include $this->template('one688');