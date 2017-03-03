<?php
//芸众商城 QQ:913768135
global $_W, $_GPC;
$mid     = intval($_GPC['mid']);
$openid  = m('user')->getOpenid();
$member  = m('member')->getMember($openid);
$uniacid = $_W['uniacid'];
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if(!isMobile() && $this->yzShopSet['ispc']==1){
  $shopurl = $this->createMobileUrl('shop', array('mid' => $mid));
  header('location: ' . $shopurl);
  exit;
}
$shopset    = set_medias(m('common')->getSysset(array('share','shop')), 'catadvimg');
$id = intval($_GPC{'storeid'});
$store = pdo_fetch('SELECT * FROM '.tablename('sz_yi_store')." WHERE id=".$id);
if ($operation == 'reset') {
    $id = intval($_GPC['storeid']);
    if (empty($id)) {
        return show_json(0);
    }
    pdo_delete('sz_yi_store_goods', array('storeid' => $id));
    return show_json(1);

}

include $this->template('mystore_select');

