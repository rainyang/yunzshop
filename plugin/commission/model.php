<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
define('TM_COMMISSION_AGENT_NEW', 'commission_agent_new');
define('TM_COMMISSION_ORDER_PAY', 'commission_order_pay');
define('TM_COMMISSION_ORDER_FINISH', 'commission_order_finish');
define('TM_COMMISSION_APPLY', 'commission_apply');
define('TM_COMMISSION_CHECK', 'commission_check');
define('TM_COMMISSION_PAY', 'commission_pay');
define('TM_COMMISSION_UPGRADE', 'commission_upgrade');
define('TM_COMMISSION_BECOME', 'commission_become');
if (!class_exists('CommissionModel')) {
	class CommissionModel extends PluginModel
	{
		//基础设置
		public function getSet()
		{
			$set = parent::getSet();

			$set['texts'] = array('agent' => empty($set['texts']['agent']) ? '分销商' : $set['texts']['agent'], 'shop' => empty($set['texts']['shop']) ? '小店' : $set['texts']['shop'], 'myshop' => empty($set['texts']['myshop']) ? '我的小店' : $set['texts']['myshop'], 'center' => empty($set['texts']['center']) ? '分销中心' : $set['texts']['center'], 'become' => empty($set['texts']['become']) ? '成为分销商' : $set['texts']['become'], 'withdraw' => empty($set['texts']['withdraw']) ? '提现' : $set['texts']['withdraw'], 'commission' => empty($set['texts']['commission']) ? '佣金' : $set['texts']['commission'], 'commission1' => empty($set['texts']['commission1']) ? '分销佣金' : $set['texts']['commission1'], 'commission_total' => empty($set['texts']['commission_total']) ? '累计佣金' : $set['texts']['commission_total'], 'commission_ok' => empty($set['texts']['commission_ok']) ? '可提现佣金' : $set['texts']['commission_ok'], 'commission_apply' => empty($set['texts']['commission_apply']) ? '已申请佣金' : $set['texts']['commission_apply'], 'commission_check' => empty($set['texts']['commission_check']) ? '待打款佣金' : $set['texts']['commission_check'], 'commission_lock' => empty($set['texts']['commission_lock']) ? '未结算佣金' : $set['texts']['commission_lock'], 'commission_detail' => empty($set['texts']['commission_detail']) ? '佣金明细' : $set['texts']['commission_detail'], 'commission_pay' => empty($set['texts']['commission_pay']) ? '成功提现佣金' : $set['texts']['commission_pay'], 'order' => empty($set['texts']['order']) ? '分销订单' : $set['texts']['order'], 'myteam' => empty($set['texts']['myteam']) ? '我的团队' : $set['texts']['myteam'], 'c1' => empty($set['texts']['c1']) ? '一级' : $set['texts']['c1'], 'c2' => empty($set['texts']['c2']) ? '二级' : $set['texts']['c2'], 'c3' => empty($set['texts']['c3']) ? '三级' : $set['texts']['c3'], 'mycustomer' => empty($set['texts']['mycustomer']) ? '我的客户' : $set['texts']['mycustomer'],);

			return $set;
		}
		//订单佣金计算
		public function calculate($orderid = 0, $update = true)
		{
			global $_W;
			$set = $this->getSet();
			$levels = $this->getLevels();

			$orders = pdo_fetch('select agentid, period_num from ' . tablename('sz_yi_order') . ' where id=:id limit 1', array(':id' => $orderid));
			$agentid = $orders['agentid'];
			$goods = pdo_fetchall('select og.id,og.realprice,og.total,g.type,g.hascommission,g.nocommission, g.commission1_rate,g.commission1_pay,g.commission2_rate,g.commission2_pay,g.commission3_rate,g.commission3_pay,og.commissions,og.optionid,g.productprice,g.marketprice,g.costprice,g.id as goodsid from ' . tablename('sz_yi_order_goods') . '  og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id = og.goodsid' . ' where og.orderid=:orderid and og.uniacid=:uniacid', array(':orderid' => $orderid, ':uniacid' => $_W['uniacid']));
			//阶梯价格插件
			$isladder = false;
			if (p('ladder')) {
			    $ladder_set = p('ladder')->getSet();
			    if ($ladder_set['isladder']) {
			        $isladder = true;   
			    }
			}
			if ($set['level'] > 0) {			
				foreach ($goods as &$cinfo) {
					//计算阶梯价格
		            if ($isladder) {
		                $ladders = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_ladder') . " WHERE goodsid = :id limit 1", array(
		                        ':id' => $cinfo['goodsid']
		                    ));
		                if ($ladders) {
		                    $ladders = unserialize($ladders['ladders']);
		                    $laddermoney = m('goods')->getLaderMoney($ladders,$cinfo['total']);
		                    $cinfo['marketprice'] = $laddermoney > 0 ? $laddermoney : $cinfo['marketprice'];
		                }
		            }
					$price = $this->calculate_method($cinfo,$orders['period_num']);
					if(p('hotel')&& $goods[0]['type']=='99'){
				    	$order = pdo_fetch('select id,goodsprice from ' . tablename('sz_yi_order').' where id=:id and uniacid=:uniacid', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
				    	$price =$order['goodsprice'];
			        }
					//$price = $cinfo['realprice'];
					if (empty($cinfo['nocommission']) && $price > 0) {
						if ($cinfo['hascommission'] == 1) {
							$cinfo['commission1'] = array('default' => $set['level'] >= 1 ? ($cinfo['commission1_rate'] > 0 ? round($cinfo['commission1_rate'] * $price / 100, 2) . "" : round($cinfo['commission1_pay'] * $cinfo['total'], 2)) : 0);
							$cinfo['commission2'] = array('default' => $set['level'] >= 2 ? ($cinfo['commission2_rate'] > 0 ? round($cinfo['commission2_rate'] * $price / 100, 2) . "" : round($cinfo['commission2_pay'] * $cinfo['total'], 2)) : 0);
							$cinfo['commission3'] = array('default' => $set['level'] >= 3 ? ($cinfo['commission3_rate'] > 0 ? round($cinfo['commission3_rate'] * $price / 100, 2) . "" : round($cinfo['commission3_pay'] * $cinfo['total'], 2)) : 0);
							foreach ($levels as $level) {
								$cinfo['commission1']['level' . $level['id']] = $cinfo['commission1_rate'] > 0 ? round($cinfo['commission1_rate'] * $price / 100, 2) . "" : round($cinfo['commission1_pay'] * $cinfo['total'], 2);
								$cinfo['commission2']['level' . $level['id']] = $cinfo['commission2_rate'] > 0 ? round($cinfo['commission2_rate'] * $price / 100, 2) . "" : round($cinfo['commission2_pay'] * $cinfo['total'], 2);
								$cinfo['commission3']['level' . $level['id']] = $cinfo['commission3_rate'] > 0 ? round($cinfo['commission3_rate'] * $price / 100, 2) . "" : round($cinfo['commission3_pay'] * $cinfo['total'], 2);
							}
						} else {
							$cinfo['commission1'] = array('default' => $set['level'] >= 1 ? round($set['commission1'] * $price / 100, 2) . "" : 0);
							$cinfo['commission2'] = array('default' => $set['level'] >= 2 ? round($set['commission2'] * $price / 100, 2) . "" : 0);
							$cinfo['commission3'] = array('default' => $set['level'] >= 3 ? round($set['commission3'] * $price / 100, 2) . "" : 0);
							foreach ($levels as $level) {
								$cinfo['commission1']['level' . $level['id']] = $set['level'] >= 1 ? round($level['commission1'] * $price / 100, 2) . "" : 0;
								$cinfo['commission2']['level' . $level['id']] = $set['level'] >= 2 ? round($level['commission2'] * $price / 100, 2) . "" : 0;
								$cinfo['commission3']['level' . $level['id']] = $set['level'] >= 3 ? round($level['commission3'] * $price / 100, 2) . "" : 0;
							}
						}
					} else {
						$cinfo['commission1'] = array('default' => 0);
						$cinfo['commission2'] = array('default' => 0);
						$cinfo['commission3'] = array('default' => 0);
						foreach ($levels as $level) {
							$cinfo['commission1']['level' . $level['id']] = 0;
							$cinfo['commission2']['level' . $level['id']] = 0;
							$cinfo['commission3']['level' . $level['id']] = 0;
						}
					}
					if ($update) {
						$commissions = array('level1' => 0, 'level2' => 0, 'level3' => 0);
						if (!empty($agentid)) {
							$m1 = m('member')->getMember($agentid);
							if ($m1['isagent'] == 1 && $m1['status'] == 1) {
								$l1 = $this->getLevel($m1['openid']);
								$commissions['level1'] = empty($l1) ? round($cinfo['commission1']['default'], 2) : round($cinfo['commission1']['level' . $l1['id']], 2);
								if (!empty($m1['agentid'])) {
									$m2 = m('member')->getMember($m1['agentid']);
									$l2 = $this->getLevel($m2['openid']);
									$commissions['level2'] = empty($l2) ? round($cinfo['commission2']['default'], 2) : round($cinfo['commission2']['level' . $l2['id']], 2);
									if (!empty($m2['agentid'])) {
										$m3 = m('member')->getMember($m2['agentid']);
										$l3 = $this->getLevel($m3['openid']);
										$commissions['level3'] = empty($l3) ? round($cinfo['commission3']['default'], 2) : round($cinfo['commission3']['level' . $l3['id']], 2);
									}
								}
							}
						}
						pdo_update('sz_yi_order_goods', 
							array(
								'commission1' => iserializer($cinfo['commission1']), 
								'commission2' => iserializer($cinfo['commission2']), 
								'commission3' => iserializer($cinfo['commission3']), 
								'commissions' => iserializer($commissions), 
								'nocommission' => $cinfo['nocommission']), 
							array(
								'id' => $cinfo['id']
							));
					}
				}
			    			
				
				unset($cinfo);
			}
			return $goods;
		}

		//Author:ym Date:2016-05-06 Content:分成方式计算		
		public function calculate_method($order_goods, $period_num = ''){
			global $_W;
			$set = $this->getSet();
			$realprice = $order_goods['realprice'];
			if(empty($set['culate_method']) || $period_num){
				return $realprice;
			}else{
				
				if($order_goods['optionid'] != 0){
					$option = pdo_fetch('select productprice,marketprice,costprice,option_ladders from ' . tablename('sz_yi_goods_option') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $order_goods['optionid'], ':uniacid' => $_W['uniacid']));
					//阶梯价格插件
					if (p('ladder')) {
					    $ladder_set = p('ladder')->getSet();
					    if ($ladder_set['isladder']) {
					        $ladders = unserialize($option['option_ladders']);
			                if ($ladders) {
			                    $laddermoney = m('goods')->getLaderMoney($ladders,$order_goods['total']);
			                    $option['marketprice'] = $laddermoney > 0 ? $laddermoney : $option['marketprice'];
			                }  
					    }
					}
					$productprice = $option['productprice'] * $order_goods['total'];	//原价
					$marketprice  = $option['marketprice'] * $order_goods['total'];		//现价
					$costprice    = $option['costprice'] * $order_goods['total'];		//成本价
				}else{

					$productprice = $order_goods['productprice'] * $order_goods['total'];		//原价
					$marketprice  = $order_goods['marketprice'] * $order_goods['total'];		//现价
					$costprice    = $order_goods['costprice'] * $order_goods['total'];			//成本价
				}
				if($set['culate_method'] == 1){
					return $productprice;
				}else if($set['culate_method'] == 2){
					return $marketprice;
				}else if($set['culate_method'] == 3){
					return $costprice;
				}else if($set['culate_method'] == 4){
					$price = $realprice - $costprice;
					return $price > 0 ? $price : 0;
				}
			}
		}
		//查询订单分佣金额
		public function getOrderCommissions($orderid = 0, $ordergoodsid = 0)
		{
			global $_W;
			$set = $this->getSet();
			$agentid = pdo_fetchcolumn('select agentid from ' . tablename('sz_yi_order') . ' where id=:id limit 1', array(':id' => $orderid));
			$commissions = pdo_fetch('select commission1,commission2,commission3 from ' . tablename('sz_yi_order_goods') . ' where id=:id and orderid=:orderid and uniacid=:uniacid and nocommission=0 limit 1', array(':id' => $ordergoodsid, ':orderid' => $orderid, ':uniacid' => $_W['uniacid']));
			$OrderCommissions = array('level1' => 0, 'level2' => 0, 'level3' => 0);
			if ($set['level'] > 0) {
				$commission1 = iunserializer($commissions['commission1']);
				$commission2 = iunserializer($commissions['commission2']);
				$commission3 = iunserializer($commissions['commission3']);
				if (!empty($agentid)) {
					$m1 = m('member')->getMember($agentid);
					if ($m1['isagent'] == 1 && $m1['status'] == 1) {
						$level1 = $this->getLevel($m1['openid']);
						$OrderCommissions['level1'] = empty($level1) ? round($commission1['default'], 2) : round($commission1['level' . $level1['id']], 2);
						if (!empty($m1['agentid'])) {
							$m2 = m('member')->getMember($m1['agentid']);
							$level2 = $this->getLevel($m2['openid']);
							$OrderCommissions['level2'] = empty($level2) ? round($commission2['default'], 2) : round($commission2['level' . $level2['id']], 2);
							if (!empty($m2['agentid'])) {
								$m3 = m('member')->getMember($m2['agentid']);
								$level3 = $this->getLevel($m3['openid']);
								$OrderCommissions['level3'] = empty($level3) ? round($commission3['default'], 2) : round($commission3['level' . $level3['id']], 2);
							}
						}
					}
				}
			}
			return $OrderCommissions;
		}
		//获取分销商详细信息
		public function getInfo($openid, $options = null)
		{
			if (empty($options) || !is_array($options)) {
				$options = array();
			}
			global $_W;
			$set = $this->getSet();
			$level = intval($set['level']);
			$member = m('member')->getMember($openid);
			$agentLevel = $this->getLevel($openid);
			$time = time();
			$day_times = intval($set['settledays']) * 3600 * 24;
			$withdraw_day = intval($set['withdraw_day']) * 3600 * 24;
			$agentcount = 0;
			$ordercount0 = 0;
			$ordermoney0 = 0;
			$ordercount = 0;
			$ordermoney = 0;
			$ordercount3 = 0;
			$ordermoney3 = 0;
			$commission_total = 0;
			$commission_ok = 0;
			$commission_apply = 0;
			$commission_check = 0;
			$commission_lock = 0;
			$commission_pay = 0;
			$level1 = 0;
			$level2 = 0;
			$level3 = 0;
			$order10 = 0;
			$order20 = 0;
			$order30 = 0;
			$order1 = 0;
			$order2 = 0;
			$order3 = 0;
			$order13 = 0;
			$order23 = 0;
			$order33 = 0;
			$order13money = 0;
			$order23money = 0;
			$order33money = 0;
			$myordermoney = 0;
			$myordercount = 0;
			if ($level >= 1) {
				if (in_array('ordercount0', $options)) {
					$level1_ordercount = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=0 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					if(p('hotel')){
					$level1_ordercount = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=0 and o.status<>4 and o.status<>5 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					}
					$order10 += $level1_ordercount['ordercount'];
					$ordercount0 += $level1_ordercount['ordercount'];
					$ordermoney0 += $level1_ordercount['ordermoney'];
				}
				if (in_array('ordercount', $options)) {
					$level1_ordercount = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=1 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					if(p('hotel')){
					$level1_ordercount = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=1 and o.status<>4 and o.status<>5 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					}
					$order1 += $level1_ordercount['ordercount'];
					$ordercount += $level1_ordercount['ordercount'];
					$ordermoney += $level1_ordercount['ordermoney'];
				}
				if (in_array('ordercount3', $options)) {
					$level1_ordercount3 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=3  and o.status<>5 and o.status<>4 and o.status<>6  and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					$order13 += $level1_ordercount3['ordercount'];
					$ordercount3 += $level1_ordercount3['ordercount'];
					$ordermoney3 += $level1_ordercount3['ordermoney'];
					$order13money += $level1_ordercount3['ordermoney'];
				}
				if (in_array('total', $options)) {
					$level1_commissions = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					if(p('hotel')){
					$level1_commissions = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=1 and o.status<>4  and o.status<>5 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));

					}
					foreach ($level1_commissions as $c) {
						$commission = iunserializer($c['commissions']);
						$commission1 = iunserializer($c['commission1']);
						if (empty($commission)) {
							$commission_total += isset($commission1['level' . $agentLevel['id']]) ? $commission1['level' . $agentLevel['id']] : $commission1['default'];
						} else {
							$commission_total += isset($commission['level1']) ? floatval($commission['level1']) : 0;
						}
					}
				}
				if (in_array('ok', $options)) {
					$level1_commissions = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . " where o.agentid=:agentid and o.status>=3 and o.status<>6  and o.status<>4 and o.status<>5 and og.nocommission=0 and ({$time} - o.finishtime > {$day_times}) and og.status1=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					foreach ($level1_commissions as $c) {
						$commission = iunserializer($c['commissions']);
						$commission1 = iunserializer($c['commission1']);
						if (empty($commission)) {
							$commission_ok += isset($commission1['level' . $agentLevel['id']]) ? $commission1['level' . $agentLevel['id']] : $commission1['default'];
						} else {
							$commission_ok += isset($commission['level1']) ? $commission['level1'] : 0;
						}
					}
				}
				if (in_array('lock', $options)) {
					$level1_commissions1 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . " where o.agentid=:agentid and o.status>=3 and  o.status<>6 and o.status<>4 and o.status<>5 and og.nocommission=0 and ({$time} - o.finishtime <= {$day_times})  and og.status1=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					foreach ($level1_commissions1 as $c) {
						$commission = iunserializer($c['commissions']);
						$commission1 = iunserializer($c['commission1']);
						if (empty($commission)) {
							$commission_lock += isset($commission1['level' . $agentLevel['id']]) ? $commission1['level' . $agentLevel['id']] : $commission1['default'];
						} else {
							$commission_lock += isset($commission['level1']) ? $commission['level1'] : 0;
						}
					}
				}
				if (in_array('apply', $options)) {
					$level1_commissions2 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3  and o.status<>6 and o.status<>4 and og.status1=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					foreach ($level1_commissions2 as $c) {
						$commission = iunserializer($c['commissions']);
						$commission1 = iunserializer($c['commission1']);
						if (empty($commission)) {
							$commission_apply += isset($commission1['level' . $agentLevel['id']]) ? $commission1['level' . $agentLevel['id']] : $commission1['default'];
						} else {
							$commission_apply += isset($commission['level1']) ? $commission['level1'] : 0;
						}
					}
				}
				if (in_array('check', $options)) {
					$level1_commissions2 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and  o.status<>6  and o.status<>4   and og.status1=2 and og.nocommission=0 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					foreach ($level1_commissions2 as $c) {
						$commission = iunserializer($c['commissions']);
						$commission1 = iunserializer($c['commission1']);
						if (empty($commission)) {
							$commission_check += isset($commission1['level' . $agentLevel['id']]) ? $commission1['level' . $agentLevel['id']] : $commission1['default'];
						} else {
							$commission_check += isset($commission['level1']) ? $commission['level1'] : 0;
						}
					}
				}
				if (in_array('pay', $options)) {
					$level1_commissions2 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and  o.status<>6 and o.status<>4 and o.status<>5 and og.status1=3 and og.nocommission=0 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					foreach ($level1_commissions2 as $c) {
						$commission = iunserializer($c['commissions']);
						$commission1 = iunserializer($c['commission1']);
						if (empty($commission)) {
							$commission_pay += isset($commission1['level' . $agentLevel['id']]) ? $commission1['level' . $agentLevel['id']] : $commission1['default'];
						} else {
							$commission_pay += isset($commission['level1']) ? $commission['level1'] : 0;
						}
					}
				}
				$level1_agentids = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']), 'id');
				$level1 = count($level1_agentids);
				$agentcount += $level1;
			}
			if ($level >= 2) {
				if ($level1 > 0) {
					if (in_array('ordercount0', $options)) {
						$level2_ordercount = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ')  and o.status>=0 and o.status<>4 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$order20 += $level2_ordercount['ordercount'];
						$ordercount0 += $level2_ordercount['ordercount'];
						$ordermoney0 += $level2_ordercount['ordermoney'];
					}
					if (in_array('ordercount', $options)) {
						$level2_ordercount = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ')  and o.status>=1 and o.status<>4 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$order2 += $level2_ordercount['ordercount'];
						$ordercount += $level2_ordercount['ordercount'];
						$ordermoney += $level2_ordercount['ordermoney'];
					}
					if (in_array('ordercount3', $options)) {
						$level2_ordercount3 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ')  and o.status>=3 and o.status<>6 and o.status<>4 and o.status<>5 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$order23 += $level2_ordercount3['ordercount'];
						$ordercount3 += $level2_ordercount3['ordercount'];
						$ordermoney3 += $level2_ordercount3['ordermoney'];
						$order23money += $level2_ordercount3['ordermoney'];
					}
					if (in_array('total', $options)) {
						$level2_commissions = pdo_fetchall('select og.commission2,og.commissions from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ')  and o.status>=1 and o.status<>4 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($level2_commissions as $c) {
							$commission = iunserializer($c['commissions']);
							$commission2 = iunserializer($c['commission2']);
							if (empty($commission)) {
								$commission_total += isset($commission2['level' . $agentLevel['id']]) ? $commission2['level' . $agentLevel['id']] : $commission2['default'];
							} else {
								$commission_total += isset($commission['level2']) ? $commission['level2'] : 0;
							}
						}
					}
					if (in_array('ok', $options)) {
						$level2_commissions = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ")  and ({$time} - o.finishtime > {$day_times}) and o.status>=3  and  o.status<>6 and o.status<>4 and o.status<>5 and og.status2=0 and og.nocommission=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
						foreach ($level2_commissions as $c) {
							$commission = iunserializer($c['commissions']);
							$commission2 = iunserializer($c['commission2']);
							if (empty($commission)) {
								$commission_ok += isset($commission2['level' . $agentLevel['id']]) ? $commission2['level' . $agentLevel['id']] : $commission2['default'];
							} else {
								$commission_ok += isset($commission['level2']) ? $commission['level2'] : 0;
							}
						}
					}
					if (in_array('lock', $options)) {
						$level2_commissions1 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ")  and ({$time} - o.finishtime <= {$day_times}) and og.status2=0 and o.status>=3  and  o.status<>6 and o.status<>4 and o.status<>5 and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
						foreach ($level2_commissions1 as $c) {
							$commission = iunserializer($c['commissions']);
							$commission2 = iunserializer($c['commission2']);
							if (empty($commission)) {
								$commission_lock += isset($commission2['level' . $agentLevel['id']]) ? $commission2['level' . $agentLevel['id']] : $commission2['default'];
							} else {
								$commission_lock += isset($commission['level2']) ? $commission['level2'] : 0;
							}
						}
					}
					if (in_array('apply', $options)) {
						$level2_commissions2 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ')  and o.status>=3 and o.status<>6 and o.status<>4 and o.status<>5 and og.status2=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($level2_commissions2 as $c) {
							$commission = iunserializer($c['commissions']);
							$commission2 = iunserializer($c['commission2']);
							if (empty($commission)) {
								$commission_apply += isset($commission2['level' . $agentLevel['id']]) ? $commission2['level' . $agentLevel['id']] : $commission2['default'];
							} else {
								$commission_apply += isset($commission['level2']) ? $commission['level2'] : 0;
							}
						}
					}
					if (in_array('check', $options)) {
						$level2_commissions3 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ')  and o.status>=3 and o.status<>6 and o.status<>4 and o.status<>5 and og.status2=2 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($level2_commissions3 as $c) {
							$commission = iunserializer($c['commissions']);
							$commission2 = iunserializer($c['commission2']);
							if (empty($commission)) {
								$commission_check += isset($commission2['level' . $agentLevel['id']]) ? $commission2['level' . $agentLevel['id']] : $commission2['default'];
							} else {
								$commission_check += isset($commission['level2']) ? $commission['level2'] : 0;
							}
						}
					}
					if (in_array('pay', $options)) {
						$level2_commissions3 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ')  and o.status>=3 and o.status<>6 and o.status<>4 and o.status<>5 and og.status2=3 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($level2_commissions3 as $c) {
							$commission = iunserializer($c['commissions']);
							$commission2 = iunserializer($c['commission2']);
							if (empty($commission)) {
								$commission_pay += isset($commission2['level' . $agentLevel['id']]) ? $commission2['level' . $agentLevel['id']] : $commission2['default'];
							} else {
								$commission_pay += isset($commission['level2']) ? $commission['level2'] : 0;
							}
						}
					}
					$level2_agentids = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where agentid in( ' . implode(',', array_keys($level1_agentids)) . ') and isagent=1 and status=1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
					$level2 = count($level2_agentids);
					$agentcount += $level2;
				}
			}
			if ($level >= 3) {
				if ($level2 > 0) {
					if (in_array('ordercount0', $options)) {
						$level3_ordercount = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ')  and o.status>=0 and o.status<>4 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$order30 += $level3_ordercount['ordercount'];
						$ordercount0 += $level3_ordercount['ordercount'];
						$ordermoney0 += $level3_ordercount['ordermoney'];
					}
					if (in_array('ordercount', $options)) {
						$level3_ordercount = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ')  and o.status>=1 and o.status<>4 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$order3 += $level3_ordercount['ordercount'];
						$ordercount += $level3_ordercount['ordercount'];
						$ordermoney += $level3_ordercount['ordermoney'];
					}
					if (in_array('ordercount3', $options)) {
						$level3_ordercount3 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ')  and o.status>=3 and o.status<>6 and o.status<>4 and o.status<>5 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$order33 += $level3_ordercount3['ordercount'];
						$ordercount3 += $level3_ordercount3['ordercount'];
						$ordermoney3 += $level3_ordercount3['ordermoney'];
						$order33money += $level3_ordercount['ordermoney'];
					}
					if (in_array('total', $options)) {
						$level3_commissions = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ')  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($level3_commissions as $c) {
							$commission = iunserializer($c['commissions']);
							$commission3 = iunserializer($c['commission3']);
							if (empty($commission)) {
								$commission_total += isset($commission3['level' . $agentLevel['id']]) ? $commission3['level' . $agentLevel['id']] : $commission3['default'];
							} else {
								$commission_total += isset($commission['level3']) ? $commission['level3'] : 0;
							}
						}
					}
					if (in_array('ok', $options)) {
						$level3_commissions = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ")  and ({$time} - o.finishtime > {$day_times}) and o.status>=3 and o.status<>6 and o.status<>4 and o.status<>5 and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
						foreach ($level3_commissions as $c) {
							$commission = iunserializer($c['commissions']);
							$commission3 = iunserializer($c['commission3']);
							if (empty($commission)) {
								$commission_ok += isset($commission3['level' . $agentLevel['id']]) ? $commission3['level' . $agentLevel['id']] : $commission3['default'];
							} else {
								$commission_ok += isset($commission['level3']) ? $commission['level3'] : 0;
							}
						}
					}
					if (in_array('lock', $options)) {
						$level3_commissions1 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ")  and o.status>=3 and o.status<>6 and o.status<>4 and o.status<>5 and ({$time} - o.finishtime > {$day_times}) and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
						foreach ($level3_commissions1 as $c) {
							$commission = iunserializer($c['commissions']);
							$commission3 = iunserializer($c['commission3']);
							if (empty($commission)) {
								$commission_lock += isset($commission3['level' . $agentLevel['id']]) ? $commission3['level' . $agentLevel['id']] : $commission3['default'];
							} else {
								$commission_lock += isset($commission['level3']) ? $commission['level3'] : 0;
							}
						}
					}
					if (in_array('apply', $options)) {
						$level3_commissions2 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ')  and o.status>=3 and o.status<>6  and o.status<>4 and o.status<>5 and og.status3=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($level3_commissions2 as $c) {
							$commission = iunserializer($c['commissions']);
							$commission3 = iunserializer($c['commission3']);
							if (empty($commission)) {
								$commission_apply += isset($commission3['level' . $agentLevel['id']]) ? $commission3['level' . $agentLevel['id']] : $commission3['default'];
							} else {
								$commission_apply += isset($commission['level3']) ? $commission['level3'] : 0;
							}
						}
					}
					if (in_array('check', $options)) {
						$level3_commissions3 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ')  and o.status>=3 and o.status<>6 and o.status<>4 and o.status<>5 and og.status3=2 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($level3_commissions3 as $c) {
							$commission = iunserializer($c['commissions']);
							$commission3 = iunserializer($c['commission3']);
							if (empty($commission)) {
								$commission_check += isset($commission3['level' . $agentLevel['id']]) ? $commission3['level' . $agentLevel['id']] : $commission3['default'];
							} else {
								$commission_check += isset($commission['level3']) ? $commission['level3'] : 0;
							}
						}
					}
					if (in_array('pay', $options)) {
						$level3_commissions3 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ')  and o.status>=3  and o.status<>4 and o.status<>5 and o.status<>6 and og.status3=3 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($level3_commissions3 as $c) {
							$commission = iunserializer($c['commissions']);
							$commission3 = iunserializer($c['commission3']);
							if (empty($commission)) {
								$commission_pay += isset($commission3['level' . $agentLevel['id']]) ? $commission3['level' . $agentLevel['id']] : $commission3['default'];
							} else {
								$commission_pay += isset($commission['level3']) ? $commission['level3'] : 0;
							}
						}
					}
					$level3_agentids = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where uniacid=:uniacid and agentid in( ' . implode(',', array_keys($level2_agentids)) . ') and isagent=1 and status=1', array(':uniacid' => $_W['uniacid']), 'id');
					$level3 = count($level3_agentids);
					$agentcount += $level3;
				}
			}
			//Author:ym Date:2016-04-07 Content:自购完成订单
			if (in_array('myorder', $options)) {
				$myorder_where = "";
				if(p('love')){
					$myorder_where .= " and ({$time} - o.finishtime <= {$withdraw_day}) and cashier=0";
				}
				$myorder = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.openid=:openid and o.status>=3 and o.status<>6 and o.status<>4 and o.status<>5 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
				//Author:ym Date:2016-04-07 Content:自购订单金额
				$myordermoney = $myorder['ordermoney'];
				//Author:ym Date:2016-04-07 Content:自购订单数量
				$myordercount = $myorder['ordercount'];
			}

			$member['agentcount'] = $agentcount;
			$member['ordercount'] = $ordercount;
			$member['ordermoney'] = $ordermoney;
			$member['order1'] = $order1;
			$member['order2'] = $order2;
			$member['order3'] = $order3;
			$member['ordercount3'] = $ordercount3;
			$member['ordermoney3'] = $ordermoney3;
			$member['order13'] = $order13;
			$member['order23'] = $order23;
			$member['order33'] = $order33;
			$member['order13money'] = $order13money;
			$member['order23money'] = $order23money;
			$member['order33money'] = $order33money;
			$member['ordercount0'] = $ordercount0;
			$member['ordermoney0'] = $ordermoney0;
			$member['order10'] = $order10;
			$member['order20'] = $order20;
			$member['order30'] = $order30;
			$member['commission_total'] = round($commission_total, 2);
			$member['commission_ok'] = round($commission_ok, 2);
			$member['commission_lock'] = round($commission_lock, 2);
			$member['commission_apply'] = round($commission_apply, 2);
			$member['commission_check'] = round($commission_check, 2);
			$member['commission_pay'] = round($commission_pay, 2);
			//Author:ym Date:2016-07-15 Content:减去已消费的佣金
			if(p('love')){
				$member['commission_ok'] -= $member['credit20'];
				$commission_apply_credit20 = pdo_fetchcolumn('select credit20 from ' . tablename('sz_yi_commission_apply') . ' where uniacid=:uniacid and status=1', array(':uniacid' => $_W['uniacid']));
				if(!empty($commission_apply_credit20)){
					$member['commission_apply'] -= $commission_apply_credit20;
				}
				$commission_check_credit20 = pdo_fetchcolumn('select credit20 from ' . tablename('sz_yi_commission_apply') . ' where uniacid=:uniacid and status=2', array(':uniacid' => $_W['uniacid']));
				if(!empty($commission_check_credit20)){
					$member['commission_check'] -= $commission_check_credit20;
				}
				$commission_pay = floatval($commission_pay);
				$commission_pay_credit20 = pdo_fetchcolumn('select credit20 from ' . tablename('sz_yi_commission_apply') . ' where uniacid=:uniacid and status=3', array(':uniacid' => $_W['uniacid']));
				if(!empty($commission_pay_credit20)){
					$member['commission_pay'] -= floatval($commission_pay_credit20);
				}
			}
			$member['level1'] = $level1;
			$member['level1_agentids'] = $level1_agentids;
			$member['level2'] = $level2;
			$member['level2_agentids'] = $level2_agentids;
			$member['level3'] = $level3;
			$member['level3_agentids'] = $level3_agentids;
			//成为分销商未记录时间，已创建时间为准修改
			if(!empty($member['agenttime'])){
				$member['agenttime'] = date('Y-m-d H:i', $member['agenttime']);
			}else{
				$agent_time = $member['createtime'] > 0 ? $member['createtime'] : $time;
				pdo_update("sz_yi_member", array("agenttime" => $agent_time), array("openid" => $openid, "uniacid" => $_W['uniacid']));
				$member['agenttime'] = date('Y-m-d H:i', $agent_time);
			}
			$member['myordermoney'] = $myordermoney;
			$member['myordercount'] = $myordercount;
			return $member;
		}
		//获取该订单的所有分佣分销商
		public function getAgents($orderid = 0)
		{
			global $_W, $_GPC;
			$agents = array();
			$order = pdo_fetch('select id,agentid,openid from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($order)) {
				return $agents;
			}
			$m1 = m('member')->getMember($order['agentid']);
			if (!empty($m1) && $m1['isagent'] == 1 && $m1['status'] == 1) {
				$agents[] = $m1;
				if (!empty($m1['agentid'])) {
					$m2 = m('member')->getMember($m1['agentid']);
					if (!empty($m2) && $m2['isagent'] == 1 && $m2['status'] == 1) {
						$agents[] = $m2;
						if (!empty($m2['agentid'])) {
							$m3 = m('member')->getMember($m2['agentid']);
							if (!empty($m3) && $m3['isagent'] == 1 && $m3['status'] == 1) {
								$agents[] = $m3;
							}
						}
					}
				}
			}
			return $agents;
		}
		//是否为分销商
		public function isAgent($openid)
		{
			if (empty($openid)) {
				return false;
			}
			if (is_array($openid)) {
				return $openid['isagent'] == 1 && $openid['status'] == 1;
			}
			$member = m('member')->getMember($openid);
			return $member['isagent'] == 1 && $member['status'] == 1;
		}

		//Author:ym Date:2016-05-06 Content:分成方式计算		
		public function calculate_goods_method($goods){
			global $_W;
			$set = $this->getSet();
			$realprice = $goods['marketprice'];
			if(empty($set['culate_method'])){
				return $realprice;
			}else{
				if($set['culate_method'] == 1){
					return $goods['productprice'];
				}else if($set['culate_method'] == 2){
					return $goods['marketprice'];
				}else if($set['culate_method'] == 3){
					return $goods['costprice'];
				}else if($set['culate_method'] == 4){
					$price = $realprice - $goods['costprice'];
					return $price > 0 ? $price : 0;
				}
			}
		}
		//获取商品的分销佣金
		public function getCommission($goods)
		{
			global $_W;
			$set = $this->getSet();
			$commission = 0;
			if ($goods['hascommission'] == 1) {
				$commission = $set['level'] >= 1 ? ($goods['commission1_rate'] > 0 ? ($goods['commission1_rate'] * $goods['marketprice'] / 100) : $goods['commission1_pay']) : 0;
			} else {
				$openid = m('user')->getOpenid();
				$level = $this->getLevel($openid);
				$price = $this->calculate_goods_method($goods);
				if (!empty($level)) {
					$commission = $set['level'] >= 1 ? round($level['commission1'] * $price / 100, 2) : 0;
				} else {
					$commission = $set['level'] >= 1 ? round($set['commission1'] * $price / 100, 2) : 0;
				}
			}
			return $commission;
		}
		//创建商城二维码
		public function createMyShopQrcode($mid = 0, $posterid = 0)
		{
			global $_W;
			$path = IA_ROOT . '/addons/sz_yi/data/qrcode/' . $_W['uniacid'];
			if (!is_dir($path)) {
				load()->func('file');
				mkdirs($path);
			}
			$url = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=sz_yi&do=plugin&p=commission&method=myshop&mid=' . $mid;
			if (!empty($posterid)) {
				$url .= '&posterid=' . $posterid;
			}
			$file = 'myshop_' . $posterid . '_' . $mid . '.png';
			$qr_file = $path . '/' . $file;
			if (!is_file($qr_file)) {
				require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
				QRcode::png($url, $qr_file, QR_ECLEVEL_H, 4);
			}
			return $_W['siteroot'] . 'addons/sz_yi/data/qrcode/' . $_W['uniacid'] . '/' . $file;
		}
		//创建图像
		private function createImage($url)
		{
			load()->func('communication');
			$resp = ihttp_request($url);
			return imagecreatefromstring($resp['content']);
		}
		//创建商品推广图
		public function createGoodsImage($goods, $shop_set)
		{
			global $_W, $_GPC;
			$goods = set_medias($goods, 'thumb');
			$openid = m('user')->getOpenid();
			$me = m('member')->getMember($openid);
			if ($me['isagent'] == 1 && $me['status'] == 1) {
				$userinfo = $me;
			} else {
				$mid = intval($_GPC['mid']);
				if (!empty($mid)) {
					$userinfo = m('member')->getMember($mid);
				}
			}
			$path = IA_ROOT . '/addons/sz_yi/data/poster/' . $_W['uniacid'] . '/';
			if (!is_dir($path)) {
				load()->func('file');
				mkdirs($path);
			}
			$img = empty($goods['commission_thumb']) ? $goods['thumb'] : tomedia($goods['commission_thumb']);
			$md5 = md5(json_encode(array('id' => $goods['id'], 'marketprice' => $goods['marketprice'], 'productprice' => $goods['productprice'], 'img' => $img, 'openid' => $openid, 'version' => 4)));
			$file = $md5 . '.jpg';
			//echo $path . $file;exit;
			if (!is_file($path . $file)) {
				
				set_time_limit(0);
				$font = IA_ROOT . '/addons/sz_yi/static/fonts/msyh.ttf';
				$target = imagecreatetruecolor(640, 1225);
				if(!is_weixin()){
					$location_num = 196;
					$bg = imagecreatefromjpeg(IA_ROOT . '/addons/sz_yi/plugin/commission/images/poster_pc.jpg');
				}else{
					$location_num = 50;
					$bg = imagecreatefromjpeg(IA_ROOT . '/addons/sz_yi/plugin/commission/images/poster.jpg');
				}
				$imgusername = $userinfo['nickname'] ? $userinfo['nickname'] : $userinfo['realname'];
				$imgusername = $imgusername ? $imgusername : $userinfo['mobile'];
				imagecopy($target, $bg, 0, 0, 0, 0, 640, 1225);
				imagedestroy($bg);
				$avatar = preg_replace('/\\/0$/i', '/96', $userinfo['avatar']);
				$head = $this->createImage($avatar);
				$w = imagesx($head);
				$h = imagesy($head);
				imagecopyresized($target, $head, 24, 32, 0, 0, 88, 88, $w, $h);
				imagedestroy($head);
				$thumb = $this->createImage($img);
				$w = imagesx($thumb);
				$h = imagesy($thumb);
				imagecopyresized($target, $thumb, 0, 160, 0, 0, 640, 640, $w, $h);
				imagedestroy($thumb);
				$black = imagecreatetruecolor(640, 127);
				imagealphablending($black, false);
				imagesavealpha($black, true);
				$blackcolor = imagecolorallocatealpha($black, 0, 0, 0, 25);
				imagefill($black, 0, 0, $blackcolor);
				imagecopy($target, $black, 0, 678, 0, 0, 640, 127);
				imagedestroy($black);
				$goods_qrcode_file = tomedia(m('qrcode')->createGoodsQrcode($userinfo['id'], $goods['id']));
				$qrcode = $this->createImage($goods_qrcode_file);
				$w = imagesx($qrcode);
				$h = imagesy($qrcode);
				imagecopyresized($target, $qrcode, $location_num, 835, 0, 0, 250, 250, $w, $h);
				imagedestroy($qrcode);
				$bc = imagecolorallocate($target, 0, 3, 51);
				$cc = imagecolorallocate($target, 240, 102, 0);
				$wc = imagecolorallocate($target, 255, 255, 255);
				$yc = imagecolorallocate($target, 255, 255, 0);
				$str1 = '我是';
				imagettftext($target, 20, 0, 150, 70, $bc, $font, $str1);
				imagettftext($target, 20, 0, 210, 70, $cc, $font, $imgusername);
				$str2 = '我要为';
				imagettftext($target, 20, 0, 150, 105, $bc, $font, $str2);
				$str3 = $shop_set['name'];
				imagettftext($target, 20, 0, 240, 105, $cc, $font, $str3);
				$box = imagettfbbox(20, 0, $font, $str3);
				$width = $box[4] - $box[6];
				$str4 = '代言';
				imagettftext($target, 20, 0, 240 + $width + 10, 105, $bc, $font, $str4);
				$str5 = mb_substr($goods['title'], 0, 50, 'utf-8');
				imagettftext($target, 20, 0, 30, 730, $wc, $font, $str5);
				$str6 = '￥' . number_format($goods['marketprice'], 2);
				imagettftext($target, 25, 0, 25, 780, $yc, $font, $str6);
				$box = imagettfbbox(26, 0, $font, $str6);
				$width = $box[4] - $box[6];
				if ($goods['productprice'] > 0) {
					$str7 = '￥' . number_format($goods['productprice'], 2);
					imagettftext($target, 22, 0, 25 + $width + 10, 780, $wc, $font, $str7);
					$end = 25 + $width + 10;
					$box = imagettfbbox(22, 0, $font, $str7);
					$width = $box[4] - $box[6];
					imageline($target, $end, 770, $end + $width + 20, 770, $wc);
					imageline($target, $end, 771.5, $end + $width + 20, 771, $wc);
				}
				imagejpeg($target, $path . $file);
				imagedestroy($target);
			}
			return $_W['siteroot'] . 'addons/sz_yi/data/poster/' . $_W['uniacid'] . '/' . $file;
		}
		//创建商城推广图
		public function createShopImage($shop_set)
		{
			global $_W, $_GPC;
			$shop_set = set_medias($shop_set, 'signimg');
			$path = IA_ROOT . '/addons/sz_yi/data/poster/' . $_W['uniacid'] . '/';
			if (!is_dir($path)) {
				load()->func('file');
				mkdirs($path);
			}
			$mid = intval($_GPC['mid']);
			$openid = m('user')->getOpenid();
			$me = m('member')->getMember($openid);
			if ($me['isagent'] == 1 && $me['status'] == 1) {
				$userinfo = $me;
			} else {
				$mid = intval($_GPC['mid']);
				if (!empty($mid)) {
					$userinfo = m('member')->getMember($mid);
				}
			}
			$md5 = md5(json_encode(array('openid' => $openid, 'signimg' => $shop_set['signimg'], 'version' => 4)));
			$file = $md5 . '.jpg';
			if (!is_file($path . $file)) {
				set_time_limit(0);
				@ini_set('memory_limit', '256M');
				$font = IA_ROOT . '/addons/sz_yi/static/fonts/msyh.ttf';
				$target = imagecreatetruecolor(640, 1225);
				$bc = imagecolorallocate($target, 0, 3, 51);
				$cc = imagecolorallocate($target, 240, 102, 0);
				$wc = imagecolorallocate($target, 255, 255, 255);
				$yc = imagecolorallocate($target, 255, 255, 0);
				if(!is_weixin()){
					$bg = imagecreatefromjpeg(IA_ROOT . '/addons/sz_yi/plugin/commission/images/poster_pc.jpg');
					$location_num = 196;
				}else{
					$bg = imagecreatefromjpeg(IA_ROOT . '/addons/sz_yi/plugin/commission/images/poster.jpg');
					$location_num = 50;
				}
				$imgusername = $userinfo['nickname'] ? $userinfo['nickname'] : $userinfo['realname'];
				$imgusername = $imgusername ? $imgusername : $userinfo['mobile'];
				imagecopy($target, $bg, 0, 0, 0, 0, 640, 1225);
				imagedestroy($bg);
				$avatar = preg_replace('/\\/0$/i', '/96', $userinfo['avatar']);
				$head = $this->createImage($avatar);
				$w = imagesx($head);
				$h = imagesy($head);
				imagecopyresized($target, $head, 24, 32, 0, 0, 88, 88, $w, $h);
				imagedestroy($head);
				$thumb = $this->createImage($shop_set['signimg']);
				$w = imagesx($thumb);
				$h = imagesy($thumb);
				imagecopyresized($target, $thumb, 0, 160, 0, 0, 640, 640, $w, $h);
				imagedestroy($thumb);
				$qrcode_file = tomedia($this->createMyShopQrcode($userinfo['id']));
				$qrcode = $this->createImage($qrcode_file);
				$w = imagesx($qrcode);
				$h = imagesy($qrcode);
				imagecopyresized($target, $qrcode, $location_num, 835, 0, 0, 250, 250, $w, $h);
				imagedestroy($qrcode);
				$str1 = '我是';
				imagettftext($target, 20, 0, 150, 70, $bc, $font, $str1);
				imagettftext($target, 20, 0, 210, 70, $cc, $font, $imgusername);
				$str2 = '我要为';
				imagettftext($target, 20, 0, 150, 105, $bc, $font, $str2);
				$str3 = $shop_set['name'];
				imagettftext($target, 20, 0, 240, 105, $cc, $font, $str3);
				$box = imagettfbbox(20, 0, $font, $str3);
				$width = $box[4] - $box[6];
				$str4 = '代言';
				imagettftext($target, 20, 0, 240 + $width + 10, 105, $bc, $font, $str4);
				imagejpeg($target, $path . $file);
				imagedestroy($target);
			}
			return $_W['siteroot'] . 'addons/sz_yi/data/poster/' . $_W['uniacid'] . '/' . $file;
		}
		//会员进入商城根据配置处理
		public function checkAgent()
		{
			global $_W, $_GPC;
			$set = $this->getSet();
			if (empty($set['level'])) {
				return;
			}
			$openid = m('user')->getOpenid();
			if (empty($openid)) {
				return;
			}
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			$parent = false;
			$mid = intval($_GPC['mid']);
			if (!empty($mid)) {
				$parent = m('member')->getMember($mid);
			}
			$parent_is_agent = !empty($parent) && $parent['isagent'] == 1 && $parent['status'] == 1;
			if ($parent_is_agent) {
				if ($parent['openid'] != $openid) {
					$clickcount = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_commission_clickcount') . ' where uniacid=:uniacid and openid=:openid and from_openid=:from_openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':from_openid' => $parent['openid']));
					if ($clickcount <= 0) {
						$click = array('uniacid' => $_W['uniacid'], 'openid' => $openid, 'from_openid' => $parent['openid'], 'clicktime' => time());
						pdo_insert('sz_yi_commission_clickcount', $click);
						pdo_update('sz_yi_member', array('clickcount' => $parent['clickcount'] + 1), array('uniacid' => $_W['uniacid'], 'id' => $parent['id']));
					}
				}
			}
			if ($member['isagent'] == 1) {
				return;
			}
			if ($type == 0) {
				$first = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member') . ' where id<:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $member['id']));
				if ($first <= 0) {
					pdo_update('sz_yi_member', array('isagent' => 1, 'status' => 1, 'agenttime' => time(), 'agentblack' => 0), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
					return;
				}
			}
			$time = time();
			$become_child = intval($set['become_child']);
			if ($parent_is_agent && empty($member['agentid'])) {
				if ($member['id'] != $parent['id']) {
					if (empty($become_child)) {
						if (empty($member['fixagentid'])) {
							pdo_update('sz_yi_member', array('agentid' => $parent['id'], 'childtime' => $time), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
							$this->sendMessage($parent['openid'], array('nickname' => $member['nickname'], 'childtime' => $time), TM_COMMISSION_AGENT_NEW);
							$this->upgradeLevelByAgent($parent['id']);
						}
					} else {
						pdo_update('sz_yi_member', array('inviter' => $parent['id']), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
					}
				}
			}
			$become_check = intval($set['become_check']);
			if (empty($set['become'])) {
				if (empty($member['agentblack'])) {
					pdo_update('sz_yi_member', array('isagent' => 1, 'status' => $become_check, 'agenttime' => $become_check == 1 ? $time : 0), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
					if ($become_check == 1) {
						$this->sendMessage($openid, array('nickname' => $member['nickname'], 'agenttime' => $time), TM_COMMISSION_BECOME);
						if ($parent_is_agent) {
							$this->upgradeLevelByAgent($parent['id']);
						}
					}
				}
			}
		}

		/**
		 * Author:ym
		 * Date:2016-04-07
		 * Content:处理提交订单
		 **/
		public function checkOrderConfirm($orderid = '0')
		{
			global $_W, $_GPC;
			if (empty($orderid)) {
				return;
			}
			
			$set = $this->getSet();
			if (empty($set['level'])) {
				return;
			}
			$order = pdo_fetch('select id,openid,ordersn,goodsprice,agentid,paytime from ' . tablename('sz_yi_order') . ' where id=:id and status>=0 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($order)) {
				return;
			}
			$openid = $order['openid'];
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			
			$become_child = intval($set['become_child']);
			$parent = false;
			if (empty($become_child)) {
				$parent = m('member')->getMember($member['agentid']);
			} else {
				$parent = m('member')->getMember($member['inviter']);
			}
			$parent_is_agent = !empty($parent) && $parent['isagent'] == 1 && $parent['status'] == 1;
			$time = time();
			$become_child = intval($set['become_child']);
			if ($parent_is_agent) {
				if ($become_child == 1) {
					if (empty($member['agentid']) && $member['id'] != $parent['id']) {
						if (empty($member['fixagentid'])) {
							$member['agentid'] = $parent['id'];
							pdo_update('sz_yi_member', array('agentid' => $parent['id'], 'childtime' => $time), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
							$this->sendMessage($parent['openid'], array('nickname' => $member['nickname'], 'childtime' => $time), TM_COMMISSION_AGENT_NEW);
							$this->upgradeLevelByAgent($parent['id']);
						}
					}
				}
			}
			$agentid = $member['agentid'];
			if ($member['isagent'] == 1 && $member['status'] == 1) {
				if (!empty($set['selfbuy'])) {
					$agentid = $member['id'];
				}
			}
			if (!empty($agentid)) {
				pdo_update('sz_yi_order', array('agentid' => $agentid), array('id' => $orderid));
			}
			$this->calculate($orderid);
			//Author:ym Date:2016-04-07 Content:分红提交订单处理
			$pluginbonus = p("bonus");
			if(!empty($pluginbonus)){
				$pluginbonus->checkOrderConfirm($orderid);
			}
		}

		/**
		 * Author:ym
		 * Date:2016-04-07
		 * Content:处理订单支付
		 **/
		public function checkOrderPay($orderid = '0')
		{
			global $_W, $_GPC;
			if (empty($orderid)) {
				return;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return;
			}
			$order = pdo_fetch('select id,openid,ordersn,goodsprice,agentid,paytime from ' . tablename('sz_yi_order') . ' where id=:id and status>=1 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($order)) {
				return;
			}
			$openid = $order['openid'];
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			
			$pluginchannel = p("channel");
			if(!empty($pluginchannel)){
				$pluginchannel->checkOrderFinishOrPay($orderid);
				$channel_set = $pluginchannel->getSet();
				if (empty($member['ischannel']) && $member['isagent'] == 1) {
					if ($channel_set['become_condition'] == 3 || $channel_set['become_condition'] == 4) {
						$pluginchannel->becomeChannelByOrder($openid, $orderid);
					} elseif ($channel_set['become_condition'] == 2) {
						$pluginchannel->becomeChannelByAgent($openid);
					} elseif ($channel_set['become_condition'] == 5) {
						$pluginchannel->becomeChannelByGood($openid, $orderid);
					}				
				} else {
					if ($channel_set['become'] == 1) {
						$pluginchannel->upgradelevelByGood($openid,$orderid);
					} elseif ($channel_set['become'] == 2) {
						$pluginchannel->upgradelevelByOther($openid,$orderid);
					}
				}
				
			}
			$become_child = intval($set['become_child']);
			$parent = false;
			if (empty($become_child)) {
				$parent = m('member')->getMember($member['agentid']);
			} else {
				$parent = m('member')->getMember($member['inviter']);
			}
			$parent_is_agent = !empty($parent) && $parent['isagent'] == 1 && $parent['status'] == 1;
			$time = time();
			$become_child = intval($set['become_child']);
			if ($parent_is_agent) {
				if ($become_child == 2) {
					if (empty($member['agentid']) && $member['id'] != $parent['id']) {
						if (empty($member['fixagentid'])) {
							$member['agentid'] = $parent['id'];
							pdo_update('sz_yi_member', array('agentid' => $parent['id'], 'childtime' => $time), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
							$this->sendMessage($parent['openid'], array('nickname' => $member['nickname'], 'childtime' => $time), TM_COMMISSION_AGENT_NEW);
							$this->upgradeLevelByAgent($parent['id']);
							if (empty($order['agentid'])) {
								$order['agentid'] = $parent['id'];
								pdo_update('sz_yi_order', array('agentid' => $parent['id']), array('id' => $orderid));
								$this->calculate($orderid);
							}
						}
					}
				}
			}
			$isagent = $member['isagent'] == 1 && $member['status'] == 1;
			//Author:Y.yang Date:2016-04-08 Content:购买指定商品成为分销商，判断是否为分销商，如果不是，查选择的商品是否存在，条件满足更改用户信息（isagent => 1, status => 1）并推送微信消息。
			if (!$isagent) {
                if (intval($set['become']) == 4 && !empty($set['become_goodsid'])) {
                    $goods_id = pdo_fetchall('select goodsid from ' . tablename('sz_yi_order_goods') . ' where orderid=:orderid and uniacid=:uniacid  ', array(':uniacid' => $_W['uniacid'], ':orderid' => $order['id']), 'goodsid');
                    if (in_array($set['become_goodsid'], array_keys($goods_id))) {
                        if (empty($member['agentblack'])) {
                            pdo_update('sz_yi_member', array('status' => 1, 'isagent' => 1, 'agenttime' => $time), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
                            $this->sendMessage($openid, array('nickname' => $member['nickname'], 'agenttime' => $time), TM_COMMISSION_BECOME);
                            if (!empty($parent)) {
                                $this->upgradeLevelByAgent($parent['id']);
                            }
                        }
                    }
                }
            }
            //END
			if (!$isagent && empty($set['become_order'])) {
				$time = time();
				if ($set['become'] == 2 || $set['become'] == 3) {
					$parentisagent = true;
					if (!empty($member['agentid'])) {
						$parent = m('member')->getMember($member['agentid']);
						if (empty($parent) || $parent['isagent'] != 1 || $parent['status'] != 1) {
							$parentisagent = false;
						}
					}
					if ($parentisagent) {
						$can = false;
						if ($set['become'] == '2') {
							$ordercount = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order') . ' where openid=:openid and status>=1 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
							$can = $ordercount >= intval($set['become_ordercount']);
						} else if ($set['become'] == '3') {
							$moneycount = pdo_fetchcolumn('select sum(og.realprice) from ' . tablename('sz_yi_order_goods') . ' og left join ' . tablename('sz_yi_order') . ' o on og.orderid=o.id  where o.openid=:openid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
							$can = $moneycount >= floatval($set['become_moneycount']);
						}
						if ($can) {
							if (empty($member['agentblack'])) {
								$become_check = intval($set['become_check']);
								pdo_update('sz_yi_member', array('status' => $become_check, 'isagent' => 1, 'agenttime' => $time), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
								if ($become_check == 1) {
									$this->sendMessage($openid, array('nickname' => $member['nickname'], 'agenttime' => $time), TM_COMMISSION_BECOME);
									if ($parentisagent) {
										$this->upgradeLevelByAgent($parent['id']);
									}
								}
							}
						}
					}
				}
			}
			if (!empty($order['agentid'])) {
				$parent = m('member')->getMember($order['agentid']);
				if (!empty($parent) && $parent['isagent'] == 1 && $parent['status'] == 1) {
					if ($order['agentid'] == $parent['id']) {
						$order_goods = pdo_fetchall('select g.id,g.title,og.total,og.price,og.realprice, og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1 from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $order['id']));
						$goods = '';
						$level = $parent['agentlevel'];
						$commission_total = 0;
						$pricetotal = 0;
						foreach ($order_goods as $og) {
							$goods .= "" . $og['title'] . '( ';
							if (!empty($og['optiontitle'])) {
								$goods .= ' 规格: ' . $og['optiontitle'];
							}
							$goods .= ' 单价: ' . ($og['realprice'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['realprice'] . '); ';
							$commission = iunserializer($og['commission1']);
							$commission_total += isset($commission['level' . $level]) ? $commission['level' . $level] : $commission['default'];
							$pricetotal += $og['realprice'];
						}
						$this->sendMessage($parent['openid'], array('nickname' => $member['nickname'], 'ordersn' => $order['ordersn'], 'price' => $pricetotal, 'goods' => $goods, 'commission' => $commission_total, 'paytime' => $order['paytime'],), TM_COMMISSION_ORDER_PAY);
					}
				}
				if(!empty($set['remind_message']) && $set['level'] >= 2){ //Author:ym Date:2016-04-07 Content:三级消息提醒开关
					//Author:ym Date:2016-04-07 Content:二级消息处理
					if (!empty($parent['agentid'])) {
						$parent = m('member')->getMember($parent['agentid']);
						if (!empty($parent) && $parent['isagent'] == 1 && $parent['status'] == 1) {
							if ($order['agentid'] != $parent['id']) {
								$order_goods = pdo_fetchall('select g.id,g.title,og.total,og.price,og.realprice, og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission2 from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $order['id']));
								$goods = '';
								$level = $parent['agentlevel'];
								$commission_total = 0;
								$pricetotal = 0;
								foreach ($order_goods as $og) {
									$goods .= "" . $og['title'] . '( ';
									if (!empty($og['optiontitle'])) {
										$goods .= ' 规格: ' . $og['optiontitle'];
									}
									$goods .= ' 单价: ' . ($og['realprice'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['realprice'] . '); ';
									$commission = iunserializer($og['commission2']);
									$commission_total += isset($commission['level' . $level]) ? $commission['level' . $level] : $commission['default'];
									$pricetotal += $og['realprice'];
								}
								$this->sendMessage($parent['openid'], array('nickname' => $member['nickname'], 'ordersn' => $order['ordersn'], 'price' => $pricetotal, 'goods' => $goods, 'commission' => $commission_total, 'paytime' => $order['paytime'],), TM_COMMISSION_ORDER_PAY);
							}
						}
						//Author:ym Date:2016-04-07 Content:三级消息处理
						if (!empty($parent['agentid']) && $set['level'] >= 3) {
							$parent = m('member')->getMember($parent['agentid']);
							if (!empty($parent) && $parent['isagent'] == 1 && $parent['status'] == 1) {
								if ($order['agentid'] != $parent['id']) {
									$order_goods = pdo_fetchall('select g.id,g.title,og.total,og.price,og.realprice, og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission3 from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $order['id']));
									$goods = '';
									$level = $parent['agentlevel'];
									$commission_total = 0;
									$pricetotal = 0;
									foreach ($order_goods as $og) {
										$goods .= "" . $og['title'] . '( ';
										if (!empty($og['optiontitle'])) {
											$goods .= ' 规格: ' . $og['optiontitle'];
										}
										$goods .= ' 单价: ' . ($og['realprice'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['realprice'] . '); ';
										$commission = iunserializer($og['commission3']);
										$commission_total += isset($commission['level' . $level]) ? $commission['level' . $level] : $commission['default'];
										$pricetotal += $og['realprice'];
									}
									$this->sendMessage($parent['openid'], array('nickname' => $member['nickname'], 'ordersn' => $order['ordersn'], 'price' => $pricetotal, 'goods' => $goods, 'commission' => $commission_total, 'paytime' => $order['paytime'],), TM_COMMISSION_ORDER_PAY);
								}
							}
						}
					}
				}
			}
			//Author:ym Date:2016-04-07 Content:分红支付订单处理
			$pluginbonus = p("bonus");
			if(!empty($pluginbonus)){
				$pluginbonus->checkOrderPay($orderid);
			}
		}

		/**
		 * Author:ym
		 * Date:2016-04-07
		 * Content:处理订单完成
		 **/
		public function checkOrderFinish($orderid = '')
		{
			global $_W, $_GPC;
			if (empty($orderid)) {
				return;
			}
			$order = pdo_fetch('select id,openid, ordersn,goodsprice,agentid,finishtime from ' . tablename('sz_yi_order') . ' where id=:id and status>=3 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($order)) {
				return;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return;
			}
			$openid = $order['openid'];
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			
			$pluginchannel = p("channel");
			if(!empty($pluginchannel)){
				$channel_set = $pluginchannel->getSet();
				if (empty($member['ischannel'])) {
					if ($set['become_condition'] == 3 || $set['become_condition'] == 4) {
						$pluginchannel->becomeChannelByOrder($openid, $orderid);
					} elseif ($set['become_condition'] == 2) {
						$pluginchannel->becomeChannelByAgent($openid);
					} elseif ($set['become_condition'] == 5) {
						$pluginchannel->becomeChannelByGood($openid, $orderid);
					}				
				} else {
					if ($channel_set['become'] == 1) {
						$pluginchannel->upgradelevelByGood($openid,$orderid);
					} elseif ($channel_set['become'] == 2) {
						$pluginchannel->upgradelevelByOther($openid,$orderid);
					}
				}
				
			}
			$time = time();
			$isagent = $member['isagent'] == 1 && $member['status'] == 1;
			if (!$isagent && $set['become_order'] == 1) {
				if ($set['become'] == 2 || $set['become'] == 3) {
					$parentisagent = true;
					if (!empty($member['agentid'])) {
						$parent = m('member')->getMember($member['agentid']);
						if (empty($parent) || $parent['isagent'] != 1 || $parent['status'] != 1) {
							$parentisagent = false;
						}
					}
					if ($parentisagent) {
						$can = false;
						if ($set['become'] == '2') {
							$ordercount = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order') . ' where openid=:openid and status>=3 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
							$can = $ordercount >= intval($set['become_ordercount']);
						} else if ($set['become'] == '3') {
							$moneycount = pdo_fetchcolumn('select sum(goodsprice) from ' . tablename('sz_yi_order') . ' where openid=:openid and status>=3 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
							$can = $moneycount >= floatval($set['become_moneycount']);
						}
						if ($can) {
							if (empty($member['agentblack'])) {
								$become_check = intval($set['become_check']);
								pdo_update('sz_yi_member', array('status' => $become_check, 'isagent' => 1, 'agenttime' => $time), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
								if ($become_check == 1) {
									$this->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'agenttime' => $time), TM_COMMISSION_BECOME);
									if ($parentisagent) {
										$this->upgradeLevelByAgent($parent['id']);
									}
								}
							}
						}
					}
				}
			}
			if (!empty($order['agentid'])) {
				$parent = m('member')->getMember($order['agentid']);
				if (!empty($parent) && $parent['isagent'] == 1 && $parent['status'] == 1) {
					if ($order['agentid'] == $parent['id']) {
						$order_goods = pdo_fetchall('select g.id,g.title,og.total,og.realprice,og.price,og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1 from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $order['id']));
						$goods = '';
						$level = $parent['agentlevel'];
						$commission_total = 0;
						$pricetotal = 0;
						foreach ($order_goods as $og) {
							$goods .= "" . $og['title'] . '( ';
							if (!empty($og['optiontitle'])) {
								$goods .= ' 规格: ' . $og['optiontitle'];
							}
							$goods .= ' 单价: ' . ($og['realprice'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['realprice'] . '); ';
							$commission = iunserializer($og['commission1']);
							$commission_total += isset($commission['level' . $level]) ? $commission['level' . $level] : $commission['default'];
							$pricetotal += $og['realprice'];
						}
						$this->sendMessage($parent['openid'], array('nickname' => $member['nickname'], 'ordersn' => $order['ordersn'], 'price' => $pricetotal, 'goods' => $goods, 'commission' => $commission_total, 'finishtime' => $order['finishtime'],), TM_COMMISSION_ORDER_FINISH);
					}
				}

				if(!empty($set['remind_message']) && $set['level'] >= 2){ //Author:ym Date:2016-04-07 Content:三级消息提醒开关
					//Author:ym Date:2016-04-07 Content:二级消息处理
					if (!empty($parent['agentid'])) { 
						$parent = m('member')->getMember($parent['agentid']);
						if (!empty($parent) && $parent['isagent'] == 1 && $parent['status'] == 1) {
							if ($order['agentid'] != $parent['id']) {
								$order_goods = pdo_fetchall('select g.id,g.title,og.total,og.realprice,og.price,og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission2 from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $order['id']));
								$goods = '';
								$level = $parent['agentlevel'];
								$commission_total = 0;
								$pricetotal = 0;
								foreach ($order_goods as $og) {
									$goods .= "" . $og['title'] . '( ';
									if (!empty($og['optiontitle'])) {
										$goods .= ' 规格: ' . $og['optiontitle'];
									}
									$goods .= ' 单价: ' . ($og['realprice'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['realprice'] . '); ';
									$commission = iunserializer($og['commission2']);
									$commission_total += isset($commission['level' . $level]) ? $commission['level' . $level] : $commission['default'];
									$pricetotal += $og['realprice'];
								}
								$this->sendMessage($parent['openid'], array('nickname' => $member['nickname'], 'ordersn' => $order['ordersn'], 'price' => $pricetotal, 'goods' => $goods, 'commission' => $commission_total, 'finishtime' => $order['finishtime'],), TM_COMMISSION_ORDER_FINISH);
							}
						}
						//Author:ym Date:2016-04-07 Content:三级消息处理
						if (!empty($parent['agentid']) && $set['level'] >= 3) {
							$parent = m('member')->getMember($parent['agentid']);
							if (!empty($parent) && $parent['isagent'] == 1 && $parent['status'] == 1) {
								if ($order['agentid'] != $parent['id']) {
									$order_goods = pdo_fetchall('select g.id,g.title,og.total,og.realprice,og.price,og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission3 from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $order['id']));
									$goods = '';
									$level = $parent['agentlevel'];
									$commission_total = 0;
									$pricetotal = 0;
									foreach ($order_goods as $og) {
										$goods .= "" . $og['title'] . '( ';
										if (!empty($og['optiontitle'])) {
											$goods .= ' 规格: ' . $og['optiontitle'];
										}
										$goods .= ' 单价: ' . ($og['realprice'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['realprice'] . '); ';
										$commission = iunserializer($og['commission3']);
										$commission_total += isset($commission['level' . $level]) ? $commission['level' . $level] : $commission['default'];
										$pricetotal += $og['realprice'];
									}
									$this->sendMessage($parent['openid'], array('nickname' => $member['nickname'], 'ordersn' => $order['ordersn'], 'price' => $pricetotal, 'goods' => $goods, 'commission' => $commission_total, 'finishtime' => $order['finishtime'],), TM_COMMISSION_ORDER_FINISH);
								}
							}
						}
					}
				}
			}
			$this->upgradeLevelByOrder($openid);
			$this->upgradeLevelByGood($orderid);
			//Author:ym Date:2016-07-14 Content:爱心基金完成订单
			$pluginlove = p('love');
            if($pluginlove){
               $goods_where = "og.orderid = {$orderid}";
               $pluginlove->checkOrder($goods_where, $order['openid'], 1);
            }
			//Author:ym Date:2016-04-07 Content:分红完成订单处理
			$pluginbonus = p("bonus");
			if(!empty($pluginbonus)){
				$pluginbonus->checkOrderFinish($orderid);
			}
		}

		//获取小店的详细信息
		function getShop($openid)
		{
			global $_W;
			$member = m('member')->getMember($openid);
			$shop = pdo_fetch('select * from ' . tablename('sz_yi_commission_shop') . ' where uniacid=:uniacid and mid=:mid limit 1', array(':uniacid' => $_W['uniacid'], ':mid' => $member['id']));
			$sysset = m('common')->getSysset(array('shop', 'share'));
			$set = $sysset['shop'];
			$share = $sysset['share'];
			$desc = $share['desc'];
			if (empty($desc)) {
				$desc = $set['description'];
			}
			if (empty($desc)) {
				$desc = $set['name'];
			}
			$thisset = $this->getSet();
			if (empty($shop)) {
				$shop = array('name' => $member['nickname'] . '的' . $thisset['texts']['shop'], 'logo' => $member['avatar'], 'desc' => $desc, 'img' => tomedia($set['img']),);
			} else {
				if (empty($shop['name'])) {
					$shop['name'] = $member['nickname'] . '的' . $thisset['texts']['shop'];
				}
				if (empty($shop['logo'])) {
					$shop['logo'] = tomedia($member['avatar']);
				}
				if (empty($shop['img'])) {
					$shop['img'] = tomedia($set['img']);
				}
				if (empty($shop['desc'])) {
					$shop['desc'] = $desc;
				}
			}
			return $shop;
		}
		//获取全部分销商等级
		function getLevels($all = true)
		{
			global $_W;
			if ($all) {
				return pdo_fetchall('select * from ' . tablename('sz_yi_commission_level') . ' where uniacid=:uniacid order by commission1 asc', array(':uniacid' => $_W['uniacid']));
			} else {
				return pdo_fetchall('select * from ' . tablename('sz_yi_commission_level') . ' where uniacid=:uniacid and (ordermoney>0 or commissionmoney>0) order by commission1 asc', array(':uniacid' => $_W['uniacid']));
			}
		}
		//查询个人分销商等级
		function getLevel($openid)
		{
			global $_W;
			if (empty($openid)) {
				return false;
			}
			$member = m('member')->getMember($openid);
			if (empty($member['agentlevel'])) {
				return false;
			}
			$level = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $member['agentlevel']));
			return $level;
		}
		//用户分局订单条件升级
		function upgradeLevelByOrder($openid)
		{
			global $_W;
			if (empty($openid)) {
				return false;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return false;
			}
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			$leveltype = intval($set['leveltype']);
			if ($leveltype == 4 || $leveltype == 5) {
				if (!empty($member['agentnotupgrade'])) {
					return;
				}
				$oldlevel = $this->getLevel($member['openid']);
				if (empty($oldlevel['id'])) {
					$oldlevel = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
				}
				$m31 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.openid=:openid and o.status>=3 and o.status<>6 and o.status<>4 and o.status<>5 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
				$ordermoney = $m31['ordermoney'];
				$ordercount = $m31['ordercount'];
				if ($leveltype == 4) {
					$newlevel = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$ordermoney} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
					if (empty($newlevel)) {
						return;
					}
					if (!empty($oldlevel['id'])) {
						if ($oldlevel['id'] == $newlevel['id']) {
							return;
						}
						if ($oldlevel['ordermoney'] > $newlevel['ordermoney']) {
							return;
						}
					}
				} else if ($leveltype == 5) {
					$newlevel = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$ordercount} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
					if (empty($newlevel)) {
						return;
					}
					if (!empty($oldlevel['id'])) {
						if ($oldlevel['id'] == $newlevel['id']) {
							return;
						}
						if ($oldlevel['ordercount'] > $newlevel['ordercount']) {
							return;
						}
					}
				}
				pdo_update('sz_yi_member', array('agentlevel' => $newlevel['id']), array('id' => $member['id']));
				$this->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'oldlevel' => $oldlevel, 'newlevel' => $newlevel,), TM_COMMISSION_UPGRADE);
			} else if ($leveltype >= 0 && $leveltype <= 3) {
				$agents = array();
				if (!empty($set['selfbuy'])) {
					$agents[] = $member;
				}
				if (!empty($member['agentid'])) {
					$m1 = m('member')->getMember($member['agentid']);
					if (!empty($m1)) {
						$agents[] = $m1;
						if (!empty($m1['agentid']) && $m1['isagent'] == 1 && $m1['status'] == 1) {
							$m2 = m('member')->getMember($m1['agentid']);
							if (!empty($m2) && $m2['isagent'] == 1 && $m2['status'] == 1) {
								$agents[] = $m2;
								if (empty($set['selfbuy'])) {
									if (!empty($m2['agentid']) && $m2['isagent'] == 1 && $m2['status'] == 1) {
										$m3 = m('member')->getMember($m2['agentid']);
										if (!empty($m3) && $m3['isagent'] == 1 && $m3['status'] == 1) {
											$agents[] = $m3;
										}
									}
								}
							}
						}
					}
				}
				if (empty($agents)) {
					return;
				}
				foreach ($agents as $agent) {
					$agent_info = $this->getInfo($agent['id'], array('ordercount3', 'ordermoney3', 'order13money', 'order13'));
					if (!empty($agent_info['agentnotupgrade'])) {
						continue;
					}
					$oldlevel = $this->getLevel($agent['openid']);
					if (empty($oldlevel['id'])) {
						$oldlevel = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
					}
					if ($leveltype == 0) {
						$ordermoney = $agent_info['ordermoney3'];
						$newlevel = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid and {$ordermoney} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
						if (empty($newlevel)) {
							continue;
						}
						if (!empty($oldlevel['id'])) {
							if ($oldlevel['id'] == $newlevel['id']) {
								continue;
							}
							if ($oldlevel['ordermoney'] > $newlevel['ordermoney']) {
								continue;
							}
						}
					} else if ($leveltype == 1) {
						$ordermoney = $agent_info['order13money'];
						$newlevel = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid and {$ordermoney} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
						if (empty($newlevel)) {
							continue;
						}
						if (!empty($oldlevel['id'])) {
							if ($oldlevel['id'] == $newlevel['id']) {
								continue;
							}
							if ($oldlevel['ordermoney'] > $newlevel['ordermoney']) {
								continue;
							}
						}
					} else if ($leveltype == 2) {
						$ordercount = $agent_info['ordercount3'];
						$newlevel = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$ordercount} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
						if (empty($newlevel)) {
							continue;
						}
						if (!empty($oldlevel['id'])) {
							if ($oldlevel['id'] == $newlevel['id']) {
								continue;
							}
							if ($oldlevel['ordercount'] > $newlevel['ordercount']) {
								continue;
							}
						}
					} else if ($leveltype == 3) {
						$ordercount = $agent_info['order13'];
						$newlevel = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$ordercount} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
						if (empty($newlevel)) {
							continue;
						}
						if (!empty($oldlevel['id'])) {
							if ($oldlevel['id'] == $newlevel['id']) {
								continue;
							}
							if ($oldlevel['ordercount'] > $newlevel['ordercount']) {
								continue;
							}
						}
					}
					pdo_update('sz_yi_member', array('agentlevel' => $newlevel['id']), array('id' => $agent['id']));
					$this->sendMessage($agent['openid'], array('nickname' => $agent['nickname'], 'oldlevel' => $oldlevel, 'newlevel' => $newlevel,), TM_COMMISSION_UPGRADE);
				}
			}
			$pluginbonus = p("bonus");
			if(!empty($pluginbonus)){
				$pluginbonus->upgradeLevelByAgent($openid);
			}
		}
		//会员根据
		function upgradeLevelByAgent($openid)
		{
			global $_W;
			if (empty($openid)) {
				return false;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return false;
			}
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			
			$pluginchannel = p('channel');
			if(!empty($pluginchannel)){
				$channel_set = $pluginchannel->getSet();
				if (empty($member['ischannel'])) {
					if ($set['become_condition'] == 3 || $set['become_condition'] == 4) {
						$pluginchannel->becomeChannelByOrder($openid, $orderid);
					} elseif ($set['become_condition'] == 2) {
						$pluginchannel->becomeChannelByAgent($openid);
					} elseif ($set['become_condition'] == 5) {
						$pluginchannel->becomeChannelByGood($openid, $orderid);
					}				
				} else {
					if ($channel_set['become'] == 1) {
						$pluginchannel->upgradelevelByGood($openid,$orderid);
					} elseif ($channel_set['become'] == 2) {
						$pluginchannel->upgradelevelByOther($openid,$orderid);
					}
				}
				
			}
			$leveltype = intval($set['leveltype']);
			if ($leveltype < 6 || $leveltype > 9) {
				return;
			}
			$agent_info = $this->getInfo($member['id'], array());
			if ($leveltype == 6 || $leveltype == 8) {
				$agents = array($member);
				if (!empty($member['agentid'])) {
					$m1 = m('member')->getMember($member['agentid']);
					if (!empty($m1)) {
						$agents[] = $m1;
						if (!empty($m1['agentid']) && $m1['isagent'] == 1 && $m1['status'] == 1) {
							$m2 = m('member')->getMember($m1['agentid']);
							if (!empty($m2) && $m2['isagent'] == 1 && $m2['status'] == 1) {
								$agents[] = $m2;
							}
						}
					}
				}
				if (empty($agents)) {
					return;
				}
				foreach ($agents as $agent) {
					$agent_info = $this->getInfo($agent['id'], array());
					if (!empty($agent_info['agentnotupgrade'])) {
						continue;
					}
					$oldlevel = $this->getLevel($agent['openid']);
					if (empty($oldlevel['id'])) {
						$oldlevel = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
					}
					if ($leveltype == 6) {
						$below1_ids = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid']), 'id');
						$below_count = count($below1_ids);
						if (!empty($below1_ids)) {
							$below2_ids = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where agentid in( ' . implode(',', array_keys($below1_ids)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
							$below_count += count($below2_ids);
							if (!empty($below2_ids)) {
								$below3_ids = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where agentid in( ' . implode(',', array_keys($below2_ids)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
								$below_count += count($below3_ids);
							}
						}
						$newlevel = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$below_count} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
					} else if ($leveltype == 8) {
						$below_count = $agent_info['level1'] + $agent_info['level2'] + $agent_info['level3'];
						$newlevel = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$below_count} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
					}
					if (empty($newlevel)) {
						continue;
					}
					if ($newlevel['id'] == $oldlevel['id']) {
						continue;
					}
					if (!empty($oldlevel['id'])) {
						if ($oldlevel['downcount'] > $newlevel['downcount']) {
							continue;
						}
					}
					pdo_update('sz_yi_member', array('agentlevel' => $newlevel['id']), array('id' => $agent['id']));
					$this->sendMessage($agent['openid'], array('nickname' => $agent['nickname'], 'oldlevel' => $oldlevel, 'newlevel' => $newlevel,), TM_COMMISSION_UPGRADE);
				}
			} else {
				if (!empty($member['agentnotupgrade'])) {
					return;
				}
				$oldlevel = $this->getLevel($member['openid']);
				if (empty($oldlevel['id'])) {
					$oldlevel = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
				}
				if ($leveltype == 7) {
					$below_count = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid']));
					$newlevel = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$below_count} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
				} else if ($leveltype == 9) {
					$below_count = $agent_info['level1'];
					$newlevel = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$below_count} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
				}
				if (empty($newlevel)) {
					return;
				}
				if ($newlevel['id'] == $oldlevel['id']) {
					return;
				}
				if (!empty($oldlevel['id'])) {
					if ($oldlevel['downcount'] > $newlevel['downcount']) {
						return;
					}
				}
				pdo_update('sz_yi_member', array('agentlevel' => $newlevel['id']), array('id' => $member['id']));
				$this->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'oldlevel' => $oldlevel, 'newlevel' => $newlevel,), TM_COMMISSION_UPGRADE);
			}
			$pluginbonus = p("bonus");
			if(!empty($pluginbonus)){
				$pluginbonus->upgradeLevelByAgent($openid);
			}
		}
		//根据可提现佣金升级
		function upgradeLevelByCommissionOK($openid)
		{
			global $_W;
			if (empty($openid)) {
				return false;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return false;
			}
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			
			$leveltype = intval($set['leveltype']);
			if ($leveltype != 10) {
				return;
			}
			if (!empty($member['agentnotupgrade'])) {
				return;
			}
			$oldlevel = $this->getLevel($member['openid']);
			if (empty($oldlevel['id'])) {
				$oldlevel = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
			}
			$info = $this->getInfo($member['id'], array('pay'));
			$commission_pay = $info['commission_pay'];
			$newlevel = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$commission_pay} >= commissionmoney and commissionmoney>0  order by commissionmoney desc limit 1", array(':uniacid' => $_W['uniacid']));
			if (empty($newlevel)) {
				return;
			}
			if ($oldlevel['id'] == $newlevel['id']) {
				return;
			}
			if (!empty($oldlevel['id'])) {
				if ($oldlevel['commissionmoney'] > $newlevel['commissionmoney']) {
					return;
				}
			}
			pdo_update('sz_yi_member', array('agentlevel' => $newlevel['id']), array('id' => $member['id']));
			$this->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'oldlevel' => $oldlevel, 'newlevel' => $newlevel,), TM_COMMISSION_UPGRADE);
			$pluginbonus = p("bonus");
			if(!empty($pluginbonus)){
				$pluginbonus->upgradeLevelByAgent($openid);
			}
		}
		//发送消息
		function sendMessage($openid = '', $data = array(), $message_type = '')
		{
			global $_W, $_GPC;
			$set = $this->getSet();
			$tm = $set['tm'];
			$templateid = $tm['templateid'];
			$member = m('member')->getMember($openid);
			$usernotice = unserialize($member['noticeset']);
			if (!is_array($usernotice)) {
				$usernotice = array();
			}
			if ($message_type == TM_COMMISSION_AGENT_NEW && !empty($tm['commission_agent_new']) && empty($usernotice['commission_agent_new'])) {
				$message = $tm['commission_agent_new'];
				$message = str_replace('[昵称]', $data['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', $data['childtime']), $message);
				$msg = array('keyword1' => array('value' => !empty($tm['commission_agent_newtitle']) ? $tm['commission_agent_newtitle'] : '新增下线通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
				if (!empty($templateid)) {
					m('message')->sendTplNotice($openid, $templateid, $msg);
				} else {
					m('message')->sendCustomNotice($openid, $msg);
				}
			} else if ($message_type == TM_COMMISSION_ORDER_PAY && !empty($tm['commission_order_pay']) && empty($usernotice['commission_order_pay'])) {
				$message = $tm['commission_order_pay'];
				$message = str_replace('[昵称]', $data['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', $data['paytime']), $message);
				$message = str_replace('[订单编号]', $data['ordersn'], $message);
				$message = str_replace('[订单金额]', $data['price'], $message);
				$message = str_replace('[佣金金额]', $data['commission'], $message);
				$message = str_replace('[商品详情]', $data['goods'], $message);
				$msg = array('keyword1' => array('value' => !empty($tm['commission_order_paytitle']) ? $tm['commission_order_paytitle'] : '下线付款通知'), 'keyword2' => array('value' => $message));
				if (!empty($templateid)) {
					m('message')->sendTplNotice($openid, $templateid, $msg);
				} else {
					m('message')->sendCustomNotice($openid, $msg);
				}
			} else if ($message_type == TM_COMMISSION_ORDER_FINISH && !empty($tm['commission_order_finish']) && empty($usernotice['commission_order_finish'])) {
				$message = $tm['commission_order_finish'];
				$message = str_replace('[昵称]', $data['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', $data['finishtime']), $message);
				$message = str_replace('[订单编号]', $data['ordersn'], $message);
				$message = str_replace('[订单金额]', $data['price'], $message);
				$message = str_replace('[佣金金额]', $data['commission'], $message);
				$message = str_replace('[商品详情]', $data['goods'], $message);
				$msg = array('keyword1' => array('value' => !empty($tm['commission_order_finishtitle']) ? $tm['commission_order_finishtitle'] : '下线确认收货通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
				if (!empty($templateid)) {
					m('message')->sendTplNotice($openid, $templateid, $msg);
				} else {
					m('message')->sendCustomNotice($openid, $msg);
				}
			} else if ($message_type == TM_COMMISSION_APPLY && !empty($tm['commission_apply']) && empty($usernotice['commission_apply'])) {
				$message = $tm['commission_apply'];
				$message = str_replace('[昵称]', $member['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[金额]', $data['commission'], $message);
				$message = str_replace('[提现方式]', $data['type'], $message);
				$msg = array('keyword1' => array('value' => !empty($tm['commission_applytitle']) ? $tm['commission_applytitle'] : '提现申请提交成功', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
				if (!empty($templateid)) {
					m('message')->sendTplNotice($openid, $templateid, $msg);
				} else {
					m('message')->sendCustomNotice($openid, $msg);
				}
			} else if ($message_type == TM_COMMISSION_CHECK && !empty($tm['commission_check']) && empty($usernotice['commission_check'])) {
				$message = $tm['commission_check'];
				$message = str_replace('[昵称]', $member['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[金额]', $data['commission'], $message);
				$message = str_replace('[提现方式]', $data['type'], $message);
				$msg = array('keyword1' => array('value' => !empty($tm['commission_checktitle']) ? $tm['commission_checktitle'] : '提现申请审核处理完成', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
				if (!empty($templateid)) {
					m('message')->sendTplNotice($openid, $templateid, $msg);
				} else {
					m('message')->sendCustomNotice($openid, $msg);
				}
			} else if ($message_type == TM_COMMISSION_PAY && !empty($tm['commission_pay']) && empty($usernotice['commission_pay'])) {
				$message = $tm['commission_pay'];
				$message = str_replace('[昵称]', $member['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[金额]', $data['commission'], $message);
				$message = str_replace('[提现方式]', $data['type'], $message);
				$message = str_replace('[微信比例]', $set['withdraw_wechat'], $message);
				$message = str_replace('[商城余额比例]', $set['withdraw_balance'], $message);
				$message = str_replace('[税费和服务费比例]', $set['withdraw_factorage'], $message);
				
				$msg = array('keyword1' => array('value' => !empty($tm['commission_paytitle']) ? $tm['commission_paytitle'] : '佣金打款通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
				if (!empty($templateid)) {
					m('message')->sendTplNotice($openid, $templateid, $msg);
				} else {
					m('message')->sendCustomNotice($openid, $msg);
				}
			} else if ($message_type == TM_COMMISSION_UPGRADE && !empty($tm['commission_upgrade']) && empty($usernotice['commission_upgrade'])) {
				$message = $tm['commission_upgrade'];
				$message = str_replace('[昵称]', $member['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[旧等级]', $data['oldlevel']['levelname'], $message);
				$message = str_replace('[旧一级分销比例]', $data['oldlevel']['commission1'] . '%', $message);
				$message = str_replace('[旧二级分销比例]', $data['oldlevel']['commission2'] . '%', $message);
				$message = str_replace('[旧三级分销比例]', $data['oldlevel']['commission3'] . '%', $message);
				$message = str_replace('[新等级]', $data['newlevel']['levelname'], $message);
				$message = str_replace('[新一级分销比例]', $data['newlevel']['commission1'] . '%', $message);
				$message = str_replace('[新二级分销比例]', $data['newlevel']['commission2'] . '%', $message);
				$message = str_replace('[新三级分销比例]', $data['newlevel']['commission3'] . '%', $message);
				$msg = array('keyword1' => array('value' => !empty($tm['commission_upgradetitle']) ? $tm['commission_upgradetitle'] : '分销等级升级通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
				if (!empty($templateid)) {
					m('message')->sendTplNotice($openid, $templateid, $msg);
				} else {
					m('message')->sendCustomNotice($openid, $msg);
				}
			} else if ($message_type == TM_COMMISSION_BECOME && !empty($tm['commission_become']) && empty($usernotice['commission_become'])) {
				/*m('member')->setCredit($openid, $credittype = 'credit1', $set['own_integral'], $log = array());*/
				$message = $tm['commission_become'];
				$message = str_replace('[昵称]', $data['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', $data['agenttime']), $message);
				/*$message = str_replace('[积分]', $set['own_integral'], $message);*/
				$msg = array('keyword1' => array('value' => !empty($tm['commission_becometitle']) ? $tm['commission_becometitle'] : '成为分销商通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
				if (!empty($templateid)) {
					m('message')->sendTplNotice($openid, $templateid, $msg);
				} else {
					m('message')->sendCustomNotice($openid, $msg);
				}
				/*if (!empty($member['agentid'])) {
					$agent_info = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid={$_W['uniacid']} and id={$member['agentid']}");
					m('member')->setCredit($agent_info['openid'], $credittype = 'credit1', $set['up_integral'], $log = array());
					$message = "[" . $member['realname'] . "]成为分销商，您获得推广奖励：" . $set['up_integral'] . "积分";
					$msg = array('keyword1' => array('value' => '推广奖励通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
					m('message')->sendCustomNotice($agent_info['openid'], $msg);
				}*/
			}
		}

		function perms()
		{
			return array('commission' => array('text' => $this->getName(), 'isplugin' => true, 'child' => array('cover' => array('text' => '入口设置'), 'agent' => array('text' => '分销商', 'view' => '浏览', 'check' => '审核-log', 'edit' => '修改-log', 'agentblack' => '黑名单操作-log', 'delete' => '删除-log', 'user' => '查看下线', 'order' => '查看推广订单(还需有订单权限)', 'changeagent' => '设置分销商'), 'level' => array('text' => '分销商等级', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'apply' => array('text' => '佣金审核', 'view1' => '浏览待审核', 'view2' => '浏览已审核', 'view3' => '浏览已打款', 'view_1' => '浏览无效', 'export1' => '导出待审核-log', 'export2' => '导出已审核-log', 'export3' => '导出已打款-log', 'export_1' => '导出无效-log', 'check' => '审核-log', 'pay' => '打款-log', 'cancel' => '重新审核-log'), 'notice' => array('text' => '通知设置-log'), 'increase' => array('text' => '分销商趋势图'), 'changecommission' => array('text' => '修改佣金-log'), 'set' => array('text' => '基础设置-log'))));
		}
		//购买商品升级
        function upgradeLevelByGood($orderid)
        {
            // 如果购买的商品可以指定成为分销商
            global $_W;
            $set = $this->getSet();
            if (!$set['upgrade_by_good']) {
                return;
            }

            $goods = pdo_fetch('select g.commission_level_id from ' . tablename('sz_yi_order_goods') . ' AS og, ' . tablename('sz_yi_goods') . ' AS g WHERE og.goodsid = g.id AND og.orderid=:orderid AND og.uniacid=:uniacid LIMIT 1', array(
                ':orderid' => $orderid,
                ':uniacid' => $_W['uniacid']
            ));
            $goodsLevel = $goods['commission_level_id'];
            if ($goodsLevel) {
                $levels = $this->getLevels();
                foreach ($levels as $level) {
                    if ($level['id'] == $goodsLevel) {
                        $goodsLevelCommission1 = $level['commission1'];
                        $goodsLevelCommission2 = $level['commission2'];
                        $goodsLevelCommission3 = $level['commission3'];
                    }
                }
                $openid = pdo_fetchcolumn('select openid from ' . tablename('sz_yi_order') . ' where uniacid=:uniacid and id=:orderid', array(
                    ':uniacid' => $_W['uniacid'],
                    ':orderid' => $orderid
                ));
                $userLevel = $this->getLevel($openid);
                // 如果原来用户没有分销商等级或者分销商等级较低，则变为该商品的分销商等级
                if (!$userLevel
                 || $userLevel['commission1'] < $goodsLevelCommission1
                 || $userLevel['commission2'] < $goodsLevelCommission2
                 || $userLevel['commission3'] < $goodsLevelCommission3
                ) {
                    pdo_update('sz_yi_member', array('agentlevel' => $goodsLevel), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
                }
            }
        }
	}
}