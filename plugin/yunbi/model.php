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
	                'id' 			=> $order_goods[0]['id'],
	                'openid' 		=> $order_goods[0]['openid'],
	                'credittype' 	=> 'virtual_currency',
	                'money' 		=> $virtual_currency,
					'remark'		=> '购物获得'.$virtual_currency.$set['yunbi_title']
                );
				$this->addYunbiLog($_W['uniacid'],$data_log,'1');

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
	                'id' 			=> $value['id'],
	                'openid' 		=> $value['openid'],
	                'credittype' 	=> 'virtual_currency',
	                'money' 		=> $value['last_money'],
					'remark'		=> '分销下线获得'.$value['last_money'].$set['yunbi_title']
                );
				$this->addYunbiLog($uniacid,$data_log,'2');
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
	
		//虚拟币返现到余额
		public function PerformYunbiReturn($set,$uniacid){
			global $_W, $_GPC;
			$current_time = time();
			if ($set['isreturn_or_remove'] == 0) {
				$mc_sql = "update ".tablename('mc_members')." as m join (select sm.virtual_currency, sm.uid from ".tablename('sz_yi_member')." sm where sm.`uniacid` =  " . $uniacid . " and sm.virtual_currency > 0 ) as ac on m.uid = ac.uid set m.credit2 = credit2 + (ac.virtual_currency * " .$set['yunbi_return']. " / 100)  where m.`uniacid` =  " . $uniacid ;
				pdo_fetchall($mc_sql);
				$sz_sql = "update ".tablename('sz_yi_member')."  set credit2 = credit2 + (virtual_currency * " .$set['yunbi_return']. " / 100), last_money =  (virtual_currency * " .$set['yunbi_return']. " / 100) ,updatetime = " .$current_time. ", `virtual_currency` = virtual_currency - (virtual_currency * " .$set['yunbi_return']. " / 100) where `uniacid` =  " . $uniacid ." AND virtual_currency > 0";
				pdo_fetchall($sz_sql);

				$update_member = pdo_fetchall("SELECT id, uniacid, openid, last_money, updatetime FROM " . tablename('sz_yi_member') . " WHERE updatetime = :updatetime and uniacid = :uniacid ",
					array(':updatetime' => $current_time,':uniacid' => $uniacid
				));	

				foreach ($update_member as $key => $value) {
					$data_log = array(
		                'id' 			=> $value['id'],
		                'openid' 		=> $value['openid'],
		                'credittype' 	=> 'virtual_currency',
		                'money' 		=> $value['last_money'],
						'remark'		=> $set['yunbi_title']."返现到余额,扣除".$value['last_money']
	                );
	                $this->addYunbiLog($uniacid,$data_log,'5');
					$messages = array(
						'keyword1' => array(
							'value' => $set['yunbi_title'].'返现通知',
							'color' => '#73a68d'),
						'keyword2' =>array(
							'value' => '本次返现到月'.$value['last_money'].$set['yunbi_title'].",余额获得：".$value['last_money']."元",
							'color' => '#73a68d')
						);
					m('message')->sendCustomNotice($value['openid'], $messages);
				}
			}
		}

		//虚拟币清除
		public function RemoveYunbi($set,$uniacid){
			global $_W, $_GPC;
			$current_time = time();

			$sql = "update ".tablename('sz_yi_member')."  set last_money = virtual_currency ,updatetime = " .$current_time. ", `virtual_currency` = virtual_currency - virtual_currency where `uniacid` =  " . $uniacid ." AND virtual_currency > 0";
			pdo_fetchall($sql);
			$update_member = pdo_fetchall("SELECT id, uniacid, openid, last_money, updatetime FROM " . tablename('sz_yi_member') . " WHERE updatetime = :updatetime and uniacid = :uniacid ",
				array(':updatetime' => $current_time,':uniacid' => $uniacid
			));	
			foreach ($update_member as $key => $value) {


				$data_log = array(
	                'id' 			=> $value['id'],
	                'openid' 		=> $value['openid'],
	                'credittype' 	=> 'virtual_currency',
	                'money' 		=> $value['last_money'],
					'remark'		=> "清除".$set['yunbi_title']
                );
				$this->addYunbiLog($uniacid,$data_log,'6');
				$messages = array(
					'keyword1' => array(
						'value' => $set['yunbi_title'].'清除通知',
						'color' => '#73a68d'),
					'keyword2' =>array(
						'value' => '本次清除'.$value['last_money'].$set['yunbi_title'],
						'color' => '#73a68d')
					);
				m('message')->sendCustomNotice($value['openid'], $messages);
			}
		}

				
		public function addYunbiLog ($uniacid,$data=array(),$type){
			$data_log = array(
				'uniacid' 		=> $uniacid,
			    'mid' 			=> $data['id'],
			    'openid' 		=> $data['openid'],
			    'credittype' 	=> $data['virtual_currency'],
			    'money' 		=> $data['money'],
			    'status' 		=> 1,
			    'returntype' 	=> $type,
				'create_time'	=> time(),
				'remark'		=> $data['remark']
			);
			pdo_insert('sz_yi_yunbi_log', $data_log);
		}

		public function MoneySumTotal($conditions='',$mid='') {
			global $_W, $_GPC;
			if (!empty($mid)) {
			    $total = pdo_fetchcolumn("select sum(money) as money from" . tablename('sz_yi_yunbi_log') . " where uniacid = :uniacid ".$conditions." and money > 0 and mid = :mid ", array(
			        ':uniacid' => $_W['uniacid'],
			        ':mid' => $mid
			    ));
			} else {
			    $total = pdo_fetchcolumn("select sum(money) as money from" . tablename('sz_yi_yunbi_log') . " where uniacid = :uniacid ".$conditions." and money > 0 ", array(
			        ':uniacid' => $_W['uniacid']
			    ));
			}
		    return $total;
		}
	}
}