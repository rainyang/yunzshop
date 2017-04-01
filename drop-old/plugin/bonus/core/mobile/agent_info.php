<?php
global $_W, $_GPC;
$openid   = m('user')->getOpenid();
$member = $this->model->getInfo($openid, array());
if(empty($member['isagency'])){
	$url = $this->createPluginMobileUrl('bonus/agency', array("returnurl" => urlencode($this->createPluginMobileUrl('bonus/agent_info'))));
	header("location:". $url);
}
if(!empty($member['check_imgs'])){
	$check_imgs = set_medias(unserialize($member['check_imgs']));
}
if ($_W['ispost']) {
	$imgdata = serialize($_GPC['imgs']);

	pdo_update('sz_yi_member', array('check_imgs' => $imgdata, "bonus_status" => 9), array('id' => $member['id']));
	$comfirm_show = 1;
}
include $this->template('agent_info');