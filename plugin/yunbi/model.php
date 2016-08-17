<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('YunbiModel')) {

	class YunbiModel extends PluginModel
	{
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}

		//消费获得虚拟币
		public function GetVirtualCurrency($orderid) {
			global $_W, $_GPC;
			$set = $this->getSet();
			if ($set['isyunbi'] == 1 && $set['isconsumption'] == 1) {
				if (empty($orderid)) {
					return false;
				}
				$order_goods = pdo_fetchall("SELECT g.isyunbi,g.yunbi_consumption,o.openid,o.price,o.dispatchprice,m.id,m.openid as mid FROM " . tablename('sz_yi_order') . " o left join " . tablename('sz_yi_member') . " m  on o.openid = m.openid left join " . tablename("sz_yi_order_goods") . " og on og.orderid = o.id  left join " . tablename("sz_yi_goods") . " g on g.id = og.goodsid WHERE o.id = :orderid and o.uniacid = :uniacid and m.uniacid = :uniacid",
					array(':orderid' => $orderid,':uniacid' => $_W['uniacid']
				));
				if (empty($order_goods)) {
					return false;
				}
				$virtual_currency = 0;
				foreach($order_goods as $good){
 					if($good['isyunbi'] == 1){
 						if ($good['yunbi_consumption'] > 0) {
 							$virtual_currency += ($good['price'] - $good['dispatchprice']) * $good['yunbi_consumption'] / 100;
 						} else {
							$virtual_currency += ($good['price'] - $good['dispatchprice']) * $set['consumption'] / 100;
 						}
 						$is_goods_return = true;
 					}
				}
				//商品 没有返消费币 返回
				if(!$is_goods_return)
				{
					return false;
				}
				m('member')->setCredit($order_goods[0]['openid'],'virtual_currency',$virtual_currency);
	        	$data_log = array(
					'uniacid' 		=> $_W['uniacid'],
	                'mid' 			=> $order_goods[0]['id'],
	                'openid' 		=> $order_goods[0]['openid'],
	                'credittype' 	=> 'virtual_currency',
	                'money' 		=> $virtual_currency,
	                'status' 		=> 1,
	                'returntype' 	=> 1,
					'create_time'	=> time(),
					'remark'		=> '购物获得'.$set['yunbi_title']
                );
				pdo_insert('sz_yi_yunbi_log', $data_log);
				$messages = array(
					'keyword1' => array(
						'value' => '购物获得'.$set['yunbi_title'].'通知',
						'color' => '#73a68d'),
					'keyword2' =>array(
						'value' => '本次获得'.$virtual_currency.$set['yunbi_title'],
						'color' => '#73a68d')
					);
				m('message')->sendCustomNotice($order_goods[0]['openid'], $messages);
			}
		}
		//分销商获得虚拟币
		public function GetVirtual_Currency($set,$uniacid) {
			global $_W, $_GPC;
			$current_time = time();

			$sql = "update ".tablename('sz_yi_member')." as m join (select sm.agentid, count(1) as agent_count from ".tablename('sz_yi_member')." sm where sm.`uniacid` =  " . $uniacid . " group by sm.agentid) as ac on m.id = ac.agentid set `virtual_currency` = virtual_currency + ac.agent_count *  " . $set['distribution'] . ", last_money =  ac.agent_count *  " . $set['distribution'] . ",updatetime = " .$current_time. " where m.`uniacid` =  " . $uniacid . " AND status = '1' AND isagent = '1' ";
			pdo_fetchall($sql);

			$update_member = pdo_fetchall("SELECT id, uniacid, openid, last_money, updatetime FROM " . tablename('sz_yi_member') . " WHERE updatetime = :updatetime and uniacid = :uniacid ",
				array(':updatetime' => $current_time,':uniacid' => $uniacid
			));	
			foreach ($update_member as $key => $value) {
				$data_log = array(
					'uniacid' 		=> $uniacid,
	                'mid' 			=> $value['id'],
	                'openid' 		=> $value['openid'],
	                'credittype' 	=> 'virtual_currency',
	                'money' 		=> $value['last_money'],
	                'status' 		=> 1,
	                'returntype' 	=> 2,
					'create_time'	=> time(),
					'remark'		=> '分销下线获得'.$set['yunbi_title']
                );
				pdo_insert('sz_yi_yunbi_log', $data_log);
				$messages = array(
					'keyword1' => array(
						'value' => '分销下线获得'.$set['yunbi_title'].'通知',
						'color' => '#73a68d'),
					'keyword2' =>array(
						'value' => '本次获得'.$value['last_money'].$set['yunbi_title'],
						'color' => '#73a68d')
					);
				m('message')->sendCustomNotice($value['openid'], $messages);
			}
				
		}
	
	}
}