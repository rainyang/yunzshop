<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid     = m('user')->getOpenid();
$uniacid    = $_W['uniacid'];
$set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));
$commission = p('commission');
$shopset   = m('common')->getSysset('shop');
if ($commission) {
    $shopid = intval($_GPC['shopid']);
    if (!empty($shopid)) {
        $myshop = set_medias($commission->getShop($shopid), array(
            'img',
            'logo'
        ));
    }
}
$category = set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_category')." where parentid=0 and enabled=1 and uniacid=".$_W['uniacid']),'advimg');

foreach ($category as $key => $value) {
    $children = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_category')." where parentid=:pid and uniacid=:uniacid and enabled=1",array(':pid' => $value['id'],':uniacid' => $_W["uniacid"])),'advimg');
    foreach($children as $key1 => $value1){
        $category[$key]['children'][$key1] = $value1;
        $third = set_medias(pdo_fetchall(" select  * from ".tablename('sz_yi_category')." where parentid=:pid and uniacid=:uniacid and enabled=1",array(':pid' => $value1['id'] , ':uniacid' => $_W["uniacid"])),'advimg');
        foreach($third as $key2 => $value2){
            $category[$key]['children'][$key1]['third'][$key2] = $value2;
        }
    }
}

if ($_W['isajax']) {
    return show_json(1, array(
        
        'category' => $category
        
    ));
}
include $this->template('shop/alllist');
