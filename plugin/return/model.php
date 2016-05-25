<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('ReturnModel')) {

	class ReturnModel extends PluginModel
	{
		public function getSet()
		{
			$_var_0 = parent::getSet();

			return $_var_0;
		}
		public function setGoodsQueue($orderid) {
			global $_W;
			$_var_0 = $this->getSet();
	                
			$order_goods = pdo_fetchall("SELECT og.orderid,og.goodsid,og.total,og.price,g.isreturnqueue,o.openid,m.id as mid FROM " . tablename('sz_yi_order') . " o left join " . tablename('sz_yi_member') . " m  on o.openid = m.openid left join " . tablename("sz_yi_order_goods") . " og on og.orderid = o.id  left join " . tablename("sz_yi_goods") . " g on g.id = og.goodsid WHERE o.id = :orderid and o.uniacid = :uniacid and m.uniacid = :uniacid",
				array(':orderid' => $orderid,':uniacid' => $_W['uniacid']
			));
		
			foreach($order_goods as $good){
				if($good['isreturnqueue'] == 1){

					$goods_queue = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order_goods_queue') . " where uniacid = ".$_W['uniacid']." and goodsid = ".$good['goodsid']." order by queue desc limit 1" );
					$queuemessages = '';
					for ($i=1; $i <= $good['total'] ; $i++) { 
						$queue = $goods_queue['queue']+$i;
						$queuemessages .= $queue."、";
						$data = array(
		                    'uniacid' 	=> $_W['uniacid'],
		                    'openid' 	=> $good['openid'],
		                    'goodsid' 	=> $good['goodsid'],
		                    'orderid' 	=> $good['orderid'],
		                    'price' 	=> $good['price']/$good['total'],
		                    'queue' 	=> $queue,
		                    'create_time' 	=> time()
		                    );
		                pdo_insert('sz_yi_order_goods_queue',$data);
		                $queueid = pdo_insertid();

						if(!($queue%$_var_0['queue']))
						{
							$queue = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order_goods_queue') . " where uniacid = ".$_W['uniacid']." and goodsid = ".$good['goodsid']." and status = 0 order by queue asc limit 1" );

							pdo_update('sz_yi_order_goods_queue', array('returnid'=>$queueid,'status'=>'1'), array('id' => $queue['id'], 'uniacid' => $_W['uniacid']));
							m('member')->setCredit($queue['openid'],'credit2',$queue['price']);

							$messages = array(
								'keyword1' => array('value' => '排列返现通知',
									'color' => '#73a68d'),
								'keyword2' => array('value' => '本次返现金额'.$queue['price']."元！",
									'color' => '#73a68d'),
								'keyword3' => array('value' => '排列返现完成！',
									'color' => '#73a68d')
								);
							m('message')->sendCustomNotice($queue['openid'], $messages);
						}

					}

						$queue_messages = array(
							'keyword1' => array('value' => '加入排列通知',
								'color' => '#73a68d'),
							'keyword2' => array('value' => "您已加入排列，排列号为".$queuemessages."号！",
								'color' => '#73a68d'),
							'keyword3' => array('value' => '加入排列完成，请等待返现！',
								'color' => '#73a68d')
							);
						m('message')->sendCustomNotice($good['openid'], $queue_messages);
				}
			}

		}

		public function cumulative_order_amount($orderid) {
			global $_W;
			$_var_0 = $this->getSet();
			if($_var_0['isqueue'])
			{
				$this->setGoodsQueue($orderid);
			}

			if ($_var_0['isreturn'] == 1) {
				if (empty($orderid)) {
					return false;
				}

				$order_goods = pdo_fetchall("SELECT og.price,g.isreturn,o.openid,m.id as mid FROM " . tablename('sz_yi_order') . " o left join " . tablename('sz_yi_member') . " m  on o.openid = m.openid left join " . tablename("sz_yi_order_goods") . " og on og.orderid = o.id  left join " . tablename("sz_yi_goods") . " g on g.id = og.goodsid WHERE o.id = :orderid and o.uniacid = :uniacid and m.uniacid = :uniacid",
					array(':orderid' => $orderid,':uniacid' => $_W['uniacid']
				));
				$order_price = 0;
				foreach($order_goods as $good){
 					if($good['isreturn'] == 1){
 						$order_price += $good['price'];
 					}
				}
				if (empty($order_goods)) {
					return false;
				}
				if($_var_0['returnrule'] == 1)
				{
					$this->setOrderRule($order_goods,$order_price);
				}else
				{
					$this->setOrderMoneyRule($order_goods,$order_price);
				}
				

			}
			
		}

		//单笔订单
		public function setOrderRule($order_goods,$order_price)
		{
			global $_W;
			$_var_0 = $this->getSet();

			$data = array(
                'mid' => $order_goods[0]['mid'],
                'uniacid' => $_W['uniacid'],
                'money' => $order_price,
                'returnrule' => $_var_0['returnrule'],
				'create_time'	=> time()
                );
			pdo_insert('sz_yi_return', $data);
			$text = "您的订单以加入全返机制，等待全返。";
			$_var_156 = array(
				'keyword1' => array('value' => '订单全返通知', 'color' => '#73a68d'), 
				'keyword2' => array('value' => '[订单返现金额]'.$return_money, 'color' => '#73a68d'),
				'remark' => array('value' => $text)
			);
        	m('message')->sendCustomNotice($order_goods[0]['openid'], $_var_156);

		}
		//订单累计金额
		public function setOrderMoneyRule($order_goods,$order_price)
		{
			global $_W;
			$_var_0 = $this->getSet();

				$return = pdo_fetch("SELECT * FROM " . tablename('sz_yi_return_money') . " WHERE mid = :mid and uniacid = :uniacid",
					array(':mid' => $order_goods[0]['mid'],':uniacid' => $_W['uniacid']
				));
				if (!empty($return)) {
					$returnid = $return['id'];
					$data = array(
	                    'money' => $return['money']+$order_price,
	                );
	                pdo_update('sz_yi_return_money', $data, array(
	                    'id' => $returnid
	                ));
				} else {
					$data = array(
	                    'mid' => $order_goods[0]['mid'],
	                    'uniacid' => $_W['uniacid'],
	                    'money' => $order_price,
	                    );
	                pdo_insert('sz_yi_return_money',$data);
	                $returnid = pdo_insertid();
				}
				$return_money = pdo_fetchcolumn("SELECT money FROM " . tablename('sz_yi_return_money') . " WHERE id = :id and uniacid = :uniacid",
					array(':id' => $returnid,':uniacid' => $_W['uniacid']
				));

				$this->setmoney($_var_0['orderprice'],$_W['uniacid']);

				if ($return_money >= $_var_0['orderprice']) {
					$text = "您的订单累计金额已经超过".$_var_0['orderprice']."元，每".$_var_0['orderprice']."元可以加入全返机制，等待全返。";
				} else {
					$text = "您的订单累计金额还差" . ($_var_0['orderprice']-$return_money) . "元达到".$_var_0['orderprice']."元，订单累计金额每达到".$_var_0['orderprice']."元就可以加入全返机制，等待全返。继续加油！";
				}
				$_var_156 = array(
					'keyword1' => array('value' => '订单金额累计通知', 'color' => '#73a68d'), 
					'keyword2' => array('value' => '[订单累计金额]'.$return_money, 'color' => '#73a68d'),
					'remark' => array('value' => $text)
				);
	        	m('message')->sendCustomNotice($order_goods[0]['openid'], $_var_156);
			
		}
		
		//单笔订单返现
		public function setOrderReturn(){
			global $_W;
			$_var_0 = $this->getSet();

			//返利队列
			$data_money = pdo_fetchall("select * from " . tablename('sz_yi_return') . " where uniacid = '". $_W['uniacid'] ."' and status = 0 and returnrule = '".$_var_0['returnrule']."'");
			$return_money_totle = 0;
			$surplus_money_totle = 0;
			foreach ($data_money as $key => $value) {
				$r_each = $value['money'] * $_var_0['percentage'] / 100;//可返利金额
				
				$member = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid = '". $_W['uniacid'] ."' and id = '".$value['mid']."'");

				if(($value['money']-$value['return_money']) < $r_each){
					pdo_update('sz_yi_return', array('return_money'=>$value['money'],'status'=>'1'), array('id' => $value['id'], 'uniacid' => $_W['uniacid']));
					m('member')->setCredit($member['openid'],'credit2',$value['money']-$value['return_money']);

					$return_money_totle += $value['money']-$value['return_money'];
		

				}else
				{
					pdo_update('sz_yi_return', array('return_money'=>$value['return_money']+$r_each), array('id' => $value['id'], 'uniacid' => $_W['uniacid']));
					m('member')->setCredit($member['openid'],'credit2',$r_each);

					$surplus = $value['money']-$value['return_money']-$r_each;

					$return_money_totle += $r_each;
					$surplus_money_totle += $surplus;
				}
			}
			if($return_money_totle > 0)
			{
					$messages = array(
						'keyword1' => array(
							'value' => '返现通知',
							'color' => '#73a68d'),
						'keyword2' =>array(
							'value' => '本次返现金额'.$return_money_totle."元",
							'color' => '#73a68d'),
						'keyword3' => array(
							'value' => "此返单剩余返现金额".$surplus_money_totle."元",
							'color' => '#73a68d')
						);
					m('message')->sendCustomNotice($member['openid'], $messages);
			}

		}

		//订单累计金额返现
		public function setOrderMoneyReturn(){
			global $_W;
			$_var_0 = $this->getSet();

			//昨天成交金额
			$daytime = strtotime(date("Y-m-d 00:00:00"));
			$stattime = $daytime - 86400;
			$endtime = $daytime - 1;
			$sql = "select sum(o.price) from ".tablename('sz_yi_order')." o left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 where 1 and o.status>=3 and o.uniacid={$_W['uniacid']} and  o.finishtime >={$stattime} and o.finishtime < {$endtime}  ORDER BY o.finishtime DESC,o.status DESC";
			$ordermoney = pdo_fetchcolumn($sql);
			$ordermoney = floatval($ordermoney);
			$r_ordermoney = $ordermoney * $_var_0['percentage'] / 100;//可返利金额


			//返利队列
			$data_money = pdo_fetchall("select * from " . tablename('sz_yi_return') . " where uniacid = '". $_W['uniacid'] ."' and status = 0 and returnrule = '".$_var_0['returnrule']."'");
			$r_each = $r_ordermoney / count($data_money);//每个队列返现金额
			$r_each = sprintf("%.2f", $r_each);
			$return_money_totle = 0;
			$surplus_money_totle = 0;
			foreach ($data_money as $key => $value) {
				
				$member = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid = '". $_W['uniacid'] ."' and id = '".$value['mid']."'");
				
				if(($value['money']-$value['return_money']) < $r_each){
					pdo_update('sz_yi_return', array('return_money'=>$value['money'],'status'=>'1'), array('id' => $value['id'], 'uniacid' => $_W['uniacid']));
					m('member')->setCredit($member['openid'],'credit2',$value['money']-$value['return_money']);
					$return_money_totle += $value['money']-$value['return_money'];

				}else
				{
					pdo_update('sz_yi_return', array('return_money'=>$value['return_money']+$r_each), array('id' => $value['id'], 'uniacid' => $_W['uniacid']));
					m('member')->setCredit($member['openid'],'credit2',$r_each);

					$surplus = $value['money']-$value['return_money']-$r_each;
					$return_money_totle += $r_each;
					$surplus_money_totle += $surplus;

				}
			}
			if($return_money_totle)
			{
				$messages = array(
					'keyword1' => array(
						'value' => '返现通知',
						'color' => '#73a68d'),
					'keyword2' =>array(
						'value' => '本次返现金额'.$return_money_totle."元",
						'color' => '#73a68d'),
					'keyword3' => array(
						'value' => "此返单剩余返现金额".$surplus_money_totle."元",
						'color' => '#73a68d')
					);
				m('message')->sendCustomNotice($member['openid'], $messages);
			}

		}
		// 查询可参加返利的 加入返利队列
		public function setmoney($orderprice,$uniacid){
			global $_W;
			$_var_0 = $this->getSet();
			$data_money = pdo_fetchall("select * from " . tablename('sz_yi_return_money') . " where uniacid = '". $uniacid . "' and money >= '" . $orderprice . "' ");
			if($data_money){
				foreach ($data_money as $key => $value) {
					if($value['money'] >= $orderprice){
						$data = array(
							'uniacid' 		=> $value['uniacid'],
							'mid' 			=> $value['mid'],
							'money' 		=> $orderprice,
							'returnrule' => $_var_0['returnrule'],
							'create_time'	=> time()
							 );
						pdo_insert('sz_yi_return', $data);
						pdo_update('sz_yi_return_money', array('money'=>$value['money']-$orderprice), array('id' => $value['id'], 'uniacid' => $uniacid));	
					}
				}
				$this->setmoney($orderprice,$uniacid);
			}

		}

	}
}
