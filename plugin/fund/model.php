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

		//计算倒计时时间
		public function check_time($endtime){
			$time = time();
			$ystime = $endtime - $time;
			if ($ystime > 86400) {
				$day = ceil($ystime / 86400);
				return array("n" => $day, 'text' => "天");
			} elseif ($ystime <= 86400 && $ystime > 3600) {
				$day = ceil($ystime / 3600);
				return array("n" => $day, 'text' => "时");
			} elseif ($ystime <= 3600){
				$day = ceil($ystime / 60);
				return array("n" => $day, 'text' => "分");
			} else {
				return array("n" => "", 'text' => "已结束");
			}
			return 0;
		}

		//自动执行众筹项目下架
		public function autogoods(){
			global $_W;
			$time = time();
			$goods = pdo_fetchall("SELECT id FROM " . tablename('sz_yi_goods') . " WHERE uniacid=".$_W['uniacid']." and timeend < " . $time . " and status > 0 and plugin = 'fund'");
			foreach ($goods as $key => $value) {
				pdo_update("sz_yi_goods", array("status" => 0), array("id" => $value['id'], 'uniacid' => $_W['uniacid']));
			}
		}
	}
}
