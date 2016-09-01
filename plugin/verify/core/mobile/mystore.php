<?php
//芸众商城 QQ:913768135
global $_W, $_GPC;
$mid     = intval($_GPC['mid']);
$openid  = m('user')->getOpenid();
$member  = m('member')->getMember($openid);
$uniacid = $_W['uniacid'];
if(!isMobile() && $this->yzShopSet['ispc']==1){
  $shopurl = $this->createMobileUrl('shop', array('mid' => $mid));
  header('location: ' . $shopurl);
  exit;
}
$id = intval($_GPC{'id'});
$store = pdo_fetch('SELECT * FROM '.tablename('sz_yi_store')." WHERE id=".$id);


include $this->template('mystore_select');

