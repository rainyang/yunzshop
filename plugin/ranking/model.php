<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('RankingModel')) {

	class RankingModel extends PluginModel
	{
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}
		public function getMember($id)
		{
			global $_W, $_GPC;
			 $list      = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid = '" .$_W['uniacid'] . "' and id = '".$id."'");
			 return $list;
		}
	}
}
