<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$pageid = intval($_GPC['pageid']);
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid     = m('user')->getOpenid();
$uniacid    = $_W['uniacid'];
$shopset    = set_medias(m('common')->getSysset(array('share','shop')), 'catadvimg');
$commission = p('commission');
$member = m('member')->getInfo($openid);
if ($commission) {
	$shopid = intval($_GPC['shopid']);
	$shop = set_medias($commission->getShop($openid), array('img', 'logo'));
}
$color=pdo_fetch('select color from ' .tablename('sz_yi_chooseagent'). ' where id='.$pageid);
$_W['shopshare'] = array(
    'title' => !empty($shopset['share']) ? $shopset['share'] : $shopset['name'],
    'imgUrl' => !empty($shopset['icon']) ? tomedia($shopset['icon']) : tomedia($shopset['logo']),
    'desc' => !empty($shopset['desc']) ? $shopset['desc'] : $shopset['name'],
    'link' => $this->createPluginMobileUrl('choose', array('pageid' => $pageid, 'mid' => $member['id']))
);
$this->setHeader();
include $this->template('index');
