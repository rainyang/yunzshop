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

$info = pdo_fetch("SELECT * FROM " . tablename('sz_yi_push') . " WHERE `id` = " . $_GPC['id']);
$info['time'] =  date('Y-m-d',$info['time']);
$info['content'] = html_entity_decode($info['content']);
pdo_update('sz_yi_push', array('status'=>'1'), array('id'=>$_GPC['id']));

include $this->template('member/pushinfo');