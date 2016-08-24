<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid     = m('user')->getOpenid();
$uniacid    = $_W['uniacid'];
$set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));
$commission = p('commission');
$shopset   = m('common')->getSysset('shop');
if ($operation == 'display') {
    if (!empty($_GPC['tcate_area'])) {
        $category = set_medias(pdo_fetch('select * from ' . tablename('sz_yi_category_area') . ' where id=:id 
            and uniacid=:uniacid order by displayorder DESC', array(
            ':id' => intval($_GPC['tcate_area']), ':uniacid' => $_W['uniacid']
        )),'thumb');
        $goods = set_medias(pdo_fetchall("SELECT * FROM ".tablename('sz_yi_goods')." WHERE tcate_area=:id and status=1 and deleted=0 and uniacid=:uniacid ",array(':id' => intval($_GPC['tcate_area']),':uniacid' => $_W['uniacid'])),'thumb');
        $total = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('sz_yi_goods')." WHERE tcate_area=:id and status=1 and deleted=0 and uniacid=:uniacid ",array(':id' => intval($_GPC['tcate_area']),':uniacid' => $_W['uniacid']));
        $category['url'] = $this->createMobileUrl('shop/area_detail')."&tcate_area=";
    } else if (!empty($_GPC['ccate_area'])) {
        $category = set_medias(pdo_fetch('select * from ' . tablename('sz_yi_category_area') . ' where id=:id 
            and uniacid=:uniacid order by displayorder DESC', array(
            ':id' => intval($_GPC['ccate_area']),
            ':uniacid' => $_W['uniacid']
        )),'thumb');
        $goods = set_medias(pdo_fetchall("SELECT * FROM ".tablename('sz_yi_goods')." WHERE ccate_area=:id and status=1 and deleted=0 and uniacid=:uniacid ",array(':id' => intval($_GPC['ccate_area']),':uniacid' => $_W['uniacid'])),'thumb');
        $total = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('sz_yi_goods')." WHERE ccate_area=:id and status=1 and deleted=0 and uniacid=:uniacid ",array(':id' => intval($_GPC['ccate_area']),':uniacid' => $_W['uniacid']));
        $category['url'] = $this->createMobileUrl('shop/area_detail')."&ccate_area=";


    }
    $args['pagesize'] = 20;
    $pindex = max(1, intval($_GPC['page']));
    $pager = pagination($total, $pindex, $args['pagesize']);
}    
include $this->template('shop/area');
