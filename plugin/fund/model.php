<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

if (!class_exists('FundModel')) {
	class FundModel extends PluginModel
	{

		public function getSet($uniacid = 0)
		{
			global $_W;
			if(!empty($uniacid)){
				$_W['uniacid'] = $uniacid;
			}
			$set = parent::getSet();
			$set['texts'] = array('order' => empty($set['texts']['order']) ? '众筹订单' : $set['texts']['order']);
			return $set;
		}

		public function check_goods($id){
			global $_W;
			$uniacid = $_W['uniacid'];
			$time = time();
			$goods = pdo_fetch("select status, timeend, plugin from ".tablename('sz_yi_goods')." where id =".$id." and uniacid=".$uniacid);
			if($goods['status'] == 1 && $goods['plugin'] == 'fund'){
				if($time >= $goods['timeend']){
					pdo_update('sz_yi_goods', array('status' => 0), array('id' => $id, 'uniacid' => $uniacid));
				}
			}
		}
	}
}
