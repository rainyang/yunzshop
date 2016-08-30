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
		public function setGoodsQueue($orderid,$_var_0=array(),$uniacid='') {

			$order_goods = pdo_fetchall("SELECT og.orderid,og.goodsid,og.total,og.price,g.isreturnqueue,o.openid,m.id as mid FROM " . tablename('sz_yi_order') . " o left join " . tablename('sz_yi_member') . " m  on o.openid = m.openid left join " . tablename("sz_yi_order_goods") . " og on og.orderid = o.id  left join " . tablename("sz_yi_goods") . " g on g.id = og.goodsid WHERE o.id = :orderid and o.uniacid = :uniacid and m.uniacid = :uniacid",
				array(':orderid' => $orderid,':uniacid' => $uniacid
			));

			foreach($order_goods as $good){
				if($good['isreturnqueue'] == 1){

					$goods_queue = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order_goods_queue') . " where uniacid = ".$uniacid." and goodsid = ".$good['goodsid']." order by queue desc limit 1" );
					$queuemessages = '';

					for ($i=1; $i <= $good['total'] ; $i++) { 
						$queue = $goods_queue['queue']+$i;
						$queuemessages .= $queue."、";
						$data = array(
		                    'uniacid' 	=> $uniacid,
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
							$queue = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order_goods_queue') . " where uniacid = ".$uniacid." and goodsid = ".$good['goodsid']." and status = 0 order by queue asc limit 1" );

							pdo_update('sz_yi_order_goods_queue', array('returnid'=>$queueid,'status'=>'1'), array('id' => $queue['id'], 'uniacid' => $uniacid));
							$this->setReturnCredit($queue['openid'],'credit2',$queue['price'],'4');
							$queue_price_txt= $_var_0['queue_price'];
							$queue_price_txt = str_replace('[返现金额]', $queue['price'], $queue_price_txt);
							$messages = array(
								'keyword1' => array('value' => $_var_0['queue_title']?$_var_0['queue_title']:'排列返现通知',
									'color' => '#73a68d'),
								'keyword2' => array('value' => $queue_price_txt?$queue_price_txt:'本次返现金额'.$queue['price']."元！",
									'color' => '#73a68d')
							);
							m('message')->sendCustomNotice($queue['openid'], $messages);
						}

					}
						$queuemessages_txt= $_var_0['queuemessages'];
						$queuemessages_txt = str_replace('[排列序号]', $queuemessages, $queuemessages_txt);
						$queue_messages = array(
							'keyword1' => array('value' => $_var_0['add_queue_title']?$_var_0['add_queue_title']:'加入排列通知',
								'color' => '#73a68d'),
							'keyword2' => array('value' => $queuemessages_txt?$queuemessages_txt:"您已加入排列，排列号为".$queuemessages."号！",
								'color' => '#73a68d')
							);
						m('message')->sendCustomNotice($good['openid'], $queue_messages);
				}
			}

		}
		public function setMembeerLevel($orderid,$_var_0=array(),$uniacid='') {
			$order_goods = pdo_fetchall("SELECT og.price,og.total,g.isreturn,g.returns,g.returns2,g.returntype,o.openid,m.id as mid ,m.level, m.agentlevel FROM " . tablename('sz_yi_order') . " o left join " . tablename('sz_yi_member') . " m  on o.openid = m.openid left join " . tablename("sz_yi_order_goods") . " og on og.orderid = o.id  left join " . tablename("sz_yi_goods") . " g on g.id = og.goodsid WHERE o.id = :orderid and o.uniacid = :uniacid and m.uniacid = :uniacid",
				array(':orderid' => $orderid,':uniacid' => $uniacid
			));	
			foreach ($order_goods as $key => $value) {
				if ($value['returntype'] == 1) {
					$discounts = json_decode($value['returns'],true);
					$level = $value['level'];
				} else {
					$discounts = json_decode($value['returns2'],true);
					$level = $value['agentlevel'];		
				}
				if ($level == '0') {
					$money += $discounts['default']?$discounts['default']*$value['total']:'0';
				} else {
					$money += $discounts['level'.$level]?$discounts['level'.$level]*$value['total']:'0';
				}
			}
			if( $money > 0 )
			{
				// $data = array(
				// 	'uniacid' => $uniacid,
	   //              'mid' => $order_goods[0]['mid'],
	   //              'openid' => $order_goods[0]['openid'],
	   //              'money' => $money,
	   //              'status' => 1,
	   //              'returntype' => 1,
				// 	'create_time'	=> time()
    //             );
				// pdo_insert('sz_yi_return_log', $data);

				$this->setReturnCredit($order_goods[0]['openid'],'credit2',$money,'1');
				$member_price_txt = $_var_0['member_price'];
				$member_price_txt = str_replace('[排列序号]', $money, $member_price_txt);
				$member_price_txt = str_replace('[订单ID]', $orderid, $member_price_txt);
				$_var_156 = array(
					'keyword1' => array('value' => $_var_0['member_title']?$_var_0['member_title']:'购物返现通知', 'color' => '#73a68d'), 
					'keyword2' => array('value' => $member_price_txt?$member_price_txt:'[返现金额]'.$money.'元,已存到您的余额', 'color' => '#73a68d')
				);

	        	m('message')->sendCustomNotice($order_goods[0]['openid'], $_var_156);
			}
			
			
		}
		public function cumulative_order_amount($orderid) {
			global $_W, $_GPC;
			$_var_0 = $this->getSet();
			$order = pdo_fetch("SELECT * FROM ".tablename('sz_yi_order')." WHERE id=:id and uniacid=:uniacid", array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			//会员等级返现
			if($_var_0['islevelreturn'])
			{
				$this->setMembeerLevel($orderid,$_var_0,$_W['uniacid']);
			}

			
			//排列全返
			if($_var_0['isqueue'])
			{
				$this->setGoodsQueue($orderid,$_var_0,$_W['uniacid']);
			}

			if ($_var_0['isreturn'] == 1) {
				if (empty($orderid)) {
					return false;
				}
				if (empty($order['cashier'])) {
					$order_goods = pdo_fetchall("SELECT og.price,g.isreturn,o.openid,m.id as mid FROM " . tablename('sz_yi_order') . " o left join " . tablename('sz_yi_member') . " m  on o.openid = m.openid left join " . tablename("sz_yi_order_goods") . " og on og.orderid = o.id  left join " . tablename("sz_yi_goods") . " g on g.id = og.goodsid WHERE o.id = :orderid and o.uniacid = :uniacid and m.uniacid = :uniacid",
					array(':orderid' => $orderid,':uniacid' => $_W['uniacid']
					));
					$order_price = 0;
					$is_goods_return = false;
					foreach($order_goods as $good){
	 					if($good['isreturn'] == 1){
	 						$order_price += $good['price'];
	 						$is_goods_return = true;
	 					}
					}	
				} else {
					$order_price = $order['price'];
					$order_goods = array();
					$m = m('member')->getMember($order['openid']);
					$order_goods[0]['openid'] = $order['openid'];
					$order_goods[0]['mid'] = $m['id'];
					$is_goods_return = true;

				}
				
				//商品 没有开启全返 返回
				if(!$is_goods_return && empty($order['cashier']))
				{
					return false;
				}
				if (empty($order_goods)) {
					return false;
				}
				if($_var_0['returnrule'] == 1)
				{
					$this->setOrderRule($order_goods,$order_price,$_var_0,$_W['uniacid']);
				}elseif($_var_0['returnrule'] == 2)
				{
					$this->setOrderMoneyRule($order_goods,$order_price,$_var_0,$_W['uniacid']);
				}
				

			}
			
		}

		//单笔订单
		public function setOrderRule($order_goods,$order_price,$_var_0=array(),$uniacid='')
		{


			$data = array(
                'mid' => $order_goods[0]['mid'],
                'uniacid' => $uniacid,
                'money' => $order_price,
                'returnrule' => $_var_0['returnrule'],
				'create_time'	=> time()
                );
			pdo_insert('sz_yi_return', $data);
			$order_price_txt = $_var_0['order_price'];
			$order_price_txt = str_replace('[订单金额]', $order_price, $order_price_txt);
			$_var_156 = array(
				'keyword1' => array('value' => $_var_0['add_single_title']?$_var_0['add_single_title']:'订单全返通知', 'color' => '#73a68d'), 
				'keyword2' => array('value' => $order_price_txt?$order_price_txt:'[订单返现金额]'.$order_price, 'color' => '#73a68d')
			);
        	m('message')->sendCustomNotice($order_goods[0]['openid'], $_var_156);

		}
		//订单累计金额
		public function setOrderMoneyRule($order_goods,$order_price,$_var_0=array(),$uniacid='')
		{
				$return = pdo_fetch("SELECT * FROM " . tablename('sz_yi_return_money') . " WHERE mid = :mid and uniacid = :uniacid",
					array(':mid' => $order_goods[0]['mid'],':uniacid' => $uniacid
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
	                    'uniacid' => $uniacid,
	                    'money' => $order_price,
	                    );
	                pdo_insert('sz_yi_return_money',$data);
	                $returnid = pdo_insertid();
				}
				$return_money = pdo_fetchcolumn("SELECT money FROM " . tablename('sz_yi_return_money') . " WHERE id = :id and uniacid = :uniacid",
					array(':id' => $returnid,':uniacid' => $uniacid
				));
				$this->setmoney($_var_0['orderprice'],$_var_0,$uniacid);

				
				if ($return_money >= $_var_0['orderprice']) {
					$total_reach_txt = $_var_0['total_reach'];
					$total_reach_txt = str_replace('[标准金额]', $_var_0['orderprice'], $total_reach_txt);

					$text = $total_reach_txt?$total_reach_txt:"您的订单累计金额已经超过".$_var_0['orderprice']."元，每".$_var_0['orderprice']."元可以加入全返机制，等待全返。";
				} else {
					$total_unreached_txt = $_var_0['total_unreached'];
					$total_unreached_txt = str_replace('[缺少金额]', $_var_0['orderprice']-$return_money, $total_unreached_txt);
					$total_unreached_txt = str_replace('[标准金额]', $_var_0['orderprice'], $total_unreached_txt);

					$text = $total_unreached_txt?$total_unreached_txt:"您的订单累计金额还差" . ($_var_0['orderprice']-$return_money) . "元达到".$_var_0['orderprice']."元，订单累计金额每达到".$_var_0['orderprice']."元就可以加入全返机制，等待全返。继续加油！";
				}
				$total_price_txt = $_var_0['total_price'];
				$total_price_txt = str_replace('[累计金额]', $return_money, $total_price_txt);
				$_var_156 = array(
					'keyword1' => array('value' => $_var_0['total_title']?$_var_0['total_title']:'订单金额累计通知', 'color' => '#73a68d'), 
					'keyword2' => array('value' => $total_price_txt?$total_price_txt:'[订单累计金额]'.$return_money, 'color' => '#73a68d'),
					'remark' => array('value' => $text)
				);
	        	m('message')->sendCustomNotice($order_goods[0]['openid'], $_var_156);
			
		}
		
		//单笔订单返现
		public function setOrderReturn($_var_0=array(),$uniacid=''){
			$tmpdir = IA_ROOT . "/addons/sz_yi/tmp/reutrn";
			$return_log = $tmpdir."/return_jog.txt";
			$log_content[] = date("Y-m-d H:i:s")."公众号ID：".$uniacid." 单笔订单返现开始==============\r\n";
			$member_record = pdo_fetchall("SELECT r.mid, m.level, m.agentlevel, m.openid FROM " . tablename('sz_yi_return') . " r 
				left join " . tablename('sz_yi_member') . " m on (r.mid = m.id) 
			 WHERE r.uniacid = '". $uniacid ."' and r.returnrule = '".$_var_0['returnrule']."' and r.delete = '0'  group by r.mid");
			$level =  array();
			if($_var_0['islevels'] == 1)
			{
				if($_var_0['islevel'] == 1)
				{
					$level = json_decode($_var_0['member'], true);
				}elseif($_var_0['islevel'] == 2)
				{
					$level = json_decode($_var_0['commission'], true);
				}
			}
			$log_content[] = "单笔返现队列人：";
			$log_content[] = var_export($member_record,true);
			$log_content[] = "\r\n";
			$current_time = time();
			foreach ($member_record as $key => $value) {
				$percentage = $_var_0['percentage'];

				if($_var_0['islevels'] == 1)
				{
					if($_var_0['islevel'] == 1)
					{
						$percentage = $level['level'.$value['level']]?$level['level'.$value['level']]:$_var_0['percentage'];
					}elseif($_var_0['islevel'] == 2)
					{
						$percentage = $level['level'.$value['agentlevel']]?$level['level'.$value['agentlevel']]:$_var_0['percentage'];
					}
				}
					// $unfinished_record[$percentage] = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_return') . " WHERE uniacid = '". $uniacid ."' and status=0 and (money - return_money) > money * ".$percentage." / 100 and returnrule = '".$_var_0['returnrule']."' and mid = '".$value['mid']."' ");
					// $finished_record[$percentage] = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_return') . " WHERE uniacid = '". $uniacid ."' and status=0 and (money - `return_money`) <= money * ".$percentage." / 100 and returnrule = '".$_var_0['returnrule']."' and mid = '".$value['mid']."' ");
					if($_var_0['degression'] == 1)
					{
						pdo_query("update  " . tablename('sz_yi_return') . " set last_money = money - return_money, status=1, return_money = money, updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and money - return_money <= 0.5  and returnrule = '".$_var_0['returnrule']."' and mid = '".$value['mid']."' ");
						pdo_query("update  " . tablename('sz_yi_return') . " set return_money = return_money + (money - return_money) * ".$percentage." / 100,last_money = (money - return_money) * ".$percentage." / 100,updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and money - return_money > 0.5 and returnrule = '".$_var_0['returnrule']."' and mid = '".$value['mid']."' ");
					}else{
						pdo_query("update  " . tablename('sz_yi_return') . " set last_money = money - return_money, status=1, return_money = money, updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and (money - `return_money`) <= money * ".$percentage." / 100 and returnrule = '".$_var_0['returnrule']."' and mid = '".$value['mid']."' ");
						pdo_query("update  " . tablename('sz_yi_return') . " set return_money = return_money + money * ".$percentage." / 100,last_money = money * ".$percentage." / 100,updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and (money - return_money) > money * ".$percentage." / 100 and returnrule = '".$_var_0['returnrule']."' and mid = '".$value['mid']."' ");
					}
					
			}
			$return_record = pdo_fetchall("SELECT sum(r.money) as money, sum(r.return_money) as return_money, sum(r.last_money) as last_money,m.openid,count(r.id) as count  FROM " . tablename('sz_yi_return') . " r 
				left join " . tablename('sz_yi_member') . " m on (r.mid = m.id) 
			 WHERE r.uniacid = '". $uniacid ."' and r.updatetime = '".$current_time."' and r.returnrule = '".$_var_0['returnrule']."' and r.delete = '0'  group by r.mid");
			$log_content[] = "单笔返现内容：";
			$log_content[] = var_export($return_record,true);
			$log_content[] = "\r\n";
			foreach ($return_record as $key => $value) {
				if($value['last_money'] > 0)
				{
					$return_money_totle = $value['last_money'];
					$surplus_money_totle = $value['money']-$value['return_money'];

					$this->setReturnCredit($value['openid'],'credit2',$return_money_totle,'2');
					$single_message_txt = $_var_0['single_message'];
					$single_message_txt = str_replace('[返现金额]', $return_money_totle, $single_message_txt);
					$single_message_txt = str_replace('[剩余返现金额]', $surplus_money_totle, $single_message_txt);
					$messages = array(
						'keyword1' => array(
							'value' => $_var_0['single_return_title']?$_var_0['single_return_title']:'返现通知',
							'color' => '#73a68d'),
						'keyword2' =>array(
							'value' => $single_message_txt?$single_message_txt:'本次返现金额'.$return_money_totle."元",
							'color' => '#73a68d')
						);
					m('message')->sendCustomNotice($value['openid'], $messages);
				}
			}	
			$log_content[] = date("Y-m-d H:i:s")."公众号ID：".$uniacid." 单笔订单返现完成==============\r\n\r\n\r\n\r\n";
			file_put_contents($return_log,$log_content,FILE_APPEND);

		}

		//订单累计金额返现
		public function setOrderMoneyReturn($_var_0=array(),$uniacid=''){
			$tmpdir = IA_ROOT . "/addons/sz_yi/tmp/reutrn";
			$return_log = $tmpdir."/return_jog.txt";
			$log_content[] = date("Y-m-d H:i:s")."公众号ID：".$uniacid." 订单累计返现开始==============\r\n";
			//昨天成交金额
			$daytime = strtotime(date("Y-m-d 00:00:00"));
			$stattime = $daytime - 86400;
			$endtime = $daytime - 1;
			$sql = "select sum(o.price) from ".tablename('sz_yi_order')." o left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 where 1 and o.status>=3 and o.uniacid={$uniacid} and  o.finishtime >={$stattime} and o.finishtime < {$endtime}  ORDER BY o.finishtime DESC,o.status DESC";
			$ordermoney = pdo_fetchcolumn($sql);
			$ordermoney = floatval($ordermoney);
			$log_content[] = "昨日成交金额：".$ordermoney."\r\n";
			$r_ordermoney = $ordermoney * $_var_0['percentage'] / 100;//可返利金额
			$log_content[] = "可返现金额：".$r_ordermoney."\r\n";
			//返利队列
			$queue_count = pdo_fetchcolumn("select count(1) from " . tablename('sz_yi_return') . " where uniacid = '". $uniacid ."' and status = 0 and `delete` = '0' and returnrule = '".$_var_0['returnrule']."'");
			$log_content[] = "可返现队列数：".$queue_count."\r\n";
			if($r_ordermoney>0 && $queue_count)
			{
				$r_each = $r_ordermoney / $queue_count;//每个队列返现金额
				$r_each = sprintf("%.2f", $r_each);
				$log_content[] = "每个队列返现金额：".$r_each."\r\n";
				$current_time = time();

				// $unfinished_record = pdo_fetchall("SELECT mid,count(1) as count FROM " . tablename('sz_yi_return') . " WHERE uniacid = '". $uniacid ."' and status=0 and (money - return_money) > '".$r_each."' and returnrule = '".$_var_0['returnrule']."' group by mid ");

				// $finished_record = pdo_fetchall("SELECT mid,count(1) as count FROM " . tablename('sz_yi_return') . " WHERE uniacid = '". $uniacid ."' and status=0 and (money - `return_money`) <= '".$r_each."' and returnrule = '".$_var_0['returnrule']."'  group by mid");
				pdo_query("update  " . tablename('sz_yi_return') . " set last_money = money - return_money, status=1, return_money = money, updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and (money - `return_money`) <= '".$r_each."' and returnrule = '".$_var_0['returnrule']."' ");
				pdo_query("update  " . tablename('sz_yi_return') . " set return_money = return_money + '".$r_each."',last_money = '".$r_each."',updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and (money - `return_money`) > '".$r_each."' and returnrule = '".$_var_0['returnrule']."' ");
				$return_record = pdo_fetchall("SELECT sum(r.money) as money, sum(r.return_money) as return_money, sum(r.last_money) as last_money,m.openid,count(r.id) as count  FROM " . tablename('sz_yi_return') . " r 
					left join " . tablename('sz_yi_member') . " m on (r.mid = m.id) 
				 WHERE r.uniacid = '". $uniacid ."' and r.updatetime = '".$current_time."' and r.returnrule = '".$_var_0['returnrule']."' and r.delete = '0'  group by r.mid");
				$log_content[] ="SELECT sum(r.money) as money, sum(r.return_money) as return_money, sum(r.last_money) as last_money,m.openid,count(r.id) as count  FROM " . tablename('sz_yi_return') . " r 
					left join " . tablename('sz_yi_member') . " m on (r.mid = m.id) 
				 WHERE r.uniacid = '". $uniacid ."' and r.updatetime = '".$current_time."' and r.returnrule = '".$_var_0['returnrule']."' and r.delete = '0'  group by r.mid \r\n";
				$log_content[] = "订单累计金额返现内容：".var_export($return_record,true)."\r\n";
				foreach ($return_record as $key => $value) {
					if($value['last_money'] > 0)
					{
						$return_money_totle = $value['last_money'];
						$surplus_money_totle = $value['money']-$value['return_money'];

						$this->setReturnCredit($value['openid'],'credit2',$return_money_totle,'3');
					$total_messsage_txt = $_var_0['total_messsage'];
					$total_messsage_txt = str_replace('[返现金额]', $return_money_totle, $total_messsage_txt);
					$total_messsage_txt = str_replace('[剩余返现金额]', $surplus_money_totle, $total_messsage_txt);
					$messages = array(
						'keyword1' => array(
							'value' => $_var_0['total_return_title']?$_var_0['total_return_title']:'返现通知',
							'color' => '#73a68d'),
						'keyword2' =>array(
							'value' => $total_messsage_txt?$total_messsage_txt:'本次返现金额'.$return_money_totle."元",
							'color' => '#73a68d')
						);
						m('message')->sendCustomNotice($value['openid'], $messages);
					}
				}		
			}
			$log_content[] = date("Y-m-d H:i:s")."公众号ID：".$uniacid." 订单累计返现完成==============\r\n\r\n";
			file_put_contents($return_log,$log_content,FILE_APPEND);
		}

		// 查询可参加返利的 加入返利队列
		public function setmoney($orderprice,$_var_0=array(),$uniacid=''){

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
				$this->setmoney($orderprice,$_var_0,$uniacid);
			}

		}
		/**
		*	返现打款到余额 记录每次返现金额
		*	$returntype 返现类型 1：会员等级返现 2：单笔订单返现 3：订单累计金额返现 4：队列排列返现
		*/

		public function setReturnCredit($openid = '', $credittype = 'credit1', $credits = 0, $returntype = 1, $log = array())
	    {
	        global $_W;
	        load()->model('mc');
	        $member = m('member')->getMember($openid);
	        $uid = mc_openid2uid($openid);
			
	        if (!empty($uid)) {
	            $value     = pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(
	                ':uid' => $uid
	            ));
	            $newcredit = $credits + $value;
	            if ($newcredit <= 0) {
	                $newcredit = 0;
	            }
	            pdo_update('mc_members', array(
	                $credittype => $newcredit
	            ), array(
	                'uid' => $uid
	            ));
	            if (empty($log) || !is_array($log)) {
	                $log = array(
	                    $uid,
	                    '未记录'
	                );
	            }
	            $data = array(
	                'uid' => $uid,
	                'credittype' => $credittype,
	                'uniacid' => $_W['uniacid'],
	                'num' => $credits,
	                'createtime' => TIMESTAMP,
	                'operator' => intval($log[0]),
	                'remark' => $log[1]
	            );
	            pdo_insert('mc_credits_record', $data);
	        } else {
				
	            $value     = pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('sz_yi_member') . " WHERE  uniacid=:uniacid and openid=:openid limit 1", array(
	                ':uniacid' => $_W['uniacid'],
	                ':openid' => $openid
	            ));

	            $newcredit = $credits + $value;
	            if ($newcredit <= 0) {
	                $newcredit = 0;
	            }
	            pdo_update('sz_yi_member', array(
	                $credittype => $newcredit
	            ), array(
	                'uniacid' => $_W['uniacid'],
	                'openid' => $openid
	            ));
	        }

	        $data_log = array(
					'uniacid' => $_W['uniacid'],
	                'mid' => $member['id'],
	                'openid' => $openid,
	                'money' => $credits,
	                'status' => 1,
	                'returntype' => $returntype,
					'create_time'	=> time()
                );
				pdo_insert('sz_yi_return_log', $data_log);
	        
	    }
	}
}