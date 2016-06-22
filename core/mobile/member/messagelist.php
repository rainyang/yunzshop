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

$list = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_message') . " WHERE `openid` = '" . $openid . "' ORDER BY `id` DESC");

if ($_W['isajax']) {
    show_json(1, array(
        'list' => $list
    ));
}
if($operation == 'message_read'){
  foreach ($list as $key => $value) {
      pdo_update('sz_yi_message', array('status'=>'1'), array('id'=>$value['id']));

  }
}
include $this->template('member/messagelist');