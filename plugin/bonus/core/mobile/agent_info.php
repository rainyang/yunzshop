<?php
global $_W, $_GPC;
$openid   = m('user')->getOpenid();
$member = $this->model->getInfo($openid, array());
if(!empty($member['check_imgs'])){
	$check_imgs = unserialize($member['check_imgs']);
}
if ($_W['ispost']) {
	$imgdata = serialize($_GPC['imgs']);
	pdo_update('sz_yi_member', array('check_imgs' => $imgdata), array('id' => $member['id']));
	$comfirm_show = 1;
}
include $this->template('agent_info');