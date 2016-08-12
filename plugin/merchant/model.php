<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('MerchantModel')) {
	class MerchantModel extends PluginModel
	{

		//会员id下的所有供应商的supplier_uid
		public function getAllSupplierUids($member_id){
			global $_W, $_GPC;
			$supplier_uids = pdo_fetchall("select distinct supplier_uid from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and member_id={$member_id}");
	        $uids = "";
	        foreach ($supplier_uids as $key => $value) {
	            if ($key == 0) {
	                $uids .= $value['supplier_uid'];
	            } else {
	                $uids .= ','.$value['supplier_uid'];
	            }
	        }
	        if (empty($uids)) {
	            $uids = 0;
	        }
	        return $uids;
		}

		//基础设置
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}

		//发送消息
		function sendMessage($_var_20 = '', $_var_150 = array(), $_var_151 = '')
		{
			global $_W, $_GPC;
			$set = $this->getSet();
			$_var_153 = $set['templateid'];
			$member = m('member')->getMember($_var_20);
			$_var_154 = unserialize($member['noticeset']);
			if (!is_array($_var_154)) {
				$_var_154 = array();
			}
			if ($_var_151 == TM_MERCHANT_APPLY) {
				$_var_155 = $set['merchant_applycontent'];
				$_var_155 = str_replace('[昵称]', $_var_150['nickname'], $_var_155);
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', $_var_150['time']), $_var_155);
				$_var_156 = array('keyword1' => array('value' => !empty($set['merchant_applytitle']) ? $set['merchant_applytitle'] : '提现申请通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
				if (!empty($_var_153)) {
					m('message')->sendTplNotice($_var_20, $_var_153, $_var_156);
				} else {
					m('message')->sendCustomNotice($_var_20, $_var_156);
				}
			}
			if ($_var_151 == TM_MERCHANT_PAY) {
				$_var_155 = $set['merchant_finishcontent'];
				$_var_155 = str_replace('[昵称]', $_var_150['nickname'], $_var_155);
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', $_var_150['time']), $_var_155);
				$_var_156 = array('keyword1' => array('value' => !empty($set['merchant_finishtitle']) ? $set['merchant_finishtitle'] : '提现申请完成通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
				if (!empty($_var_153)) {
					m('message')->sendTplNotice($_var_20, $_var_153, $_var_156);
				} else {
					m('message')->sendCustomNotice($_var_20, $_var_156);
				}
			}
		}

		//权限
		function perms()
		{
			return array('merchant' => array('text' => $this->getName(), 'isplugin' => true, 'child' => array('cover' => array('text' => '入口设置'), 'merchants' => array('text' => '招商员', 'view' => '浏览'))));
		}
	}
}