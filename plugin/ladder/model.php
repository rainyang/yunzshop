<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('LadderModel')) {

	class LadderModel extends PluginModel
	{
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}
	}		
}