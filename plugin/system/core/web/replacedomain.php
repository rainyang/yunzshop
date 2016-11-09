<?php
global $_W, $_GPC;
if (!$_W['isfounder']) {
    message('无权访问!');
}
ca('system.replacedomain');
if(!empty($_GPC["submit"])){
	$oldReplaceDomain = $_GPC["oldReplaceDomain"];
	$newReplaceDomain = $_GPC["newReplaceDomain"];
	if (empty($oldReplaceDomain)) {
		message('请填写旧域名!', '', 'warning');
	}
	if (empty($newReplaceDomain)) {
		message('请填写新域名!', '', 'warning');
	}
	$replaceData = array(
		'core_cache'			=> array("value"),
		'core_settings'			=> array("value"),
		'cover_reply'			=> array("url"),
		'mc_members'			=> array("avatar"),
		'modules'				=> array("url"),
		'site_templates'		=> array("url"),
		'stat_msg_history'		=> array("message"),
		'sz_yi_article'			=> array("resp_img"),
		'sz_yi_designer_menu'	=> array("menus"),
		'sz_yi_goods'			=> array("content"),
		'sz_yi_member'			=> array("avatar"),
		'sz_yi_order_comment'	=> array("headimgurl"),
		'sz_yi_poster_qr'		=> array("qrimg"),
		'sz_yi_coupon'		    => array("respurl"),
        'sz_yi_adv'		        => array("link"),
        'sz_yi_adpc'		    => array("link"),
        'sz_yi_notice'		    => array("link"),
	);
	if($oldReplaceDomain == $newReplaceDomain){
		message('域名不能相同!', '', 'warning');
	}
	//域名格式判断
	$preg = '/^(http:\/\/)?(https:\/\/)?'.
			'(([0-9]{1,3}\.){3}[0-9]{1,3}'. 
            '|'. // 允许IP和DOMAIN（域名）  
            '([0-9a-z_!~*\'()-]+\.)*'. // 三级域验证- www.  
            '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. // 二级域验证  
            '[a-z]{2,6})'.  // 顶级域验证.com or .museum  
            '(:[0-9]{1,4})?'.  // 端口- :80  
            '((\/\?)|'.  // 如果含有文件对文件部分进行校验  
            '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/';

	$preg_ip = '((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?[1-9])))(\:\d)*)';
	if(!(preg_match($preg, $oldReplaceDomain)) && !(preg_match($preg_ip, $oldReplaceDomain))){
		message('旧域名格式不正确!', '', 'warning');
	}
	if(!(preg_match($preg, $newReplaceDomain)) && !(preg_match($preg_ip, $newReplaceDomain))){
		message('新域名格式不正确!', '', 'warning');
	}

	foreach($replaceData as  $key => $value){
		foreach($value as $k => $v){
			$result = pdo_fetch("update " . tablename($key) . " set $v = REPLACE($v,'" . $oldReplaceDomain . "','" . $newReplaceDomain . "')");
		}
	}
	plog('system.replacedomain', "域名转换 旧域名：{$oldReplaceDomain} 新域名：{$newReplaceDomain}");
	 message('替换完成!', $this->createPluginWebUrl('system/replacedomain'), 'success');
}
load()->func('tpl');
include $this->template('replacedomain');