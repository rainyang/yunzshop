<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('RankingModel')) {

	class RankingModel extends PluginModel
	{
		public function getSet()
		{
			$_var_0 = parent::getSet();

			return $_var_0;
		}

	}
}
