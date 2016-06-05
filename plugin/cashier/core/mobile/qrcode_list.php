<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$member  = m('member')->getMember($openid);

$store = pdo_fetchall('select * from '.tablename('sz_yi_cashier_store').' where member_id='.$member['id']);  

include $this->template('cashier/qrcode_list');
