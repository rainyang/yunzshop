<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
/**
 * * recharge插件方法类
 * *
 * * 
 * * @package   流量话费充值插件公共方法
 * * @author    LuckyStar_D<duanfuxing@yunzshop.com>
 * * @version   v1.0
 * */
if (!class_exists('RechargeModel')) {
	class RechargeModel extends PluginModel
	{
		/**
 * 		 * 获取流量话费充值基础设置
 * 		 		 *
 * 		 		 		 * @return array $set
 * 		 		 		 		 */
		public function getSet()
		{

			$set = parent::getSet();
			return $set;
		}
	}
}
