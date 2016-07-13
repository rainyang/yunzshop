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
    'title' => !empty($shopset['share']["title"]) ? $shopset['share']["title"] : $shopset['shop']['name'],
    'imgUrl' => !empty($shopset['share']['icon']) ? tomedia($shopset['share']['icon']) : tomedia($shopset['shop']['logo']),
    'desc' => !empty($shopset['share']['desc']) ? $shopset['share']['desc'] : $shopset['shop']['name'],
    'link' => $this->createPluginMobileUrl('choose', array('pageid' => $pageid, 'mid' => $member['id']))
);
$this->setHeader();
include $this->template('index');
