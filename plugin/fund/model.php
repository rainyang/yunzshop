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
			$goods = pdo_fetch(" select marketprice, sales, status, timeend, plugin from ".tablename('sz_yi_goods')." where id =".$id." and uniacid=".$uniacid);
			if($goods['status'] == 1 && $goods['plugin'] == 'fund'){
				$allprice = pdo_fetchcolumn(" select allprice from ".tablename('sz_yi_fund_goods')." where goodsid =".$id." and uniacid=".$uniacid);
				$yetprice = pdo_fetchcolumn("select sum(og.price) as yetprice from ". tablename('sz_yi_order_goods') ." og left join " . tablename('sz_yi_order') . " o on og.orderid=o.id  where o.status > 0 and og.goodsid=".$id);
    			$yetprice += $goods['marketprice'] * $goods['sales'];
				if($time >= $goods['timeend'] || $yetprice >= $allprice){
					pdo_update('sz_yi_goods', array('status' => 0), array('id' => $id, 'uniacid' => $uniacid));
				}
			}
		}
	}
}
