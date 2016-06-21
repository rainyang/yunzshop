<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/4/29
 * Time: 上午10:55
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid = m('user')->getOpenid();
$member         = m('member')->getInfo($openid);
$uniacid    = $_W['uniacid'];

$info = pdo_fetch("SELECT * FROM " . tablename('sz_yi_message') . " WHERE `id` = " . $_GPC['id']);

pdo_update('sz_yi_message', array('status'=>'1'), array('id'=>$_GPC['id']));

include $this->template('member/messageinfo');