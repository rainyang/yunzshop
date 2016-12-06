<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('ReturnModel')) {

	class ReturnModel extends PluginModel
	{
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}

		/**
		 * 商品排列全返 执行加入全返队列方法 
		 * $orderid：订单ID
		 * $set：后台设置
		 * $uniacid：公众号ID
		 * 实付款价格
		 */
		public function setGoodsQueue($orderid,$set=array(),$uniacid='') {

			$order_goods = pdo_fetchall("SELECT og.orderid,og.goodsid,og.total,og.price,g.isreturnqueue,o.openid,m.id as mid FROM " . tablename('sz_yi_order') . " o left join " . tablename('sz_yi_member') . " m  on o.openid = m.openid left join " . tablename("sz_yi_order_goods") . " og on og.orderid = o.id  left join " . tablename("sz_yi_goods") . " g on g.id = og.goodsid WHERE o.id = :orderid and o.uniacid = :uniacid and m.uniacid = :uniacid",
				array(':orderid' => $orderid,':uniacid' => $uniacid ) );

			foreach($order_goods as $good){
				if($good['isreturnqueue'] == 1){

					$goods_queue = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order_goods_queue') . " where uniacid = ".$uniacid." and goodsid = ".$good['goodsid']." order by queue desc limit 1" );
					$queuemessages = '';

					for ($i=1; $i <= $good['total'] ; $i++) { 
						$queuenum = $goods_queue['queue']+$i;
						$queuemessages .= $queuenum."、";
						$data = array(
		                    'uniacid' 	=> $uniacid,
		                    'openid' 	=> $good['openid'],
		                    'goodsid' 	=> $good['goodsid'],
		                    'orderid' 	=> $good['orderid'],
		                    'price' 	=> $good['price']/$good['total'],
		                    'queue' 	=> $queuenum,
		                    'create_time' 	=> time()
		                );
		                pdo_insert('sz_yi_order_goods_queue',$data);
		                $queueid = pdo_insertid();

						$goods_returnid = pdo_fetchcolumn("SELECT returnid FROM " . tablename('sz_yi_order_goods_queue') . " where uniacid = ".$uniacid." and goodsid = ".$good['goodsid']." order by returnid desc limit 1" );
						$return_queue = 0;
						if (!empty($goods_returnid)) {
							$return_queue = pdo_fetchcolumn("SELECT queue FROM " . tablename('sz_yi_order_goods_queue') . " where uniacid = ".$uniacid." and goodsid = ".$good['goodsid']." and id = ".$goods_returnid);
						}

						if(($queuenum-$return_queue) >= $set['queue']) {
							$queue = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order_goods_queue') . " where uniacid = ".$uniacid." and goodsid = ".$good['goodsid']." and status = 0 order by queue asc limit 1" );
							pdo_update('sz_yi_order_goods_queue', array('returnid'=>$queueid,'status'=>'1'), array('id' => $queue['id'], 'uniacid' => $uniacid));
							//商品排列全返返现
							$this->setReturnCredit($queue['openid'],'credit2',$queue['price'],'4');
							$queue_price_txt= $set['queue_price'];
							$queue_price_txt = str_replace('[返现金额]', $queue['price'], $queue_price_txt);
							$messages = array(
								'keyword1' => array('value' => $set['queue_title']?$set['queue_title']:'排列返现通知',
									'color' => '#73a68d'),
								'keyword2' => array('value' => $queue_price_txt?$queue_price_txt:'本次返现金额'.$queue['price']."元！",
									'color' => '#73a68d')
							);
							$templateid = $set['templateid'];
							if (!empty($templateid)) {
								m('message')->sendTplNotice($queue['openid'], $templateid, $messages);
							} else {
								m('message')->sendCustomNotice($queue['openid'], $messages);
							}
							//m('message')->sendCustomNotice($queue['openid'], $messages);
						}
					}

						$queuemessages_txt= $set['queuemessages'];
						$queuemessages_txt = str_replace('[排列序号]', $queuemessages, $queuemessages_txt);
						$queue_messages = array(
							'keyword1' => array('value' => $set['add_queue_title']?$set['add_queue_title']:'加入排列通知',
								'color' => '#73a68d'),
							'keyword2' => array('value' => $queuemessages_txt?$queuemessages_txt:"您已加入排列，排列号为".$queuemessages."号！",
								'color' => '#73a68d')
							);

							$templateid = $set['templateid'];
							if (!empty($templateid)) {
								m('message')->sendTplNotice($good['openid'], $templateid, $queue_messages);
							} else {
								m('message')->sendCustomNotice($good['openid'], $queue_messages);
							}
						//m('message')->sendCustomNotice($good['openid'], $queue_messages);

				}
			}
		}
		/**
		 * 会员等级返现 
		 * 商品设置会员等级金额
		 */
		public function setMembeerLevel($orderid,$set=array(),$uniacid='') {
			$order_goods = pdo_fetchall("SELECT og.price,og.total,g.isreturn,g.returns,g.returns2,g.returntype,o.openid,m.id as mid ,m.level, m.agentlevel FROM " . tablename('sz_yi_order') . " o left join " . tablename('sz_yi_member') . " m  on o.openid = m.openid left join " . tablename("sz_yi_order_goods") . " og on og.orderid = o.id  left join " . tablename("sz_yi_goods") . " g on g.id = og.goodsid WHERE o.id = :orderid and o.uniacid = :uniacid and m.uniacid = :uniacid",
				array(':orderid' => $orderid,':uniacid' => $uniacid ));	
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
			if( $money > 0 ) {
				//会员等级返现 
				$this->setReturnCredit($order_goods[0]['openid'],'credit2',$money,'1');
				$member_price_txt = $set['member_price'];
				$member_price_txt = str_replace('[排列序号]', $money, $member_price_txt);
				$member_price_txt = str_replace('[订单ID]', $orderid, $member_price_txt);
				$msg = array(
					'keyword1' => array('value' => $set['member_title']?$set['member_title']:'购物返现通知', 'color' => '#73a68d'), 
					'keyword2' => array('value' => $member_price_txt?$member_price_txt:'[返现金额]'.$money.'元,已存到您的余额', 'color' => '#73a68d')
				);

				$templateid = $set['templateid'];
				if (!empty($templateid)) {
					m('message')->sendTplNotice($order_goods[0]['openid'], $templateid, $msg);
				} else {
					m('message')->sendCustomNotice($order_goods[0]['openid'], $msg);
				}
	        	//m('message')->sendCustomNotice($order_goods[0]['openid'], $msg);

			}
		}
		/*
		* 确认订单执行
		*/
		public function cumulative_order_amount($orderid) {
			global $_W, $_GPC;
			$set = $this->getSet();
			$order = pdo_fetch("SELECT * FROM ".tablename('sz_yi_order')." WHERE id=:id and uniacid=:uniacid", array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			//会员等级返现
			if($set['islevelreturn']) {
				$this->setMembeerLevel($orderid,$set,$_W['uniacid']);
			}
			//排列全返
			if($set['isqueue']) {
				$this->setGoodsQueue($orderid,$set,$_W['uniacid']);
			}
			if ($set['isreturn'] == 1) {
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
				if(!$is_goods_return && empty($order['cashier'])) {
					return false;
				}
				if (empty($order_goods)) {
					return false;
				}
				if($set['returnrule'] == 1) {
					$this->setOrderRule($order_goods,$order_price,$set,$_W['uniacid']);

				}elseif($set['returnrule'] == 2)
				{
					//排除赠送积分
					if ($set['iscumulative'] && $order['credit1'] > 0) {
						$order_price = $order_price - $order['credit1'];
					}

					$this->setOrderMoneyRule($order_goods,$order_price,$set,$_W['uniacid']);
				}
			}
		}

		//单笔订单 加入队列 支付价格
		public function setOrderRule($order_goods,$order_price,$set=array(),$uniacid='')
		{
			$data = array(
                'mid' 			=> $order_goods[0]['mid'],
                'uniacid' 		=> $uniacid,
                'money' 		=> $order_price,
                'returnrule'	=> $set['returnrule'],
                'status' 		=> 1,
				'create_time'	=> time()
            );

			//单笔订单加入队列
			pdo_insert('sz_yi_return_tpm', $data);

			$order_price_txt = $set['order_price'];
			$order_price_txt = str_replace('[订单金额]', $order_price, $order_price_txt);
			$msg = array(
				'keyword1' => array('value' => $set['add_single_title']?$set['add_single_title']:'订单全返通知', 'color' => '#73a68d'), 
				'keyword2' => array('value' => $order_price_txt?$order_price_txt:'[订单返现金额]'.$order_price, 'color' => '#73a68d')
			);
			$templateid = $set['templateid'];
			if (!empty($templateid)) {
				m('message')->sendTplNotice($order_goods[0]['openid'], $templateid, $msg);
			} else {
				m('message')->sendCustomNotice($order_goods[0]['openid'], $msg);
			}
        	//m('message')->sendCustomNotice($order_goods[0]['openid'], $msg);

		}
		//订单累计金额 加入队列 支付价格
		public function setOrderMoneyRule($order_goods,$order_price,$set=array(),$uniacid='')
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
			$this->setmoney($set['orderprice'],$set,$uniacid);

			if ($return_money >= $set['orderprice']) {
				$total_reach_txt = $set['total_reach'];
				$total_reach_txt = str_replace('[标准金额]', $set['orderprice'], $total_reach_txt);
				$text = $total_reach_txt?$total_reach_txt:"您的订单累计金额已经超过".$set['orderprice']."元，每".$set['orderprice']."元可以加入全返机制，等待全返。";
			} else {
				$total_unreached_txt = $set['total_unreached'];
				$total_unreached_txt = str_replace('[缺少金额]', $set['orderprice']-$return_money, $total_unreached_txt);
				$total_unreached_txt = str_replace('[标准金额]', $set['orderprice'], $total_unreached_txt);


					$text = $total_unreached_txt?$total_unreached_txt:"您的订单累计金额还差" . ($set['orderprice']-$return_money) . "元达到".$set['orderprice']."元，订单累计金额每达到".$set['orderprice']."元就可以加入全返机制，等待全返。继续加油！";
				}
				$total_price_txt = $set['total_price'];
				$total_price_txt = str_replace('[累计金额]', $return_money, $total_price_txt);
				$msg = array(
					'keyword1' => array('value' => $set['total_title']?$set['total_title']:'订单金额累计通知', 'color' => '#73a68d'), 
					'keyword2' => array('value' => $total_price_txt?$total_price_txt:'[订单累计金额]'.$return_money, 'color' => '#73a68d'),
					'remark' => array('value' => $text)
				);

				$templateid = $set['templateid'];
				if (!empty($templateid)) {
					m('message')->sendTplNotice($order_goods[0]['openid'], $templateid, $msg);
				} else {
					m('message')->sendCustomNotice($order_goods[0]['openid'], $msg);
				}

	        	//m('message')->sendCustomNotice($order_goods[0]['openid'], $msg);
			
		}

		//单笔订单返现
		public function setOrderReturn($set=array(),$uniacid=''){
			$tmpdir = IA_ROOT . "/addons/sz_yi/tmp/reutrn";
			$return_log = $tmpdir."/return_jog.txt";
			$log_content[] = date("Y-m-d H:i:s")."公众号ID：".$uniacid." 单笔订单返现开始==============\r\n";

			$current_time = time(); // 返现时间

			if($set['islevels'] == 1) {
				//会员等级 分销商等级比例返现
				$level =  array(); 
				if($set['islevel'] == 1) {
					$level = json_decode($set['member'], true); //会员等级返现比例
					$log_content[] = "会员等级返现比例";
					$log_content[] = "\r\n";
				} elseif($set['islevel'] == 2) {
					$level = json_decode($set['commission'], true); //分销商等返现级比例
					$log_content[] = "分销商等返现级比例";
					$log_content[] = "\r\n";
				}

				foreach ($level as $key => $value) {
					$value = !empty($value) ? $value : $set['percentage'];
					$levelid = intval(substr($key, 5)); 
					if($set['islevel'] == 1) {
						$condition = " m.level = '".$levelid."'";
					} elseif($set['islevel'] == 2) {
						$condition = " m.agentlevel = '".$levelid."'";
					}
					$member_record = pdo_fetchall("SELECT r.mid, m.level, m.agentlevel, m.openid FROM "." 
						(SELECT distinct mid, uniacid, returnrule,`delete` FROM " . tablename('sz_yi_return') . " WHERE uniacid = '". $uniacid ."' AND returnrule = '".$set['returnrule']."' AND `delete` =  '0') as r "." 
						LEFT JOIN " . tablename('sz_yi_member') . " m ON ( r.mid = m.id )  WHERE ".$condition);
					$mid = array();
					if($member_record)
					{
						foreach ($member_record as $k => $v) {
							$mid[] = $v['mid'];
						}
						$ratio[$value] = implode(',', $mid);
					}
				}
				if(empty($ratio)){
					return false;
				}
				foreach ($ratio as $percentage => $mids) {
					$log_content[] = $percentage;
					$log_content[] = "\r\n";
					if($set['degression'] == 1)
					{
						$log_content[] = "递减返现";
						$log_content[] = "\r\n";

						pdo_query("update  " . tablename('sz_yi_return') . " set last_money = money - return_money, status=1, return_money = money, updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and money - return_money <= 0.5  and returnrule = '".$set['returnrule']."' and mid = '".$value['mid']."' ");
						pdo_query("update  " . tablename('sz_yi_return') . " set last_money = (money - return_money) * ".$percentage." / 100, return_money = return_money + (money - return_money) * ".$percentage." / 100,updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and money - return_money > 0.5 and returnrule = '".$set['returnrule']."' and mid = '".$value['mid']."' ");

					}else{
						$log_content[] = "单笔返现";
						$log_content[] = "\r\n";
						pdo_query("update  " . tablename('sz_yi_return') . " set last_money = money - return_money, status=1, return_money = money, updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and (money - `return_money`) <= money * ".$percentage." / 100 and returnrule = '".$set['returnrule']."' and mid in (".$mids.") ");
						pdo_query("update  " . tablename('sz_yi_return') . " set return_money = return_money + money * ".$percentage." / 100,last_money = money * ".$percentage." / 100,updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and (money - return_money) > money * ".$percentage." / 100 and returnrule = '".$set['returnrule']."' and mid in (".$mids.") ");
					}
				}

			} else {
				$percentage = $set['percentage'];
				if($set['degression'] == 1)
				{
					$log_content[] = "递减返现";
					$log_content[] = "\r\n";
					pdo_query("update  " . tablename('sz_yi_return') . " set last_money = money - return_money, status=1, return_money = money, updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and money - return_money <= 0.5  and returnrule = '".$set['returnrule']."' ");
					pdo_query("update  " . tablename('sz_yi_return') . " set return_money = return_money + (money - return_money) * ".$percentage." / 100,last_money = (money - return_money) * ".$percentage." / 100,updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and money - return_money > 0.5 and returnrule = '".$set['returnrule']."'");
				}else{
					$log_content[] = "单笔返现";
					$log_content[] = "\r\n";
					pdo_query("update  " . tablename('sz_yi_return') . " set last_money = money - return_money, status=1, return_money = money, updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and (money - `return_money`) <= money * ".$percentage." / 100 and returnrule = '".$set['returnrule']."' ");
					pdo_query("update  " . tablename('sz_yi_return') . " set return_money = return_money + money * ".$percentage." / 100,last_money = money * ".$percentage." / 100,updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and (money - return_money) > money * ".$percentage." / 100 and returnrule = '".$set['returnrule']."' ");
				}
			}

			$return_record = pdo_fetchall("SELECT sum(r.money) as money, sum(r.return_money) as return_money, sum(r.last_money) as last_money,m.openid,count(r.id) as count  FROM " . tablename('sz_yi_return') . " r 
				left join " . tablename('sz_yi_member') . " m on (r.mid = m.id) 
				WHERE r.uniacid = '". $uniacid ."' and r.updatetime = '".$current_time."' and r.returnrule = '".$set['returnrule']."' and r.delete = '0'  group by r.mid");
			$log_content[] = "单笔返现内容：";
			$log_content[] = var_export($return_record,true);
			$log_content[] = "\r\n";
			//单笔订单返现
			$data = array();
			foreach ($return_record as $key => $value) {
				if($value['last_money'] > 0) {
					$return_money_totle = $value['last_money'];
					$surplus_money_totle = $value['money']-$value['return_money'];

					$data[$key]['openid'] = $value['openid'];
					$data[$key]['credit'] = 'credit2';
					$data[$key]['return_money_totle'] = $return_money_totle;
					$data[$key]['type'] = 2;

					//$this->setReturnCredit($value['openid'],'credit2',$return_money_totle,'2');

					$single_message_txt = $set['single_message'];
					$single_message_txt = str_replace('[返现金额]', $return_money_totle, $single_message_txt);
					$single_message_txt = str_replace('[剩余返现金额]', $surplus_money_totle, $single_message_txt);
					$messages = array(
						'keyword1' => array(
							'value' => $set['single_return_title']?$set['single_return_title']:'返现通知',
							'color' => '#73a68d'),
						'keyword2' =>array(
							'value' => $single_message_txt?$single_message_txt:'本次返现金额'.$return_money_totle."元",
							'color' => '#73a68d')
						);
					$templateid = $set['templateid'];
					if (!empty($templateid)) {
						m('message')->sendTplNotice($value['openid'], $templateid, $messages);
					} else {
						m('message')->sendCustomNotice($value['openid'], $messages);
					}
					m('message')->sendCustomNotice($value['openid'], $messages);
				}
			}
			$this->setReturnCredits($data);
			$log_content[] = date("Y-m-d H:i:s")."公众号ID：".$uniacid." 单笔订单返现完成==============\r\n\r\n\r\n\r\n";
			file_put_contents($return_log,$log_content,FILE_APPEND);
		}
		//单笔订单分红金额返现
		public function setAppointReturn($set=array(),$uniacid=''){
			//昨天成交订单的分红金额
			$daytime = strtotime(date("Y-m-d 00:00:00"));
			$stattime = $daytime - 86400;
			$endtime = $daytime - 1;

			$sql = "select g.return_appoint_amount, og.total from ".tablename('sz_yi_order')." o 
			left join ".tablename('sz_yi_order_goods')." og on (o.id = og.orderid) 
			left join ".tablename('sz_yi_goods')." g on (og.goodsid = g.id) 
			left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 
			where 1 and o.status>=3 and o.uniacid={$uniacid} and  o.finishtime >={$stattime} and o.finishtime < {$endtime}  ORDER BY o.finishtime DESC,o.status DESC";
			$dayorder = pdo_fetchall($sql);
	        foreach ($dayorder as $key => $value) {
	            $ordermoney += $value['return_appoint_amount'] * $value['total'];
	        }
	        $ordermoney = floatval($ordermoney);
			$sql = "select sum(money-return_money) from ".tablename('sz_yi_return')."  where `uniacid` = '". $uniacid ."' and `returnrule` = '".$set['returnrule']."' and `delete` = '0'";
			$return_amount = pdo_fetchcolumn($sql);
			$share = $ordermoney / $return_amount;
			$current_time = time();

			pdo_query("update " . tablename('sz_yi_return') . " set last_money = money - return_money, status=1, return_money = money, updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and money - return_money <= ". $share ." * money - return_money and returnrule = '".$set['returnrule']."' ");

			pdo_query("update " . tablename('sz_yi_return') . " set last_money = ". $share ." * (money - return_money), return_money = return_money + ". $share ." * (money - return_money), updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and money - return_money > ". $share ." * (money - return_money) and returnrule = '".$set['returnrule']."'; ");

			$return_record = pdo_fetchall("SELECT sum(r.money) as money, sum(r.return_money) as return_money, sum(r.last_money) as last_money,m.openid,count(r.id) as count  FROM " . tablename('sz_yi_return') . " r 
				left join " . tablename('sz_yi_member') . " m on (r.mid = m.id) 
			 WHERE r.uniacid = '". $uniacid ."' and r.updatetime = '".$current_time."' and r.returnrule = '".$set['returnrule']."' and r.delete = '0'  group by r.mid");
			//单笔订单分红金额返现
			foreach ($return_record as $key => $value) {
				if($value['last_money'] > 0) {
					$return_money_totle = $value['last_money'];
					$surplus_money_totle = $value['money']-$value['return_money'];

					$data[$key]['openid'] = $value['openid'];
					$data[$key]['credit'] = 'credit2';
					$data[$key]['return_money_totle'] = $return_money_totle;
					$data[$key]['type'] = 2;
					//$this->setReturnCredit($value['openid'],'credit2',$return_money_totle,'2');

					$total_messsage_txt = $set['total_messsage'];
					$total_messsage_txt = str_replace('[返现金额]', $return_money_totle, $total_messsage_txt);
					$total_messsage_txt = str_replace('[剩余返现金额]', $surplus_money_totle, $total_messsage_txt);
					$messages = array(
						'keyword1' => array(
							'value' => $set['total_return_title']?$set['total_return_title']:'返现通知',
							'color' => '#73a68d'),
						'keyword2' =>array(
							'value' => $total_messsage_txt?$total_messsage_txt:'本次返现金额'.$return_money_totle."元",
							'color' => '#73a68d')
					);
					m('message')->sendCustomNotice($value['openid'], $messages);
				}
			}
			$this->setReturnCredits($data);
		}
		//订单累计金额返现
		public function setOrderMoneyReturn($set=array(),$uniacid=''){
			$tmpdir = IA_ROOT . "/addons/sz_yi/tmp/reutrn";
			$return_log = $tmpdir."/return_jog.txt";
			$log_content[] = date("Y-m-d H:i:s")."公众号ID：".$uniacid." 订单累计返现开始==============\r\n";
			//昨天成交金额
			$daytime = strtotime(date("Y-m-d 00:00:00"));
			$stattime = $daytime - 86400;
			$endtime = $daytime - 1;
			if ($set['isprofit']) {
				$sql = "select o.id, o.price, g.marketprice, g.costprice, og.total from ".tablename('sz_yi_order')." o 
				left join ".tablename('sz_yi_order_goods')." og on (o.id = og.orderid) 
				left join ".tablename('sz_yi_goods')." g on (og.goodsid = g.id) 
				left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 
				where 1 and o.status>=3 and o.uniacid={$uniacid} and  o.finishtime >={$stattime} and o.finishtime < {$endtime}  ORDER BY o.finishtime DESC,o.status DESC";
				$dayorder = pdo_fetchall($sql);

		        foreach ($dayorder as $key => $value) {
		            $ordermoney += $value['price'] - $value['costprice'] * $value['total'];
		        }
			} else {
				$sql = "select sum(o.price) from ".tablename('sz_yi_order')." o left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 where 1 and o.status>=3 and o.uniacid={$uniacid} and  o.finishtime >={$stattime} and o.finishtime < {$endtime}  ORDER BY o.finishtime DESC,o.status DESC";
				$ordermoney = pdo_fetchcolumn($sql);
			}
			
			
			$ordermoney = floatval($ordermoney);
			$log_content[] = "昨日成交金额：".$ordermoney."\r\n";
			$r_ordermoney = $ordermoney * $set['percentage'] / 100;//可返利金额
			$log_content[] = "可返现金额：".$r_ordermoney."\r\n";
			//返利队列
			$queue_count = pdo_fetchcolumn("select count(1) from " . tablename('sz_yi_return') . " where uniacid = '". $uniacid ."' and status = 0 and `delete` = '0' and returnrule = '".$set['returnrule']."'");
			$log_content[] = "可返现队列数：".$queue_count."\r\n";
			if ($r_ordermoney>0 && $queue_count) {
				$r_each = $r_ordermoney / $queue_count;//每个队列返现金额
				$r_each = sprintf("%.2f", $r_each);
				$log_content[] = "每个队列返现金额：".$r_each."\r\n";
				$current_time = time();
				pdo_query("update  " . tablename('sz_yi_return') . " set last_money = money - return_money, status=1, return_money = money, updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and (money - `return_money`) <= '".$r_each."' and returnrule = '".$set['returnrule']."' ");
				pdo_query("update  " . tablename('sz_yi_return') . " set return_money = return_money + '".$r_each."',last_money = '".$r_each."',updatetime = '".$current_time."' WHERE uniacid = '". $uniacid ."' and status=0 and `delete` = '0' and (money - `return_money`) > '".$r_each."' and returnrule = '".$set['returnrule']."' ");
				$return_record = pdo_fetchall("SELECT sum(r.money) as money, sum(r.return_money) as return_money, sum(r.last_money) as last_money,m.openid,count(r.id) as count  FROM " . tablename('sz_yi_return') . " r 
					left join " . tablename('sz_yi_member') . " m on (r.mid = m.id) 
				 WHERE r.uniacid = '". $uniacid ."' and r.updatetime = '".$current_time."' and r.returnrule = '".$set['returnrule']."' and r.delete = '0'  group by r.mid");
				$log_content[] ="SELECT sum(r.money) as money, sum(r.return_money) as return_money, sum(r.last_money) as last_money,m.openid,count(r.id) as count  FROM " . tablename('sz_yi_return') . " r 
					left join " . tablename('sz_yi_member') . " m on (r.mid = m.id) 
				 WHERE r.uniacid = '". $uniacid ."' and r.updatetime = '".$current_time."' and r.returnrule = '".$set['returnrule']."' and r.delete = '0'  group by r.mid \r\n";
				$log_content[] = "订单累计金额返现内容：".var_export($return_record,true)."\r\n";
				//订单累计金额返现
				foreach ($return_record as $key => $value) {
					if($value['last_money'] > 0) {
						$return_money_totle = $value['last_money'];
						$surplus_money_totle = $value['money']-$value['return_money'];

						$data[$key]['openid'] = $value['openid'];
						$data[$key]['credit'] = 'credit2';
						$data[$key]['return_money_totle'] = $return_money_totle;
						$data[$key]['type'] = 3;

						//$this->setReturnCredit($value['openid'],'credit2',$return_money_totle,'3');
						$total_messsage_txt = $set['total_messsage'];
						$total_messsage_txt = str_replace('[返现金额]', $return_money_totle, $total_messsage_txt);
						$total_messsage_txt = str_replace('[剩余返现金额]', $surplus_money_totle, $total_messsage_txt);
						$messages = array(
							'keyword1' => array(
							'value' => $set['total_return_title']?$set['total_return_title']:'返现通知',
							'color' => '#73a68d'),
							'keyword2' =>array(
							'value' => $total_messsage_txt?$total_messsage_txt:'本次返现金额'.$return_money_totle."元",
							'color' => '#73a68d')
						);
						$templateid = $set['templateid'];
						if (!empty($templateid)) {
							m('message')->sendTplNotice($value['openid'], $templateid, $messages);
						} else {
							m('message')->sendCustomNotice($value['openid'], $messages);
						}
						//m('message')->sendCustomNotice($value['openid'], $messages);
					}
				}
				$this->setReturnCredits($data);	
			}

			$log_content[] = date("Y-m-d H:i:s")."公众号ID：".$uniacid." 订单累计返现完成==============\r\n\r\n";
			file_put_contents($return_log,$log_content,FILE_APPEND);
		}

		// 查询可参加返利的 加入返利队列
		public function setmoney($orderprice,$set=array(),$uniacid=''){

			$data_money = pdo_fetchall("select * from " . tablename('sz_yi_return_money') . " where uniacid = '". $uniacid . "' and money >= '" . $orderprice . "' ");
			if($data_money){
				foreach ($data_money as $key => $value) {
					if($value['money'] >= $orderprice){
						$data = array(
							'uniacid' 		=> $value['uniacid'],
							'mid' 			=> $value['mid'],
							'money' 		=> $orderprice,
							'returnrule' 	=> $set['returnrule'],
							'status' 		=> 1,
							'create_time'	=> time()
						);
						//订单累计金额加入队列
						pdo_insert('sz_yi_return_tpm', $data);
						pdo_update('sz_yi_return_money', array('money'=>$value['money']-$orderprice), array('id' => $value['id'], 'uniacid' => $uniacid));	
					}
				}
				$this->setmoney($orderprice,$set,$uniacid);
			}

		}

		/**
		*	返现打款到余额 记录每次返现金额
		*	$data[type] 返现类型 1：会员等级返现 2：单笔订单返现 3：订单累计金额返现 4：队列排列返现
		*/
		public function setReturnCredits($data = array())
	    {
	        global $_W;
	        load()->model('mc');
	        if (!empty($data)) {
		        $sql = '';
		        foreach ($data as $key => $value) {
		        	$member = array();
		        	$uid = "";
		        	$member = m('member')->getMember($value['openid']);
		        	$uid = mc_openid2uid($value['openid']);
					if (!empty($uid)) {
						$credit     = pdo_fetchcolumn("SELECT ".$value['credit']." FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(
		                		':uid' => $uid
		            	));
						$newcredit = $value['return_money_totle'] + $credit;
			            if ($newcredit <= 0) {
			                $newcredit = 0;
			            }
			            //更新金额
						$sql .= " UPDATE " . tablename('mc_members') . " SET " . $value['credit'] . " = " . $newcredit . " WHERE uid = '".$uid."';";
			            //添加日志
			            $sql .= " INSERT INTO " . tablename('mc_members') . " (`uid`, `uniacid`, `credittype`, `num`, `operator`,  `createtime`, `remark`) VALUES ('".$uid."','".$_W['uniacid']."','".$value['credit']."','".$value['return_money_totle']."','".intval($log[0])."','".TIMESTAMP.",".$log[1]."');";
					} else {
			            $credit     = pdo_fetchcolumn("SELECT ".$value['credit']." FROM " . tablename('sz_yi_member') . " WHERE  uniacid=:uniacid and openid=:openid limit 1", array(
			                ':uniacid' => $_W['uniacid'],
			                ':openid' => $value['openid']
			            ));
			            $newcredit = $value['return_money_totle'] + $credit;
			            if ($newcredit <= 0) {
			                $newcredit = 0;
			            }
			            $sql .= " UPDATE " . tablename('sz_yi_member') . " SET " . $value['credit'] . " = " . $newcredit . " WHERE uniacid = '".$_W['uniacid']."' AND openid = '".$value['openid']."';";
					}
			        $sql .= "INSERT INTO `ims_sz_yi_return_log`(`uniacid`, `mid`, `openid`, `money`, `status`, `returntype`, `create_time`) VALUES ('".$_W['uniacid']."','".$member['id']."','".$value['openid']."','".$value['return_money_totle']."','1','".$value['type']."','".TIMESTAMP."');";
					//pdo_insert('sz_yi_return_log', $data_log);
		        }
				pdo_fetch($sql);
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

	    //全返队列临时表处理
	    public function setDelay( $set = array(), $uniacid = '' ) {
	    	if ( isset($set['delay']) && $set['delay'] > 0 ) {
	    		$days = intval($set['delay']);
				$daytimes = 3600 * $days;
				$delay_queue = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_return_tpm') . " where uniacid = :uniacid and status = :status AND create_time + " . $daytimes . " <= unix_timestamp() ",array( ':uniacid'	=> $uniacid, ':status' => '1' ));
	    	} else {
	    		$delay_queue = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_return_tpm') . " where uniacid = :uniacid and status = :status",array( ':uniacid'	=> $uniacid, ':status' => '1' ));
	    	}
    		foreach ($delay_queue as $key => $value) {
				$queue_data = array(
					'uniacid' 		=> $value['uniacid'],
					'mid' 			=> $value['mid'],
					'money' 		=> $value['money'],
					'returnrule' 	=> $value['returnrule'],
					'create_time'	=> time()
				);
				//添加队列
				pdo_insert('sz_yi_return', $queue_data);
				$queueid = pdo_insertid();
				//修改临时队列
	            pdo_update('sz_yi_return_tpm', array(
	                'status' => 2,
	                'update_time' => time(),
	                'queue' => $queueid
	            ), array(
	                'uniacid' => $uniacid,
	                'id' => $value['id']
	            ));
    		}
	    }
		public function autoexec( $uniacid ) {
			global $_W, $_GPC;
			$_W['uniacid'] = $uniacid;
			set_time_limit(0);
			load()->func('file');
			$tmpdir = IA_ROOT . "/addons/sz_yi/tmp/reutrn";
			$return_log = $tmpdir."/return_log.txt";
			$log_content = array();
			$log_content[] = date("Y-m-d H:i:s")."返现开始========================\r\n";
        	$log_content[] = "当前域名：".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."\r\n";
            $tmpdirs = IA_ROOT . "/addons/sz_yi/tmp/reutrn/".date("Ymd");
            if (!is_dir($tmpdirs)) {
                mkdirs($tmpdirs);
            }
			$set = m('plugin')->getpluginSet('return', $_W['uniacid']);
            //延期返现队列处理
            $this->setDelay($set, $_W['uniacid']);
            $validation      = $tmpdirs."/".date("Ymd").$_W['uniacid'].".txt";
            if (!file_exists($validation)) {
                if (!empty($set)) {
                	//延期返现队列处理
                	$this->setDelay($set, $_W['uniacid']);
                    $isexecute = false;
                    if ($set['returnlaw'] == 1) {
                        $log_content[] = '返现规律：按天返现，每天：'.$set['returntime']."返现\r\n";
                        if (date('H') == $set['returntime']) {
                            if (!isset($set['current_d']) || $set['current_d'] != date('d')) {
                                //$data  = array_merge($set, array('current_d'=>date('d')));
                                $set['current_d'] = date('d');
                                $this->updateSet($set);
                                $isexecute = true;
                            }
                        }
                    } elseif ($set['returnlaw'] == 2) {
                        $log_content[] = "返现规律：按月返现！\r\n";
                        if (!isset($set['current_m']) || $set['current_m'] != date('m')) {
                            //$data  = array_merge($set, array('current_m'=>date('m')));
                            $set['current_m'] = date('m');
                            $this->updateSet($set);
                            $isexecute = true;
                        }
                    } elseif ($set['returnlaw'] == 3) {
                        $log_content[] = "返现规律：按周返现！\r\n";
                        if (date("w") == $set['returntimezhou']) {
                            if (!isset($set['current_d']) || $set['current_d'] != date('d')) {
                                $set['current_d'] = date('d');
                                $this->updateSet($set);
                                $isexecute = true;
                            }
                        }
                    }
                    if (($set["isreturn"] || $set["isqueue"]) && $isexecute) {
                        touch($validation);
                        $log_content[] = "当前可以返现\r\n";
                        if ($set["returnrule"] == 1) {
                        	if ($set["isappoint"] == 1) {
	                            //单笔订单 分红金额返现
	                            $log_content[] = "返现类型：单笔订单 分红金额返现\r\n";
	                            $this->setAppointReturn($set, $_W['uniacid']);
                        	} else {
	                            //单笔订单
	                            $log_content[] = "返现类型：单笔订单返现\r\n";
	                            $this->setOrderReturn($set, $_W['uniacid']);
                        	}

                        } else {
                            //订单累计金额
                            $log_content[] = "返现类型：订单累计金额返现\r\n";
                            $this->setOrderMoneyReturn($set, $_W['uniacid']);
                        }
                    } else {
                        $log_content[] = "当前不可返现\r\n";
                    }
                }
				$log_content[] = "公众号ID：".$_W['uniacid']."结束-----------\r\n\r\n";
            } else {
                $log_content[] = "公众号ID：".$_W['uniacid'].date("Y-m-d")."已返现\r\n\r\n";
            }
            $log_content[] = date("Y-m-d H:i:s")."返现任务执行完成===================\r\n \r\n \r\n";
        	file_put_contents($return_log,$log_content,FILE_APPEND);
		}
	}
}