<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid     = m('user')->getOpenid();
$uniacid    = $_W['uniacid'];
$set        = m('common')->getSysset('shop');
$commission = p('commission');
if ($commission) {
	$shopid = intval($_GPC['shopid']);
	if (!empty($shopid)) {
		$myshop = set_medias($commission->getShop($shopid), array('img', 'logo'));
	}
}
$current_category = false;
if (!empty($_GPC['tcate'])) {
	$current_category = pdo_fetch('select id,parentid,name,level from ' . tablename('sz_yi_category') . ' where id=:id and uniacid=:uniacid order by displayorder DESC', array(':id' => intval($_GPC['tcate']), ':uniacid' => $_W['uniacid']));
} else if (!empty($_GPC['ccate'])) {
	$current_category = pdo_fetch('select id,parentid,name,level from ' . tablename('sz_yi_category') . ' where id=:id and uniacid=:uniacid order by displayorder DESC', array(':id' => intval($_GPC['ccate']), ':uniacid' => $_W['uniacid']));
} else if (!empty($_GPC['pcate'])) {
	$current_category = pdo_fetch('select id,parentid,name,level from ' . tablename('sz_yi_category') . ' where id=:id and uniacid=:uniacid order by displayorder DESC', array(':id' => intval($_GPC['pcate']), ':uniacid' => $_W['uniacid']));
}
$parent_category = pdo_fetch('select id,parentid,name,level from ' . tablename('sz_yi_category') . ' where id=:id  and uniacid=:uniacid limit 1', array(':id' => $current_category['parentid'], ':uniacid' => $_W['uniacid']));
if ($_W['isajax']) {
	$args = array('pagesize' => 10, 'page' => $_GPC['page'], 'isnew' => $_GPC['isnew'], 'ishot' => $_GPC['ishot'], 'isrecommand' => $_GPC['isrecommand'], 'isdiscount' => $_GPC['isdiscount'], 'istime' => $_GPC['istime'], 'keywords' => $_GPC['keywords'], 'pcate' => $_GPC['pcate'], 'ccate' => $_GPC['ccate'], 'tcate' => $_GPC['tcate'], 'order' => $_GPC['order'], 'by' => $_GPC['by']);
	if (!empty($myshop['selectgoods']) && !empty($myshop['goodsids'])) {
		$args['ids'] = $myshop['goodsids'];
	}
	$goods = m('goods')->getList($args);
	$category = false;
	if (intval($_GPC['page']) <= 1) {
		if (!empty($_GPC['tcate'])) {
			$parent_category1 = pdo_fetch('select id,parentid,name,level,thumb from ' . tablename('sz_yi_category') . ' where id=:id  and uniacid=:uniacid limit 1', array(':id' => $parent_category['parentid'], ':uniacid' => $_W['uniacid']));
			$category = pdo_fetchall('select id,name,level,thumb from ' . tablename('sz_yi_category') . ' where parentid=:parentid and enabled=1 and uniacid=:uniacid order by level asc, isrecommand desc, displayorder DESC', array(':parentid' => $parent_category['id'], ':uniacid' => $_W['uniacid']));
			$category = array_merge(array(array('id' => 0, 'name' => '全部分类', 'level' => 0), $parent_category1, $parent_category,), $category);
		} else if (!empty($_GPC['ccate'])) {
			if (intval($set['catlevel']) == 3) {
				$category = pdo_fetchall('select id,name,level,thumb from ' . tablename('sz_yi_category') . ' where (parentid=:parentid or id=:parentid) and enabled=1  and uniacid=:uniacid order by level asc, isrecommand desc, displayorder DESC', array(':parentid' => intval($_GPC['ccate']), ':uniacid' => $_W['uniacid']));
			} else {
				$category = pdo_fetchall('select id,name,level,thumb from ' . tablename('sz_yi_category') . ' where parentid=:parentid and enabled=1 and uniacid=:uniacid order by level asc, isrecommand desc, displayorder DESC', array(':parentid' => $parent_category['id'], ':uniacid' => $_W['uniacid']));
			}
			$category = array_merge(array(array('id' => 0, 'name' => '全部分类', 'level' => 0), $parent_category,), $category);
		} else if (!empty($_GPC['pcate'])) {
			$category = pdo_fetchall('select id,name,level,thumb from ' . tablename('sz_yi_category') . ' where (parentid=:parentid or id=:parentid) and enabled=1 and uniacid=:uniacid order by level asc, isrecommand desc, displayorder DESC', array(':parentid' => intval($_GPC['pcate']), ':uniacid' => $_W['uniacid']));
			$category = array_merge(array(array('id' => 0, 'name' => '全部分类', 'level' => 0)), $category);
		} else {
			$category = pdo_fetchall('select id,name,level,thumb from ' . tablename('sz_yi_category') . ' where parentid=0 and enabled=1 and uniacid=:uniacid order by displayorder DESC', array(':uniacid' => $_W['uniacid']));
		}
		foreach ($category as &$c) {
			$c['thumb'] = tomedia($c['thumb']);
			if ($current_category['id'] == $c['id']) {
				$c['on'] = true;
			}
		}
		unset($c);
	}
	show_json(1, array('goods' => $goods, 'pagesize' => $args['pagesize'], 'category' => $category, 'current_category' => $current_category));
}
$this->setHeader();
include $this->template('shop/list');
