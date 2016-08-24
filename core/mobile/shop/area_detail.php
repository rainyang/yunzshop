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
        $times = $category['times'] + 1;
        pdo_update('sz_yi_category_area', array('times' => $times), array('id' => $category['id'], 'uniacid' => $_W['uniacid']));
       
    } else if (!empty($_GPC['ccate_area'])) {
        $category = set_medias(pdo_fetch('select * from ' . tablename('sz_yi_category_area') . ' where id=:id 
            and uniacid=:uniacid order by displayorder DESC', array(
            ':id' => intval($_GPC['ccate_area']),
            ':uniacid' => $_W['uniacid']
        )),'thumb');
        $times = $category['times'] + 1;
        pdo_update('sz_yi_category_area', array('times' => $times), array('id' => $category['id'], 'uniacid' => $_W['uniacid']));


    }
    
}    
include $this->template('shop/area_detail');
