<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

ca('statistics.view.goods_rank');
$shopset = m('common')->getSysset('shop');
$sql = 'SELECT * FROM ' . tablename('sz_yi_category') . ' WHERE `uniacid` = :uniacid ORDER BY `parentid`, `displayorder` DESC';
$category = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']), 'id');
$parent = $children = array();
$id = intval($_GPC['id']);
$member = $this->model->getInfo($id, array());
if (!empty($category)) {
    foreach ($category as $cid => $cate) {
        if (!empty($cate['parentid'])) {
            $children[$cate['parentid']][] = $cate;
        } else {
            $parent[$cate['id']] = $cate;
        }
    }
}
$pindex    = max(1, intval($_GPC['page']));
$psize     = 20;
$params    = array();
$condition = " and og.uniacid={$_W['uniacid']} ";
if (empty($starttime) || empty($endtime)) {
    $starttime = strtotime('-1 month');
    $endtime   = time();
}
if (!empty($_GPC['datetime'])) {
	$starttime = strtotime($_GPC['datetime']['start']);
	$endtime = strtotime($_GPC['datetime']['end']);
	if (!empty($_GPC['searchtime'])) {
		$condition .= " AND o.createtime >={$starttime} AND o.createtime <= {$endtime} ";
	}
}
$condition1 = ' and g.uniacid=:uniacid';
if (!empty($_GPC['category']['thirdid'])) {
	$tcate = intval($_GPC['category']['thirdid']);
    $condition1 .= " AND g.tcate = {$tcate}";
}
if (!empty($_GPC['category']['childid'])) {
	$ccate = intval($_GPC['category']['childid']);
    $condition1 .= " AND g.ccate = {$ccate}";
}
if (!empty($_GPC['category']['parentid'])) {
	$pcate = intval($_GPC['category']['parentid']);
    $condition1 .= " AND g.pcate = {$pcate}";
}
$params1 = array(':uniacid' => $_W['uniacid']);
if (!empty($_GPC['title'])) {
	$condition1 .= ' and g.title like :title';
	$params1[':title'] = "%{$_GPC['title']}%";
}
$orderby = !isset($_GPC['orderby']) ? 'money' : (empty($_GPC['orderby']) ? 'money' : 'count');
if(!empty($id)){
	$sql = "select distinct orderid from " . tablename('sz_yi_bonus_goods') . " where mid=:mid ORDER BY id DESC";
	$bonusoderids = pdo_fetchall($sql,array(':mid'=>$id), 'orderid');
	$inorderids = "";

	if(!empty($bonusoderids)){
	    $condition .= ' and  o.id in('.implode(',', array_keys($bonusoderids)).')';
	}else{
	    $condition .= ' and  o.id=0';
	}
}

$sql = 'SELECT g.id,g.title,g.thumb,' . '(select ifnull(sum(og.price),0) from  ' . tablename('sz_yi_order_goods') . ' og left join ' . tablename('sz_yi_order') . " o on og.orderid=o.id  where o.status>=1 and og.goodsid=g.id {$condition})  as money," . '(select ifnull(sum(og.total),0) from  ' . tablename('sz_yi_order_goods') . ' og left join ' . tablename('sz_yi_order') . " o on og.orderid=o.id  where o.status>=1 and og.goodsid=g.id {$condition}) as count  " . 'from ' . tablename('sz_yi_goods') . ' g  ' . "where 1 {$condition1}  order by {$orderby} desc ";
if (empty($_GPC['export'])) {
	$sql .= 'LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
}

$list = pdo_fetchall($sql, $params1);
$total = pdo_fetchcolumn('select  count(*) from ' . tablename('sz_yi_goods') . ' g ' . " where 1 {$condition1} ", $params1);
$pager = pagination($total, $pindex, $psize);
load()->func('tpl');
include $this->template('goods_rank');
exit;
