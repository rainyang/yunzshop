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

$list = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_push') . "  ORDER BY `id` DESC");
foreach ($list as $key => $value) {
	$list[$key]['time'] = date('Y-m-d',$value['time']);
}
if ($_W['isajax']) {
    return show_json(1, array(
        'list' => $list
    ));
}
// if($operation == 'push_read'){
//   foreach ($list as $key => $value) {
//       pdo_update('sz_yi_push', array('status'=>'1'), array('id'=>$value['id']));

//   }
// }
include $this->template('member/pushlist');