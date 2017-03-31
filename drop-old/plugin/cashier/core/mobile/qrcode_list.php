<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$member  = m('member')->getMember($openid);

$store = pdo_fetchall('select * from '.tablename('sz_yi_cashier_store').' where member_id='.$member['id'].' and uniacid ='.$_W['uniacid']);
if($store){
	$store=set_medias($store,'thumb');
}else{
	$store = pdo_fetchall('select s.*,sw.member_id as mid from '.tablename('sz_yi_cashier_store_waiter').' sw '.' left join '.tablename('sz_yi_cashier_store').' s on sw.sid = s.id where sw.member_id='.$member['id'].' and s.uniacid ='.$_W['uniacid']);
	
	$store=set_medias($store,'thumb');

}
include $this->template('cashier/qrcode_list');
