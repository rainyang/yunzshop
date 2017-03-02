<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid);
load()->func('tpl');
$article_sys = pdo_fetch("select * from" . tablename('sz_yi_article_sys') . "where uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
$article_sys['article_image'] = tomedia($article_sys['article_image']);

$helper_category = pdo_fetchall("SELECT id,category_name FROM " .tablename('sz_yi_article_category'). " WHERE uniacid=:uniacid and is_helper=1", array(':uniacid' => $_W['uniacid']));
if ($_W['isajax']) {
	$id = $_GPC['id'];
	$helpers = pdo_fetchall("SELECT * FROM ".tablename('sz_yi_article'). " WHERE article_category=:id and uniacid=:uniacid and is_helper=1 limit 8", array(':id' => $id, ':uniacid' => $_W['uniacid']));
	 show_json(1, array('helpers' => $helpers));
}
include $this->template('list_pc');
