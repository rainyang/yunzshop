<?php
/*=============================================================================
#     FileName: log_detail.php
#         Desc: 用户端爱心基金记录
#       Author: ym
#      Version: 0.0.1
#   LastChange: 2016-07-14
=============================================================================*/
if (!defined('IN_IA')) {
	die('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

if ($_W['isajax']) {
	if ($operation == 'display') {
		return show_json(1, array('message' => '感谢您对合利宝基金事业做出贡献！', 'url' => $this->createPluginMobileUrl('love/log')));
 	}	
}	
include $this->template('log_detail');
	
