<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('IndianaModel')) {

	class IndianaModel extends PluginModel
	{
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}
		
		public function setPeriod($id)
		{
			global $_W, $_GPC;
			echo "<pre>";print_r($id);exit;
		}
	}
}
