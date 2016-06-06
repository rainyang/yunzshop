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
		public function getSet()
		{
			$set = parent::getSet();
			$set['texts'] = array('agent' => empty($set['texts']['agent']) ? '分销商' : $set['texts']['agent'], 'shop' => empty($set['texts']['shop']) ? '小店' : $set['texts']['shop'], 'myshop' => empty($set['texts']['myshop']) ? '我的小店' : $set['texts']['myshop'], 'center' => empty($set['texts']['center']) ? '分销中心' : $set['texts']['center'], 'become' => empty($set['texts']['become']) ? '成为分销商' : $set['texts']['become'], 'withdraw' => empty($set['texts']['withdraw']) ? '提现' : $set['texts']['withdraw'], 'commission' => empty($set['texts']['commission']) ? '佣金' : $set['texts']['commission'], 'commission1' => empty($set['texts']['commission1']) ? '分销佣金' : $set['texts']['commission1'], 'commission_total' => empty($set['texts']['commission_total']) ? '累计佣金' : $set['texts']['commission_total'], 'commission_ok' => empty($set['texts']['commission_ok']) ? '可提现佣金' : $set['texts']['commission_ok'], 'commission_apply' => empty($set['texts']['commission_apply']) ? '已申请佣金' : $set['texts']['commission_apply'], 'commission_check' => empty($set['texts']['commission_check']) ? '待打款佣金' : $set['texts']['commission_check'], 'commission_lock' => empty($set['texts']['commission_lock']) ? '未结算佣金' : $set['texts']['commission_lock'], 'commission_detail' => empty($set['texts']['commission_detail']) ? '佣金明细' : $set['texts']['commission_detail'], 'commission_pay' => empty($set['texts']['commission_pay']) ? '成功提现佣金' : $set['texts']['commission_pay'], 'order' => empty($set['texts']['order']) ? '分销订单' : $set['texts']['order'], 'myteam' => empty($set['texts']['myteam']) ? '我的团队' : $set['texts']['myteam'], 'c1' => empty($set['texts']['c1']) ? '一级' : $set['texts']['c1'], 'c2' => empty($set['texts']['c2']) ? '二级' : $set['texts']['c2'], 'c3' => empty($set['texts']['c3']) ? '三级' : $set['texts']['c3'], 'mycustomer' => empty($set['texts']['mycustomer']) ? '我的客户' : $set['texts']['mycustomer'],);
			return $set;
		}

		public function calculate($orderid = 0, $update = true)
		{
			global $_W;
			$set = $this->getSet();
			$levels = $this->getLevels();
			$agentid = pdo_fetchcolumn('select agentid from ' . tablename('sz_yi_order') . ' where id=:id limit 1', array(':id' => $orderid));
			$goods = pdo_fetchall('select og.id,og.realprice,og.total,g.hascommission,g.nocommission, g.commission1_rate,g.commission1_pay,g.commission2_rate,g.commission2_pay,g.commission3_rate,g.commission3_pay,og.commissions,og.optionid,g.productprice,g.marketprice,g.costprice from ' . tablename('sz_yi_order_goods') . '  og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id = og.goodsid' . ' where og.orderid=:orderid and og.uniacid=:uniacid', array(':orderid' => $orderid, ':uniacid' => $_W['uniacid']));
			if ($set['level'] > 0) {
				foreach ($goods as &$cinfo) {
					$price = $this->calculate_method($cinfo);
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
						pdo_update('sz_yi_order_goods', array('commission1' => iserializer($cinfo['commission1']), 'commission2' => iserializer($cinfo['commission2']), 'commission3' => iserializer($cinfo['commission3']), 'commissions' => iserializer($commissions), 'nocommission' => $cinfo['nocommission']), array('id' => $cinfo['id']));
					}
				}
				unset($cinfo);
			}
			return $goods;
		}

		//Author:ym Date:2016-05-06 Content:分成方式计算		
		public function calculate_method($order_goods){
			global $_W;
			$set = $this->getSet();
			$realprice = $order_goods['realprice'];
			if(empty($set['culate_method'])){
				return $realprice;
			}else{
				
				if($order_goods['optionid'] != 0){
					$option = pdo_fetch('select productprice,marketprice,costprice from ' . tablename('sz_yi_goods_option') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $order_goods['optionid'], ':uniacid' => $_W['uniacid']));
					$productprice = $option['productprice'] * $order_goods['total'];	//原价
					$marketprice  = $option['marketprice'] * $order_goods['total'];		//现价
					$costprice    = $option['costprice'] * $order_goods['total'];	
				}else{
					$productprice = $order_goods['productprice'] * $order_goods['total'];	//原价
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

		public function getOrderCommissions($_var_1 = 0, $_var_16 = 0)
		{
			global $_W;
			$set = $this->getSet();
			$_var_4 = pdo_fetchcolumn('select agentid from ' . tablename('sz_yi_order') . ' where id=:id limit 1', array(':id' => $_var_1));
			$_var_5 = pdo_fetch('select commission1,commission2,commission3 from ' . tablename('sz_yi_order_goods') . ' where id=:id and orderid=:orderid and uniacid=:uniacid and nocommission=0 limit 1', array(':id' => $_var_16, ':orderid' => $_var_1, ':uniacid' => $_W['uniacid']));
			$_var_9 = array('level1' => 0, 'level2' => 0, 'level3' => 0);
			if ($set['level'] > 0) {
				$_var_17 = iunserializer($_var_5['commission1']);
				$_var_18 = iunserializer($_var_5['commission2']);
				$_var_19 = iunserializer($_var_5['commission3']);
				if (!empty($_var_4)) {
					$_var_10 = m('member')->getMember($_var_4);
					if ($_var_10['isagent'] == 1 && $_var_10['status'] == 1) {
						$_var_11 = $this->getLevel($_var_10['openid']);
						$_var_9['level1'] = empty($_var_11) ? round($_var_17['default'], 2) : round($_var_17['level' . $_var_11['id']], 2);
						if (!empty($_var_10['agentid'])) {
							$_var_12 = m('member')->getMember($_var_10['agentid']);
							$_var_13 = $this->getLevel($_var_12['openid']);
							$_var_9['level2'] = empty($_var_13) ? round($_var_18['default'], 2) : round($_var_18['level' . $_var_13['id']], 2);
							if (!empty($_var_12['agentid'])) {
								$_var_14 = m('member')->getMember($_var_12['agentid']);
								$_var_15 = $this->getLevel($_var_14['openid']);
								$_var_9['level3'] = empty($_var_15) ? round($_var_19['default'], 2) : round($_var_19['level' . $_var_15['id']], 2);
							}
						}
					}
				}
			}
			return $_var_9;
		}

		public function getInfo($_var_20, $_var_21 = null)
		{
			if (empty($_var_21) || !is_array($_var_21)) {
				$_var_21 = array();
			}
			global $_W;
			$set = $this->getSet();
			$_var_8 = intval($set['level']);
			$_var_22 = m('member')->getMember($_var_20);
			$_var_23 = $this->getLevel($_var_20);
			$_var_24 = time();
			$_var_25 = intval($set['settledays']) * 3600 * 24;
			$_var_26 = 0;
			$_var_27 = 0;
			$_var_28 = 0;
			$_var_29 = 0;
			$_var_30 = 0;
			$_var_31 = 0;
			$_var_32 = 0;
			$_var_33 = 0;
			$_var_34 = 0;
			$_var_35 = 0;
			$_var_36 = 0;
			$_var_37 = 0;
			$_var_38 = 0;
			$_var_39 = 0;
			$_var_40 = 0;
			$_var_41 = 0;
			$_var_42 = 0;
			$_var_43 = 0;
			$_var_44 = 0;
			$_var_45 = 0;
			$_var_46 = 0;
			$_var_47 = 0;
			$_var_48 = 0;
			$_var_49 = 0;
			$_var_50 = 0;
			$_var_51 = 0;
			$_var_52 = 0;
			$_var_53 = 0;
			$myoedermoney = 0;
			$myordercount = 0;
			if ($_var_8 >= 1) {
				if (in_array('ordercount0', $_var_21)) {
					$_var_54 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=0 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_22['id']));
					$_var_42 += $_var_54['ordercount'];
					$_var_27 += $_var_54['ordercount'];
					$_var_28 += $_var_54['ordermoney'];
				}
				if (in_array('ordercount', $_var_21)) {
					$_var_54 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=1 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_22['id']));
					$_var_45 += $_var_54['ordercount'];
					$_var_29 += $_var_54['ordercount'];
					$_var_30 += $_var_54['ordermoney'];
				}
				if (in_array('ordercount3', $_var_21)) {
					$_var_55 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=3 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_22['id']));
					$_var_48 += $_var_55['ordercount'];
					$_var_31 += $_var_55['ordercount'];
					$_var_32 += $_var_55['ordermoney'];
					$_var_51 += $_var_55['ordermoney'];
				}
				if (in_array('total', $_var_21)) {
					$_var_56 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_22['id']));
					foreach ($_var_56 as $_var_57) {
						$_var_9 = iunserializer($_var_57['commissions']);
						$_var_58 = iunserializer($_var_57['commission1']);
						if (empty($_var_9)) {
							$_var_33 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
						} else {
							$_var_33 += isset($_var_9['level1']) ? floatval($_var_9['level1']) : 0;
						}
					}
				}
				if (in_array('ok', $_var_21)) {
					$_var_56 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . " where o.agentid=:agentid and o.status>=3 and og.nocommission=0 and ({$_var_24} - o.createtime > {$_var_25}) and og.status1=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_22['id']));
					foreach ($_var_56 as $_var_57) {
						$_var_9 = iunserializer($_var_57['commissions']);
						$_var_58 = iunserializer($_var_57['commission1']);
						if (empty($_var_9)) {
							$_var_34 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
						} else {
							$_var_34 += isset($_var_9['level1']) ? $_var_9['level1'] : 0;
						}
					}
				}
				if (in_array('lock', $_var_21)) {
					$_var_59 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . " where o.agentid=:agentid and o.status>=3 and og.nocommission=0 and ({$_var_24} - o.createtime <= {$_var_25})  and og.status1=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_22['id']));
					foreach ($_var_59 as $_var_57) {
						$_var_9 = iunserializer($_var_57['commissions']);
						$_var_58 = iunserializer($_var_57['commission1']);
						if (empty($_var_9)) {
							$_var_37 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
						} else {
							$_var_37 += isset($_var_9['level1']) ? $_var_9['level1'] : 0;
						}
					}
				}
				if (in_array('apply', $_var_21)) {
					$_var_60 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_22['id']));
					foreach ($_var_60 as $_var_57) {
						$_var_9 = iunserializer($_var_57['commissions']);
						$_var_58 = iunserializer($_var_57['commission1']);
						if (empty($_var_9)) {
							$_var_35 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
						} else {
							$_var_35 += isset($_var_9['level1']) ? $_var_9['level1'] : 0;
						}
					}
				}
				if (in_array('check', $_var_21)) {
					$_var_60 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=2 and og.nocommission=0 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_22['id']));
					foreach ($_var_60 as $_var_57) {
						$_var_9 = iunserializer($_var_57['commissions']);
						$_var_58 = iunserializer($_var_57['commission1']);
						if (empty($_var_9)) {
							$_var_36 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
						} else {
							$_var_36 += isset($_var_9['level1']) ? $_var_9['level1'] : 0;
						}
					}
				}
				if (in_array('pay', $_var_21)) {
					$_var_60 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=3 and og.nocommission=0 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_22['id']));
					foreach ($_var_60 as $_var_57) {
						$_var_9 = iunserializer($_var_57['commissions']);
						$_var_58 = iunserializer($_var_57['commission1']);
						if (empty($_var_9)) {
							$_var_38 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
						} else {
							$_var_38 += isset($_var_9['level1']) ? $_var_9['level1'] : 0;
						}
					}
				}
				$_var_61 = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_22['id']), 'id');
				$_var_39 = count($_var_61);
				$_var_26 += $_var_39;
			}
			if ($_var_8 >= 2) {
				if ($_var_39 > 0) {
					if (in_array('ordercount0', $_var_21)) {
						$_var_62 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($_var_61)) . ')  and o.status>=0 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$_var_43 += $_var_62['ordercount'];
						$_var_27 += $_var_62['ordercount'];
						$_var_28 += $_var_62['ordermoney'];
					}
					if (in_array('ordercount', $_var_21)) {
						$_var_62 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($_var_61)) . ')  and o.status>=1 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$_var_46 += $_var_62['ordercount'];
						$_var_29 += $_var_62['ordercount'];
						$_var_30 += $_var_62['ordermoney'];
					}
					if (in_array('ordercount3', $_var_21)) {
						$_var_63 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($_var_61)) . ')  and o.status>=3 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$_var_49 += $_var_63['ordercount'];
						$_var_31 += $_var_63['ordercount'];
						$_var_32 += $_var_63['ordermoney'];
						$_var_52 += $_var_63['ordermoney'];
					}
					if (in_array('total', $_var_21)) {
						$_var_64 = pdo_fetchall('select og.commission2,og.commissions from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($_var_61)) . ')  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_64 as $_var_57) {
							$_var_9 = iunserializer($_var_57['commissions']);
							$_var_58 = iunserializer($_var_57['commission2']);
							if (empty($_var_9)) {
								$_var_33 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
							} else {
								$_var_33 += isset($_var_9['level2']) ? $_var_9['level2'] : 0;
							}
						}
					}
					if (in_array('ok', $_var_21)) {
						$_var_64 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($_var_61)) . ")  and ({$_var_24} - o.createtime > {$_var_25}) and o.status>=3 and og.status2=0 and og.nocommission=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
						foreach ($_var_64 as $_var_57) {
							$_var_9 = iunserializer($_var_57['commissions']);
							$_var_58 = iunserializer($_var_57['commission2']);
							if (empty($_var_9)) {
								$_var_34 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
							} else {
								$_var_34 += isset($_var_9['level2']) ? $_var_9['level2'] : 0;
							}
						}
					}
					if (in_array('lock', $_var_21)) {
						$_var_65 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($_var_61)) . ")  and ({$_var_24} - o.createtime <= {$_var_25}) and og.status2=0 and o.status>=3 and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
						foreach ($_var_65 as $_var_57) {
							$_var_9 = iunserializer($_var_57['commissions']);
							$_var_58 = iunserializer($_var_57['commission2']);
							if (empty($_var_9)) {
								$_var_37 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
							} else {
								$_var_37 += isset($_var_9['level2']) ? $_var_9['level2'] : 0;
							}
						}
					}
					if (in_array('apply', $_var_21)) {
						$_var_66 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($_var_61)) . ')  and o.status>=3 and og.status2=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_66 as $_var_57) {
							$_var_9 = iunserializer($_var_57['commissions']);
							$_var_58 = iunserializer($_var_57['commission2']);
							if (empty($_var_9)) {
								$_var_35 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
							} else {
								$_var_35 += isset($_var_9['level2']) ? $_var_9['level2'] : 0;
							}
						}
					}
					if (in_array('check', $_var_21)) {
						$_var_67 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($_var_61)) . ')  and o.status>=3 and og.status2=2 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_67 as $_var_57) {
							$_var_9 = iunserializer($_var_57['commissions']);
							$_var_58 = iunserializer($_var_57['commission2']);
							if (empty($_var_9)) {
								$_var_36 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
							} else {
								$_var_36 += isset($_var_9['level2']) ? $_var_9['level2'] : 0;
							}
						}
					}
					if (in_array('pay', $_var_21)) {
						$_var_67 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($_var_61)) . ')  and o.status>=3 and og.status2=3 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_67 as $_var_57) {
							$_var_9 = iunserializer($_var_57['commissions']);
							$_var_58 = iunserializer($_var_57['commission2']);
							if (empty($_var_9)) {
								$_var_38 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
							} else {
								$_var_38 += isset($_var_9['level2']) ? $_var_9['level2'] : 0;
							}
						}
					}
					$_var_68 = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where agentid in( ' . implode(',', array_keys($_var_61)) . ') and isagent=1 and status=1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
					$_var_40 = count($_var_68);
					$_var_26 += $_var_40;
				}
			}
			if ($_var_8 >= 3) {
				if ($_var_40 > 0) {
					if (in_array('ordercount0', $_var_21)) {
						$_var_69 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($_var_68)) . ')  and o.status>=0 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$_var_44 += $_var_69['ordercount'];
						$_var_27 += $_var_69['ordercount'];
						$_var_28 += $_var_69['ordermoney'];
					}
					if (in_array('ordercount', $_var_21)) {
						$_var_69 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($_var_68)) . ')  and o.status>=1 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$_var_47 += $_var_69['ordercount'];
						$_var_29 += $_var_69['ordercount'];
						$_var_30 += $_var_69['ordermoney'];
					}
					if (in_array('ordercount3', $_var_21)) {
						$_var_70 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($_var_68)) . ')  and o.status>=3 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$_var_50 += $_var_70['ordercount'];
						$_var_31 += $_var_70['ordercount'];
						$_var_32 += $_var_70['ordermoney'];
						$_var_53 += $_var_69['ordermoney'];
					}
					if (in_array('total', $_var_21)) {
						$_var_71 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($_var_68)) . ')  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_71 as $_var_57) {
							$_var_9 = iunserializer($_var_57['commissions']);
							$_var_58 = iunserializer($_var_57['commission3']);
							if (empty($_var_9)) {
								$_var_33 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
							} else {
								$_var_33 += isset($_var_9['level3']) ? $_var_9['level3'] : 0;
							}
						}
					}
					if (in_array('ok', $_var_21)) {
						$_var_71 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($_var_68)) . ")  and ({$_var_24} - o.createtime > {$_var_25}) and o.status>=3 and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
						foreach ($_var_71 as $_var_57) {
							$_var_9 = iunserializer($_var_57['commissions']);
							$_var_58 = iunserializer($_var_57['commission3']);
							if (empty($_var_9)) {
								$_var_34 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
							} else {
								$_var_34 += isset($_var_9['level3']) ? $_var_9['level3'] : 0;
							}
						}
					}
					if (in_array('lock', $_var_21)) {
						$_var_72 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($_var_68)) . ")  and o.status>=3 and ({$_var_24} - o.createtime > {$_var_25}) and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
						foreach ($_var_72 as $_var_57) {
							$_var_9 = iunserializer($_var_57['commissions']);
							$_var_58 = iunserializer($_var_57['commission3']);
							if (empty($_var_9)) {
								$_var_37 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
							} else {
								$_var_37 += isset($_var_9['level3']) ? $_var_9['level3'] : 0;
							}
						}
					}
					if (in_array('apply', $_var_21)) {
						$_var_73 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($_var_68)) . ')  and o.status>=3 and og.status3=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_73 as $_var_57) {
							$_var_9 = iunserializer($_var_57['commissions']);
							$_var_58 = iunserializer($_var_57['commission3']);
							if (empty($_var_9)) {
								$_var_35 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
							} else {
								$_var_35 += isset($_var_9['level3']) ? $_var_9['level3'] : 0;
							}
						}
					}
					if (in_array('check', $_var_21)) {
						$_var_74 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($_var_68)) . ')  and o.status>=3 and og.status3=2 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_74 as $_var_57) {
							$_var_9 = iunserializer($_var_57['commissions']);
							$_var_58 = iunserializer($_var_57['commission3']);
							if (empty($_var_9)) {
								$_var_36 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
							} else {
								$_var_36 += isset($_var_9['level3']) ? $_var_9['level3'] : 0;
							}
						}
					}
					if (in_array('pay', $_var_21)) {
						$_var_74 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($_var_68)) . ')  and o.status>=3 and og.status3=3 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_74 as $_var_57) {
							$_var_9 = iunserializer($_var_57['commissions']);
							$_var_58 = iunserializer($_var_57['commission3']);
							if (empty($_var_9)) {
								$_var_38 += isset($_var_58['level' . $_var_23['id']]) ? $_var_58['level' . $_var_23['id']] : $_var_58['default'];
							} else {
								$_var_38 += isset($_var_9['level3']) ? $_var_9['level3'] : 0;
							}
						}
					}
					$_var_75 = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where uniacid=:uniacid and agentid in( ' . implode(',', array_keys($_var_68)) . ') and isagent=1 and status=1', array(':uniacid' => $_W['uniacid']), 'id');
					$_var_41 = count($_var_75);
					$_var_26 += $_var_41;
				}
			}
			//Author:ym Date:2016-04-07 Content:自购完成订单
			if (in_array('myorder', $_var_21)) {
				$myorder = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.openid=:openid and o.status>=3 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_var_22['openid']));
				//Author:ym Date:2016-04-07 Content:自购订单金额
				$myoedermoney = $myorder['ordermoney'];
				//Author:ym Date:2016-04-07 Content:自购订单数量
				$myordercount = $myorder['ordercount'];
			}

			$_var_22['agentcount'] = $_var_26;
			$_var_22['ordercount'] = $_var_29;
			$_var_22['ordermoney'] = $_var_30;
			$_var_22['order1'] = $_var_45;
			$_var_22['order2'] = $_var_46;
			$_var_22['order3'] = $_var_47;
			$_var_22['ordercount3'] = $_var_31;
			$_var_22['ordermoney3'] = $_var_32;
			$_var_22['order13'] = $_var_48;
			$_var_22['order23'] = $_var_49;
			$_var_22['order33'] = $_var_50;
			$_var_22['order13money'] = $_var_51;
			$_var_22['order23money'] = $_var_52;
			$_var_22['order33money'] = $_var_53;
			$_var_22['ordercount0'] = $_var_27;
			$_var_22['ordermoney0'] = $_var_28;
			$_var_22['order10'] = $_var_42;
			$_var_22['order20'] = $_var_43;
			$_var_22['order30'] = $_var_44;
			$_var_22['commission_total'] = round($_var_33, 2);
			$_var_22['commission_ok'] = round($_var_34, 2);
			$_var_22['commission_lock'] = round($_var_37, 2);
			$_var_22['commission_apply'] = round($_var_35, 2);
			$_var_22['commission_check'] = round($_var_36, 2);
			$_var_22['commission_pay'] = round($_var_38, 2);
			$_var_22['level1'] = $_var_39;
			$_var_22['level1_agentids'] = $_var_61;
			$_var_22['level2'] = $_var_40;
			$_var_22['level2_agentids'] = $_var_68;
			$_var_22['level3'] = $_var_41;
			$_var_22['level3_agentids'] = $_var_75;
			$_var_22['agenttime'] = date('Y-m-d H:i', $_var_22['agenttime']);
			$_var_22['myoedermoney'] = $myoedermoney;
			$_var_22['myordercount'] = $myordercount;
			return $_var_22;
		}

		public function getAgents($_var_1 = 0)
		{
			global $_W, $_GPC;
			$_var_76 = array();
			$_var_77 = pdo_fetch('select id,agentid,openid from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $_var_1, ':uniacid' => $_W['uniacid']));
			if (empty($_var_77)) {
				return $_var_76;
			}
			$_var_10 = m('member')->getMember($_var_77['agentid']);
			if (!empty($_var_10) && $_var_10['isagent'] == 1 && $_var_10['status'] == 1) {
				$_var_76[] = $_var_10;
				if (!empty($_var_10['agentid'])) {
					$_var_12 = m('member')->getMember($_var_10['agentid']);
					if (!empty($_var_12) && $_var_12['isagent'] == 1 && $_var_12['status'] == 1) {
						$_var_76[] = $_var_12;
						if (!empty($_var_12['agentid'])) {
							$_var_14 = m('member')->getMember($_var_12['agentid']);
							if (!empty($_var_14) && $_var_14['isagent'] == 1 && $_var_14['status'] == 1) {
								$_var_76[] = $_var_14;
							}
						}
					}
				}
			}
			return $_var_76;
		}

		public function isAgent($_var_20)
		{
			if (empty($_var_20)) {
				return false;
			}
			if (is_array($_var_20)) {
				return $_var_20['isagent'] == 1 && $_var_20['status'] == 1;
			}
			$_var_22 = m('member')->getMember($_var_20);
			return $_var_22['isagent'] == 1 && $_var_22['status'] == 1;
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

		public function getCommission($_var_5)
		{
			global $_W;
			$set = $this->getSet();
			$_var_58 = 0;
			if ($_var_5['hascommission'] == 1) {
				$_var_58 = $set['level'] >= 1 ? ($_var_5['commission1_rate'] > 0 ? ($_var_5['commission1_rate'] * $_var_5['marketprice'] / 100) : $_var_5['commission1_pay']) : 0;
			} else {
				$_var_20 = m('user')->getOpenid();
				$_var_8 = $this->getLevel($_var_20);
				$price = $this->calculate_goods_method($_var_5);
				if (!empty($_var_8)) {
					$_var_58 = $set['level'] >= 1 ? round($_var_8['commission1'] * $price / 100, 2) : 0;
				} else {
					$_var_58 = $set['level'] >= 1 ? round($set['commission1'] * $price / 100, 2) : 0;
				}
			}
			return $_var_58;
		}

		public function createMyShopQrcode($_var_78 = 0, $_var_79 = 0)
		{
			global $_W;
			$_var_80 = IA_ROOT . '/addons/sz_yi/data/qrcode/' . $_W['uniacid'];
			if (!is_dir($_var_80)) {
				load()->func('file');
				mkdirs($_var_80);
			}
			$_var_81 = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=sz_yi&do=plugin&p=commission&method=myshop&mid=' . $_var_78;
			if (!empty($_var_79)) {
				$_var_81 .= '&posterid=' . $_var_79;
			}
			$_var_82 = 'myshop_' . $_var_79 . '_' . $_var_78 . '.png';
			$_var_83 = $_var_80 . '/' . $_var_82;
			if (!is_file($_var_83)) {
				require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
				QRcode::png($_var_81, $_var_83, QR_ECLEVEL_H, 4);
			}
			return $_W['siteroot'] . 'addons/sz_yi/data/qrcode/' . $_W['uniacid'] . '/' . $_var_82;
		}

		private function createImage($_var_81)
		{
			load()->func('communication');
			$_var_84 = ihttp_request($_var_81);
			return imagecreatefromstring($_var_84['content']);
		}

		public function createGoodsImage($_var_5, $_var_85)
		{
			global $_W, $_GPC;
			$_var_5 = set_medias($_var_5, 'thumb');
			$_var_20 = m('user')->getOpenid();
			$_var_86 = m('member')->getMember($_var_20);
			if ($_var_86['isagent'] == 1 && $_var_86['status'] == 1) {
				$_var_87 = $_var_86;
			} else {
				$_var_78 = intval($_GPC['mid']);
				if (!empty($_var_78)) {
					$_var_87 = m('member')->getMember($_var_78);
				}
			}
			$_var_80 = IA_ROOT . '/addons/sz_yi/data/poster/' . $_W['uniacid'] . '/';
			if (!is_dir($_var_80)) {
				load()->func('file');
				mkdirs($_var_80);
			}
			$_var_88 = empty($_var_5['commission_thumb']) ? $_var_5['thumb'] : tomedia($_var_5['commission_thumb']);
			$_var_89 = md5(json_encode(array('id' => $_var_5['id'], 'marketprice' => $_var_5['marketprice'], 'productprice' => $_var_5['productprice'], 'img' => $_var_88, 'openid' => $_var_20, 'version' => 4)));
			$_var_82 = $_var_89 . '.jpg';
			//echo $_var_80 . $_var_82;exit;
			if (!is_file($_var_80 . $_var_82)) {
				
				set_time_limit(0);
				$_var_90 = IA_ROOT . '/addons/sz_yi/static/fonts/msyh.ttf';
				$_var_91 = imagecreatetruecolor(640, 1225);
				if(!is_weixin()){
					$location_num = 196;
					$_var_92 = imagecreatefromjpeg(IA_ROOT . '/addons/sz_yi/plugin/commission/images/poster_pc.jpg');
				}else{
					$location_num = 50;
					$_var_92 = imagecreatefromjpeg(IA_ROOT . '/addons/sz_yi/plugin/commission/images/poster.jpg');
				}
				$imgusername = $_var_87['realname'] ? $_var_87['realname'] : $_var_87['nickname'];
				$imgusername = $imgusername ? $imgusername : $_var_87['mobile'];
				imagecopy($_var_91, $_var_92, 0, 0, 0, 0, 640, 1225);
				imagedestroy($_var_92);
				$_var_93 = preg_replace('/\\/0$/i', '/96', $_var_87['avatar']);
				$_var_94 = $this->createImage($_var_93);
				$_var_95 = imagesx($_var_94);
				$_var_96 = imagesy($_var_94);
				imagecopyresized($_var_91, $_var_94, 24, 32, 0, 0, 88, 88, $_var_95, $_var_96);
				imagedestroy($_var_94);
				$_var_97 = $this->createImage($_var_88);
				$_var_95 = imagesx($_var_97);
				$_var_96 = imagesy($_var_97);
				imagecopyresized($_var_91, $_var_97, 0, 160, 0, 0, 640, 640, $_var_95, $_var_96);
				imagedestroy($_var_97);
				$_var_98 = imagecreatetruecolor(640, 127);
				imagealphablending($_var_98, false);
				imagesavealpha($_var_98, true);
				$_var_99 = imagecolorallocatealpha($_var_98, 0, 0, 0, 25);
				imagefill($_var_98, 0, 0, $_var_99);
				imagecopy($_var_91, $_var_98, 0, 678, 0, 0, 640, 127);
				imagedestroy($_var_98);
				$_var_100 = tomedia(m('qrcode')->createGoodsQrcode($_var_87['id'], $_var_5['id']));
				$_var_101 = $this->createImage($_var_100);
				$_var_95 = imagesx($_var_101);
				$_var_96 = imagesy($_var_101);
				imagecopyresized($_var_91, $_var_101, $location_num, 835, 0, 0, 250, 250, $_var_95, $_var_96);
				imagedestroy($_var_101);
				$_var_102 = imagecolorallocate($_var_91, 0, 3, 51);
				$_var_103 = imagecolorallocate($_var_91, 240, 102, 0);
				$_var_104 = imagecolorallocate($_var_91, 255, 255, 255);
				$_var_105 = imagecolorallocate($_var_91, 255, 255, 0);
				$_var_106 = '我是';
				imagettftext($_var_91, 20, 0, 150, 70, $_var_102, $_var_90, $_var_106);
				imagettftext($_var_91, 20, 0, 210, 70, $_var_103, $_var_90, $imgusername);
				$_var_107 = '我要为';
				imagettftext($_var_91, 20, 0, 150, 105, $_var_102, $_var_90, $_var_107);
				$_var_108 = $_var_85['name'];
				imagettftext($_var_91, 20, 0, 240, 105, $_var_103, $_var_90, $_var_108);
				$_var_109 = imagettfbbox(20, 0, $_var_90, $_var_108);
				$_var_110 = $_var_109[4] - $_var_109[6];
				$_var_111 = '代言';
				imagettftext($_var_91, 20, 0, 240 + $_var_110 + 10, 105, $_var_102, $_var_90, $_var_111);
				$_var_112 = mb_substr($_var_5['title'], 0, 50, 'utf-8');
				imagettftext($_var_91, 20, 0, 30, 730, $_var_104, $_var_90, $_var_112);
				$_var_113 = '￥' . number_format($_var_5['marketprice'], 2);
				imagettftext($_var_91, 25, 0, 25, 780, $_var_105, $_var_90, $_var_113);
				$_var_109 = imagettfbbox(26, 0, $_var_90, $_var_113);
				$_var_110 = $_var_109[4] - $_var_109[6];
				if ($_var_5['productprice'] > 0) {
					$_var_114 = '￥' . number_format($_var_5['productprice'], 2);
					imagettftext($_var_91, 22, 0, 25 + $_var_110 + 10, 780, $_var_104, $_var_90, $_var_114);
					$_var_115 = 25 + $_var_110 + 10;
					$_var_109 = imagettfbbox(22, 0, $_var_90, $_var_114);
					$_var_110 = $_var_109[4] - $_var_109[6];
					imageline($_var_91, $_var_115, 770, $_var_115 + $_var_110 + 20, 770, $_var_104);
					imageline($_var_91, $_var_115, 771.5, $_var_115 + $_var_110 + 20, 771, $_var_104);
				}
				imagejpeg($_var_91, $_var_80 . $_var_82);
				imagedestroy($_var_91);
			}
			return $_W['siteroot'] . 'addons/sz_yi/data/poster/' . $_W['uniacid'] . '/' . $_var_82;
		}

		public function createShopImage($_var_85)
		{
			global $_W, $_GPC;
			$_var_85 = set_medias($_var_85, 'signimg');
			$_var_80 = IA_ROOT . '/addons/sz_yi/data/poster/' . $_W['uniacid'] . '/';
			if (!is_dir($_var_80)) {
				load()->func('file');
				mkdirs($_var_80);
			}
			$_var_78 = intval($_GPC['mid']);
			$_var_20 = m('user')->getOpenid();
			$_var_86 = m('member')->getMember($_var_20);
			if ($_var_86['isagent'] == 1 && $_var_86['status'] == 1) {
				$_var_87 = $_var_86;
			} else {
				$_var_78 = intval($_GPC['mid']);
				if (!empty($_var_78)) {
					$_var_87 = m('member')->getMember($_var_78);
				}
			}
			$_var_89 = md5(json_encode(array('openid' => $_var_20, 'signimg' => $_var_85['signimg'], 'version' => 4)));
			$_var_82 = $_var_89 . '.jpg';
			if (!is_file($_var_80 . $_var_82)) {
				set_time_limit(0);
				@ini_set('memory_limit', '256M');
				$_var_90 = IA_ROOT . '/addons/sz_yi/static/fonts/msyh.ttf';
				$_var_91 = imagecreatetruecolor(640, 1225);
				$_var_102 = imagecolorallocate($_var_91, 0, 3, 51);
				$_var_103 = imagecolorallocate($_var_91, 240, 102, 0);
				$_var_104 = imagecolorallocate($_var_91, 255, 255, 255);
				$_var_105 = imagecolorallocate($_var_91, 255, 255, 0);
				if(!is_weixin()){
					$_var_92 = imagecreatefromjpeg(IA_ROOT . '/addons/sz_yi/plugin/commission/images/poster_pc.jpg');
					$location_num = 196;
				}else{
					$_var_92 = imagecreatefromjpeg(IA_ROOT . '/addons/sz_yi/plugin/commission/images/poster.jpg');
					$location_num = 50;
				}
				$imgusername = $_var_87['realname'] ? $_var_87['realname'] : $_var_87['nickname'];
				$imgusername = $imgusername ? $imgusername : $_var_87['mobile'];
				imagecopy($_var_91, $_var_92, 0, 0, 0, 0, 640, 1225);
				imagedestroy($_var_92);
				$_var_93 = preg_replace('/\\/0$/i', '/96', $_var_87['avatar']);
				$_var_94 = $this->createImage($_var_93);
				$_var_95 = imagesx($_var_94);
				$_var_96 = imagesy($_var_94);
				imagecopyresized($_var_91, $_var_94, 24, 32, 0, 0, 88, 88, $_var_95, $_var_96);
				imagedestroy($_var_94);
				$_var_97 = $this->createImage($_var_85['signimg']);
				$_var_95 = imagesx($_var_97);
				$_var_96 = imagesy($_var_97);
				imagecopyresized($_var_91, $_var_97, 0, 160, 0, 0, 640, 640, $_var_95, $_var_96);
				imagedestroy($_var_97);
				$_var_116 = tomedia($this->createMyShopQrcode($_var_87['id']));
				$_var_101 = $this->createImage($_var_116);
				$_var_95 = imagesx($_var_101);
				$_var_96 = imagesy($_var_101);
				imagecopyresized($_var_91, $_var_101, $location_num, 835, 0, 0, 250, 250, $_var_95, $_var_96);
				imagedestroy($_var_101);
				$_var_106 = '我是';
				imagettftext($_var_91, 20, 0, 150, 70, $_var_102, $_var_90, $_var_106);
				imagettftext($_var_91, 20, 0, 210, 70, $_var_103, $_var_90, $imgusername);
				$_var_107 = '我要为';
				imagettftext($_var_91, 20, 0, 150, 105, $_var_102, $_var_90, $_var_107);
				$_var_108 = $_var_85['name'];
				imagettftext($_var_91, 20, 0, 240, 105, $_var_103, $_var_90, $_var_108);
				$_var_109 = imagettfbbox(20, 0, $_var_90, $_var_108);
				$_var_110 = $_var_109[4] - $_var_109[6];
				$_var_111 = '代言';
				imagettftext($_var_91, 20, 0, 240 + $_var_110 + 10, 105, $_var_102, $_var_90, $_var_111);
				imagejpeg($_var_91, $_var_80 . $_var_82);
				imagedestroy($_var_91);
			}
			return $_W['siteroot'] . 'addons/sz_yi/data/poster/' . $_W['uniacid'] . '/' . $_var_82;
		}

		public function checkAgent()
		{
			global $_W, $_GPC;
			$set = $this->getSet();
			if (empty($set['level'])) {
				return;
			}
			$_var_20 = m('user')->getOpenid();
			if (empty($_var_20)) {
				return;
			}
			$_var_22 = m('member')->getMember($_var_20);
			if (empty($_var_22)) {
				return;
			}
			$_var_117 = false;
			$_var_78 = intval($_GPC['mid']);
			if (!empty($_var_78)) {
				$_var_117 = m('member')->getMember($_var_78);
			}
			$_var_118 = !empty($_var_117) && $_var_117['isagent'] == 1 && $_var_117['status'] == 1;
			if ($_var_118) {
				if ($_var_117['openid'] != $_var_20) {
					$_var_119 = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_commission_clickcount') . ' where uniacid=:uniacid and openid=:openid and from_openid=:from_openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_var_20, ':from_openid' => $_var_117['openid']));
					if ($_var_119 <= 0) {
						$_var_120 = array('uniacid' => $_W['uniacid'], 'openid' => $_var_20, 'from_openid' => $_var_117['openid'], 'clicktime' => time());
						pdo_insert('sz_yi_commission_clickcount', $_var_120);
						pdo_update('sz_yi_member', array('clickcount' => $_var_117['clickcount'] + 1), array('uniacid' => $_W['uniacid'], 'id' => $_var_117['id']));
					}
				}
			}
			if ($_var_22['isagent'] == 1) {
				return;
			}
			if ($_var_121 == 0) {
				$_var_122 = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member') . ' where id<:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $_var_22['id']));
				if ($_var_122 <= 0) {
					pdo_update('sz_yi_member', array('isagent' => 1, 'status' => 1, 'agenttime' => time(), 'agentblack' => 0), array('uniacid' => $_W['uniacid'], 'id' => $_var_22['id']));
					return;
				}
			}
			$_var_24 = time();
			$_var_123 = intval($set['become_child']);
			if ($_var_118 && empty($_var_22['agentid'])) {
				if ($_var_22['id'] != $_var_117['id']) {
					if (empty($_var_123)) {
						if (empty($_var_22['fixagentid'])) {
							pdo_update('sz_yi_member', array('agentid' => $_var_117['id'], 'childtime' => $_var_24), array('uniacid' => $_W['uniacid'], 'id' => $_var_22['id']));
							$this->sendMessage($_var_117['openid'], array('nickname' => $_var_22['nickname'], 'childtime' => $_var_24), TM_COMMISSION_AGENT_NEW);
							$this->upgradeLevelByAgent($_var_117['id']);
						}
					} else {
						pdo_update('sz_yi_member', array('inviter' => $_var_117['id']), array('uniacid' => $_W['uniacid'], 'id' => $_var_22['id']));
					}
				}
			}
			$_var_124 = intval($set['become_check']);
			if (empty($set['become'])) {
				if (empty($_var_22['agentblack'])) {
					pdo_update('sz_yi_member', array('isagent' => 1, 'status' => $_var_124, 'agenttime' => $_var_124 == 1 ? $_var_24 : 0), array('uniacid' => $_W['uniacid'], 'id' => $_var_22['id']));
					if ($_var_124 == 1) {
						$this->sendMessage($_var_20, array('nickname' => $_var_22['nickname'], 'agenttime' => $_var_24), TM_COMMISSION_BECOME);
						if ($_var_118) {
							$this->upgradeLevelByAgent($_var_117['id']);
						}
					}
				}
			}
		}

		/**
		 * Author:ym
		 * Date:2016-04-07
		 * Content:修改编译变量，检查内购无问题。
		 **/
		public function checkOrderConfirm($orderid = '0')
		{
			global $_W, $_GPC;
			if (empty($orderid)) {
				return;
			}
			//Author:ym Date:2016-04-07 Content:分红提交订单处理
			$pluginbonus = p("bonus");
			if(!empty($pluginbonus)){
				$bonus_set = $pluginbonus->getSet();
				if(!empty($bonus_set['start'])){
					$pluginbonus->checkOrderConfirm($orderid);
				}
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
		}

		/**
		 * Author:ym
		 * Date:2016-04-07
		 * Content:修改编译变量
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
			//Author:ym Date:2016-04-07 Content:分红支付订单处理
			$pluginbonus = p("bonus");
			if(!empty($pluginbonus)){
				$bonus_set = $pluginbonus->getSet();
				if(!empty($bonus_set['start'])){
					$pluginbonus->checkOrderPay($orderid);
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
		}

		/**
		 * Author:ym
		 * Date:2016-04-07
		 * Content:修改编译变量
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
			//Author:ym Date:2016-04-07 Content:分红完成订单处理
			$pluginbonus = p("bonus");
			if(!empty($pluginbonus)){
				$bonus_set = $pluginbonus->getSet();
				if(!empty($bonus_set['start'])){
					$pluginbonus->checkOrderFinish($orderid);
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
		}

		function getShop($_var_132)
		{
			global $_W;
			$_var_22 = m('member')->getMember($_var_132);
			$_var_133 = pdo_fetch('select * from ' . tablename('sz_yi_commission_shop') . ' where uniacid=:uniacid and mid=:mid limit 1', array(':uniacid' => $_W['uniacid'], ':mid' => $_var_22['id']));
			$_var_134 = m('common')->getSysset(array('shop', 'share'));
			$set = $_var_134['shop'];
			$_var_135 = $_var_134['share'];
			$_var_136 = $_var_135['desc'];
			if (empty($_var_136)) {
				$_var_136 = $set['description'];
			}
			if (empty($_var_136)) {
				$_var_136 = $set['name'];
			}
			$_var_137 = $this->getSet();
			if (empty($_var_133)) {
				$_var_133 = array('name' => $_var_22['nickname'] . '的' . $_var_137['texts']['shop'], 'logo' => $_var_22['avatar'], 'desc' => $_var_136, 'img' => tomedia($set['img']),);
			} else {
				if (empty($_var_133['name'])) {
					$_var_133['name'] = $_var_22['nickname'] . '的' . $_var_137['texts']['shop'];
				}
				if (empty($_var_133['logo'])) {
					$_var_133['logo'] = tomedia($_var_22['avatar']);
				}
				if (empty($_var_133['img'])) {
					$_var_133['img'] = tomedia($set['img']);
				}
				if (empty($_var_133['desc'])) {
					$_var_133['desc'] = $_var_136;
				}
			}
			return $_var_133;
		}

		function getLevels($_var_138 = true)
		{
			global $_W;
			if ($_var_138) {
				return pdo_fetchall('select * from ' . tablename('sz_yi_commission_level') . ' where uniacid=:uniacid order by commission1 asc', array(':uniacid' => $_W['uniacid']));
			} else {
				return pdo_fetchall('select * from ' . tablename('sz_yi_commission_level') . ' where uniacid=:uniacid and (ordermoney>0 or commissionmoney>0) order by commission1 asc', array(':uniacid' => $_W['uniacid']));
			}
		}

		function getLevel($_var_20)
		{
			global $_W;
			if (empty($_var_20)) {
				return false;
			}
			$_var_22 = m('member')->getMember($_var_20);
			if (empty($_var_22['agentlevel'])) {
				return false;
			}
			$_var_8 = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $_var_22['agentlevel']));
			return $_var_8;
		}

		function upgradeLevelByOrder($_var_20)
		{
			global $_W;
			if (empty($_var_20)) {
				return false;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return false;
			}
			$_var_132 = m('member')->getMember($_var_20);
			if (empty($_var_132)) {
				return;
			}
			$pluginbonus = p("bonus");
			if(!empty($pluginbonus)){
				$bonus_set = $pluginbonus->getSet();
				if(!empty($bonus_set['start'])){
					$pluginbonus->upgradeLevelByAgent($_var_20);
				}
			}
			$_var_139 = intval($set['leveltype']);
			if ($_var_139 == 4 || $_var_139 == 5) {
				if (!empty($_var_132['agentnotupgrade'])) {
					return;
				}
				$_var_140 = $this->getLevel($_var_132['openid']);
				if (empty($_var_140['id'])) {
					$_var_140 = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
				}
				$_var_141 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.openid=:openid and o.status>=3 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_var_20));
				$_var_30 = $_var_141['ordermoney'];
				$_var_29 = $_var_141['ordercount'];
				if ($_var_139 == 4) {
					$_var_142 = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$_var_30} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
					if (empty($_var_142)) {
						return;
					}
					if (!empty($_var_140['id'])) {
						if ($_var_140['id'] == $_var_142['id']) {
							return;
						}
						if ($_var_140['ordermoney'] > $_var_142['ordermoney']) {
							return;
						}
					}
				} else if ($_var_139 == 5) {
					$_var_142 = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$_var_29} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
					if (empty($_var_142)) {
						return;
					}
					if (!empty($_var_140['id'])) {
						if ($_var_140['id'] == $_var_142['id']) {
							return;
						}
						if ($_var_140['ordercount'] > $_var_142['ordercount']) {
							return;
						}
					}
				}
				pdo_update('sz_yi_member', array('agentlevel' => $_var_142['id']), array('id' => $_var_132['id']));
				$this->sendMessage($_var_132['openid'], array('nickname' => $_var_132['nickname'], 'oldlevel' => $_var_140, 'newlevel' => $_var_142,), TM_COMMISSION_UPGRADE);
			} else if ($_var_139 >= 0 && $_var_139 <= 3) {
				$_var_76 = array();
				if (!empty($set['selfbuy'])) {
					$_var_76[] = $_var_132;
				}
				if (!empty($_var_132['agentid'])) {
					$_var_10 = m('member')->getMember($_var_132['agentid']);
					if (!empty($_var_10)) {
						$_var_76[] = $_var_10;
						if (!empty($_var_10['agentid']) && $_var_10['isagent'] == 1 && $_var_10['status'] == 1) {
							$_var_12 = m('member')->getMember($_var_10['agentid']);
							if (!empty($_var_12) && $_var_12['isagent'] == 1 && $_var_12['status'] == 1) {
								$_var_76[] = $_var_12;
								if (empty($set['selfbuy'])) {
									if (!empty($_var_12['agentid']) && $_var_12['isagent'] == 1 && $_var_12['status'] == 1) {
										$_var_14 = m('member')->getMember($_var_12['agentid']);
										if (!empty($_var_14) && $_var_14['isagent'] == 1 && $_var_14['status'] == 1) {
											$_var_76[] = $_var_14;
										}
									}
								}
							}
						}
					}
				}
				if (empty($_var_76)) {
					return;
				}
				foreach ($_var_76 as $_var_143) {
					$_var_144 = $this->getInfo($_var_143['id'], array('ordercount3', 'ordermoney3', 'order13money', 'order13'));
					if (!empty($_var_144['agentnotupgrade'])) {
						continue;
					}
					$_var_140 = $this->getLevel($_var_143['openid']);
					if (empty($_var_140['id'])) {
						$_var_140 = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
					}
					if ($_var_139 == 0) {
						$_var_30 = $_var_144['ordermoney3'];
						$_var_142 = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid and {$_var_30} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
						if (empty($_var_142)) {
							continue;
						}
						if (!empty($_var_140['id'])) {
							if ($_var_140['id'] == $_var_142['id']) {
								continue;
							}
							if ($_var_140['ordermoney'] > $_var_142['ordermoney']) {
								continue;
							}
						}
					} else if ($_var_139 == 1) {
						$_var_30 = $_var_144['order13money'];
						$_var_142 = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid and {$_var_30} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
						if (empty($_var_142)) {
							continue;
						}
						if (!empty($_var_140['id'])) {
							if ($_var_140['id'] == $_var_142['id']) {
								continue;
							}
							if ($_var_140['ordermoney'] > $_var_142['ordermoney']) {
								continue;
							}
						}
					} else if ($_var_139 == 2) {
						$_var_29 = $_var_144['ordercount3'];
						$_var_142 = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$_var_29} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
						if (empty($_var_142)) {
							continue;
						}
						if (!empty($_var_140['id'])) {
							if ($_var_140['id'] == $_var_142['id']) {
								continue;
							}
							if ($_var_140['ordercount'] > $_var_142['ordercount']) {
								continue;
							}
						}
					} else if ($_var_139 == 3) {
						$_var_29 = $_var_144['order13'];
						$_var_142 = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$_var_29} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
						if (empty($_var_142)) {
							continue;
						}
						if (!empty($_var_140['id'])) {
							if ($_var_140['id'] == $_var_142['id']) {
								continue;
							}
							if ($_var_140['ordercount'] > $_var_142['ordercount']) {
								continue;
							}
						}
					}
					pdo_update('sz_yi_member', array('agentlevel' => $_var_142['id']), array('id' => $_var_143['id']));
					$this->sendMessage($_var_143['openid'], array('nickname' => $_var_143['nickname'], 'oldlevel' => $_var_140, 'newlevel' => $_var_142,), TM_COMMISSION_UPGRADE);
				}
			}
		}

		function upgradeLevelByAgent($_var_20)
		{
			global $_W;
			if (empty($_var_20)) {
				return false;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return false;
			}
			$_var_132 = m('member')->getMember($_var_20);
			if (empty($_var_132)) {
				return;
			}
			$pluginbonus = p("bonus");
			if(!empty($pluginbonus)){
				$bonus_set = $pluginbonus->getSet();
				if(!empty($bonus_set['start'])){
					$pluginbonus->upgradeLevelByAgent($_var_20);
				}
			}
			$_var_139 = intval($set['leveltype']);
			if ($_var_139 < 6 || $_var_139 > 9) {
				return;
			}
			$_var_144 = $this->getInfo($_var_132['id'], array());
			if ($_var_139 == 6 || $_var_139 == 8) {
				$_var_76 = array($_var_132);
				if (!empty($_var_132['agentid'])) {
					$_var_10 = m('member')->getMember($_var_132['agentid']);
					if (!empty($_var_10)) {
						$_var_76[] = $_var_10;
						if (!empty($_var_10['agentid']) && $_var_10['isagent'] == 1 && $_var_10['status'] == 1) {
							$_var_12 = m('member')->getMember($_var_10['agentid']);
							if (!empty($_var_12) && $_var_12['isagent'] == 1 && $_var_12['status'] == 1) {
								$_var_76[] = $_var_12;
							}
						}
					}
				}
				if (empty($_var_76)) {
					return;
				}
				foreach ($_var_76 as $_var_143) {
					$_var_144 = $this->getInfo($_var_143['id'], array());
					if (!empty($_var_144['agentnotupgrade'])) {
						continue;
					}
					$_var_140 = $this->getLevel($_var_143['openid']);
					if (empty($_var_140['id'])) {
						$_var_140 = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
					}
					if ($_var_139 == 6) {
						$_var_145 = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $_var_132['id'], ':uniacid' => $_W['uniacid']), 'id');
						$_var_146 += count($_var_145);
						if (!empty($_var_145)) {
							$_var_147 = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where agentid in( ' . implode(',', array_keys($_var_145)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
							$_var_146 += count($_var_147);
							if (!empty($_var_147)) {
								$_var_148 = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where agentid in( ' . implode(',', array_keys($_var_147)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
								$_var_146 += count($_var_148);
							}
						}
						$_var_142 = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$_var_146} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
					} else if ($_var_139 == 8) {
						$_var_146 = $_var_144['level1'] + $_var_144['level2'] + $_var_144['level3'];
						$_var_142 = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$_var_146} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
					}
					if (empty($_var_142)) {
						continue;
					}
					if ($_var_142['id'] == $_var_140['id']) {
						continue;
					}
					if (!empty($_var_140['id'])) {
						if ($_var_140['downcount'] > $_var_142['downcount']) {
							continue;
						}
					}
					pdo_update('sz_yi_member', array('agentlevel' => $_var_142['id']), array('id' => $_var_143['id']));
					$this->sendMessage($_var_143['openid'], array('nickname' => $_var_143['nickname'], 'oldlevel' => $_var_140, 'newlevel' => $_var_142,), TM_COMMISSION_UPGRADE);
				}
			} else {
				if (!empty($_var_132['agentnotupgrade'])) {
					return;
				}
				$_var_140 = $this->getLevel($_var_132['openid']);
				if (empty($_var_140['id'])) {
					$_var_140 = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
				}
				if ($_var_139 == 7) {
					$_var_146 = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $_var_132['id'], ':uniacid' => $_W['uniacid']));
					$_var_142 = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$_var_146} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
				} else if ($_var_139 == 9) {
					$_var_146 = $_var_144['level1'];
					$_var_142 = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$_var_146} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
				}
				if (empty($_var_142)) {
					return;
				}
				if ($_var_142['id'] == $_var_140['id']) {
					return;
				}
				if (!empty($_var_140['id'])) {
					if ($_var_140['downcount'] > $_var_142['downcount']) {
						return;
					}
				}
				pdo_update('sz_yi_member', array('agentlevel' => $_var_142['id']), array('id' => $_var_132['id']));
				$this->sendMessage($_var_132['openid'], array('nickname' => $_var_132['nickname'], 'oldlevel' => $_var_140, 'newlevel' => $_var_142,), TM_COMMISSION_UPGRADE);
			}
		}

		function upgradeLevelByCommissionOK($_var_20)
		{
			global $_W;
			if (empty($_var_20)) {
				return false;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return false;
			}
			$_var_132 = m('member')->getMember($_var_20);
			if (empty($_var_132)) {
				return;
			}
			$pluginbonus = p("bonus");
			if(!empty($pluginbonus)){
				$bonus_set = $pluginbonus->getSet();
				if(!empty($bonus_set['start'])){
					$pluginbonus->upgradeLevelByAgent($_var_20);
				}
			}
			$_var_139 = intval($set['leveltype']);
			if ($_var_139 != 10) {
				return;
			}
			if (!empty($_var_132['agentnotupgrade'])) {
				return;
			}
			$_var_140 = $this->getLevel($_var_132['openid']);
			if (empty($_var_140['id'])) {
				$_var_140 = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
			}
			$_var_144 = $this->getInfo($_var_132['id'], array('pay'));
			$_var_149 = $_var_144['commission_pay'];
			$_var_142 = pdo_fetch('select * from ' . tablename('sz_yi_commission_level') . " where uniacid=:uniacid  and {$_var_149} >= commissionmoney and commissionmoney>0  order by commissionmoney desc limit 1", array(':uniacid' => $_W['uniacid']));
			if (empty($_var_142)) {
				return;
			}
			if ($_var_140['id'] == $_var_142['id']) {
				return;
			}
			if (!empty($_var_140['id'])) {
				if ($_var_140['commissionmoney'] > $_var_142['commissionmoney']) {
					return;
				}
			}
			pdo_update('sz_yi_member', array('agentlevel' => $_var_142['id']), array('id' => $_var_132['id']));
			$this->sendMessage($_var_132['openid'], array('nickname' => $_var_132['nickname'], 'oldlevel' => $_var_140, 'newlevel' => $_var_142,), TM_COMMISSION_UPGRADE);
		}

		function sendMessage($_var_20 = '', $_var_150 = array(), $_var_151 = '')
		{
			global $_W, $_GPC;
			$set = $this->getSet();
			$_var_152 = $set['tm'];
			$_var_153 = $_var_152['templateid'];
			$_var_22 = m('member')->getMember($_var_20);
			$_var_154 = unserialize($_var_22['noticeset']);
			if (!is_array($_var_154)) {
				$_var_154 = array();
			}
			if ($_var_151 == TM_COMMISSION_AGENT_NEW && !empty($_var_152['commission_agent_new']) && empty($_var_154['commission_agent_new'])) {
				$_var_155 = $_var_152['commission_agent_new'];
				$_var_155 = str_replace('[昵称]', $_var_150['nickname'], $_var_155);
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', $_var_150['childtime']), $_var_155);
				$_var_156 = array('keyword1' => array('value' => !empty($_var_152['commission_agent_newtitle']) ? $_var_152['commission_agent_newtitle'] : '新增下线通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
				if (!empty($_var_153)) {
					m('message')->sendTplNotice($_var_20, $_var_153, $_var_156);
				} else {
					m('message')->sendCustomNotice($_var_20, $_var_156);
				}
			} else if ($_var_151 == TM_COMMISSION_ORDER_PAY && !empty($_var_152['commission_order_pay']) && empty($_var_154['commission_order_pay'])) {
				$_var_155 = $_var_152['commission_order_pay'];
				$_var_155 = str_replace('[昵称]', $_var_150['nickname'], $_var_155);
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', $_var_150['paytime']), $_var_155);
				$_var_155 = str_replace('[订单编号]', $_var_150['ordersn'], $_var_155);
				$_var_155 = str_replace('[订单金额]', $_var_150['price'], $_var_155);
				$_var_155 = str_replace('[佣金金额]', $_var_150['commission'], $_var_155);
				$_var_155 = str_replace('[商品详情]', $_var_150['goods'], $_var_155);
				$_var_156 = array('keyword1' => array('value' => !empty($_var_152['commission_order_paytitle']) ? $_var_152['commission_order_paytitle'] : '下线付款通知'), 'keyword2' => array('value' => $_var_155));
				if (!empty($_var_153)) {
					m('message')->sendTplNotice($_var_20, $_var_153, $_var_156);
				} else {
					m('message')->sendCustomNotice($_var_20, $_var_156);
				}
			} else if ($_var_151 == TM_COMMISSION_ORDER_FINISH && !empty($_var_152['commission_order_finish']) && empty($_var_154['commission_order_finish'])) {
				$_var_155 = $_var_152['commission_order_finish'];
				$_var_155 = str_replace('[昵称]', $_var_150['nickname'], $_var_155);
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', $_var_150['finishtime']), $_var_155);
				$_var_155 = str_replace('[订单编号]', $_var_150['ordersn'], $_var_155);
				$_var_155 = str_replace('[订单金额]', $_var_150['price'], $_var_155);
				$_var_155 = str_replace('[佣金金额]', $_var_150['commission'], $_var_155);
				$_var_155 = str_replace('[商品详情]', $_var_150['goods'], $_var_155);
				$_var_156 = array('keyword1' => array('value' => !empty($_var_152['commission_order_finishtitle']) ? $_var_152['commission_order_finishtitle'] : '下线确认收货通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
				if (!empty($_var_153)) {
					m('message')->sendTplNotice($_var_20, $_var_153, $_var_156);
				} else {
					m('message')->sendCustomNotice($_var_20, $_var_156);
				}
			} else if ($_var_151 == TM_COMMISSION_APPLY && !empty($_var_152['commission_apply']) && empty($_var_154['commission_apply'])) {
				$_var_155 = $_var_152['commission_apply'];
				$_var_155 = str_replace('[昵称]', $_var_22['nickname'], $_var_155);
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $_var_155);
				$_var_155 = str_replace('[金额]', $_var_150['commission'], $_var_155);
				$_var_155 = str_replace('[提现方式]', $_var_150['type'], $_var_155);
				$_var_156 = array('keyword1' => array('value' => !empty($_var_152['commission_applytitle']) ? $_var_152['commission_applytitle'] : '提现申请提交成功', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
				if (!empty($_var_153)) {
					m('message')->sendTplNotice($_var_20, $_var_153, $_var_156);
				} else {
					m('message')->sendCustomNotice($_var_20, $_var_156);
				}
			} else if ($_var_151 == TM_COMMISSION_CHECK && !empty($_var_152['commission_check']) && empty($_var_154['commission_check'])) {
				$_var_155 = $_var_152['commission_check'];
				$_var_155 = str_replace('[昵称]', $_var_22['nickname'], $_var_155);
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $_var_155);
				$_var_155 = str_replace('[金额]', $_var_150['commission'], $_var_155);
				$_var_155 = str_replace('[提现方式]', $_var_150['type'], $_var_155);
				$_var_156 = array('keyword1' => array('value' => !empty($_var_152['commission_checktitle']) ? $_var_152['commission_checktitle'] : '提现申请审核处理完成', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
				if (!empty($_var_153)) {
					m('message')->sendTplNotice($_var_20, $_var_153, $_var_156);
				} else {
					m('message')->sendCustomNotice($_var_20, $_var_156);
				}
			} else if ($_var_151 == TM_COMMISSION_PAY && !empty($_var_152['commission_pay']) && empty($_var_154['commission_pay'])) {
				$_var_155 = $_var_152['commission_pay'];
				$_var_155 = str_replace('[昵称]', $_var_22['nickname'], $_var_155);
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $_var_155);
				$_var_155 = str_replace('[金额]', $_var_150['commission'], $_var_155);
				$_var_155 = str_replace('[提现方式]', $_var_150['type'], $_var_155);
				$_var_155 = str_replace('[微信比例]', $set['withdraw_wechat'], $_var_155);
				$_var_155 = str_replace('[商城余额比例]', $set['withdraw_balance'], $_var_155);
				$_var_155 = str_replace('[税费和服务费比例]', $set['withdraw_factorage'], $_var_155);
				
				$_var_156 = array('keyword1' => array('value' => !empty($_var_152['commission_paytitle']) ? $_var_152['commission_paytitle'] : '佣金打款通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
				if (!empty($_var_153)) {
					m('message')->sendTplNotice($_var_20, $_var_153, $_var_156);
				} else {
					m('message')->sendCustomNotice($_var_20, $_var_156);
				}
			} else if ($_var_151 == TM_COMMISSION_UPGRADE && !empty($_var_152['commission_upgrade']) && empty($_var_154['commission_upgrade'])) {
				$_var_155 = $_var_152['commission_upgrade'];
				$_var_155 = str_replace('[昵称]', $_var_22['nickname'], $_var_155);
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $_var_155);
				$_var_155 = str_replace('[旧等级]', $_var_150['oldlevel']['levelname'], $_var_155);
				$_var_155 = str_replace('[旧一级分销比例]', $_var_150['oldlevel']['commission1'] . '%', $_var_155);
				$_var_155 = str_replace('[旧二级分销比例]', $_var_150['oldlevel']['commission2'] . '%', $_var_155);
				$_var_155 = str_replace('[旧三级分销比例]', $_var_150['oldlevel']['commission3'] . '%', $_var_155);
				$_var_155 = str_replace('[新等级]', $_var_150['newlevel']['levelname'], $_var_155);
				$_var_155 = str_replace('[新一级分销比例]', $_var_150['newlevel']['commission1'] . '%', $_var_155);
				$_var_155 = str_replace('[新二级分销比例]', $_var_150['newlevel']['commission2'] . '%', $_var_155);
				$_var_155 = str_replace('[新三级分销比例]', $_var_150['newlevel']['commission3'] . '%', $_var_155);
				$_var_156 = array('keyword1' => array('value' => !empty($_var_152['commission_upgradetitle']) ? $_var_152['commission_upgradetitle'] : '分销等级升级通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
				if (!empty($_var_153)) {
					m('message')->sendTplNotice($_var_20, $_var_153, $_var_156);
				} else {
					m('message')->sendCustomNotice($_var_20, $_var_156);
				}
			} else if ($_var_151 == TM_COMMISSION_BECOME && !empty($_var_152['commission_become']) && empty($_var_154['commission_become'])) {
				/*m('member')->setCredit($_var_20, $credittype = 'credit1', $set['own_integral'], $log = array());*/
				$_var_155 = $_var_152['commission_become'];
				$_var_155 = str_replace('[昵称]', $_var_150['nickname'], $_var_155);
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', $_var_150['agenttime']), $_var_155);
				/*$_var_155 = str_replace('[积分]', $set['own_integral'], $_var_155);*/
				$_var_156 = array('keyword1' => array('value' => !empty($_var_152['commission_becometitle']) ? $_var_152['commission_becometitle'] : '成为分销商通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
				if (!empty($_var_153)) {
					m('message')->sendTplNotice($_var_20, $_var_153, $_var_156);
				} else {
					m('message')->sendCustomNotice($_var_20, $_var_156);
				}
				/*if (!empty($_var_22['agentid'])) {
					$agent_info = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid={$_W['uniacid']} and id={$_var_22['agentid']}");
					m('member')->setCredit($agent_info['openid'], $credittype = 'credit1', $set['up_integral'], $log = array());
					$_var_155 = "[" . $_var_22['realname'] . "]成为分销商，您获得推广奖励：" . $set['up_integral'] . "积分";
					$_var_156 = array('keyword1' => array('value' => '推广奖励通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
					m('message')->sendCustomNotice($agent_info['openid'], $_var_156);
				}*/
			}
		}

		function perms()
		{
			return array('commission' => array('text' => $this->getName(), 'isplugin' => true, 'child' => array('cover' => array('text' => '入口设置'), 'agent' => array('text' => '分销商', 'view' => '浏览', 'check' => '审核-log', 'edit' => '修改-log', 'agentblack' => '黑名单操作-log', 'delete' => '删除-log', 'user' => '查看下线', 'order' => '查看推广订单(还需有订单权限)', 'changeagent' => '设置分销商'), 'level' => array('text' => '分销商等级', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'apply' => array('text' => '佣金审核', 'view1' => '浏览待审核', 'view2' => '浏览已审核', 'view3' => '浏览已打款', 'view_1' => '浏览无效', 'export1' => '导出待审核-log', 'export2' => '导出已审核-log', 'export3' => '导出已打款-log', 'export_1' => '导出无效-log', 'check' => '审核-log', 'pay' => '打款-log', 'cancel' => '重新审核-log'), 'notice' => array('text' => '通知设置-log'), 'increase' => array('text' => '分销商趋势图'), 'changecommission' => array('text' => '修改佣金-log'), 'set' => array('text' => '基础设置-log'))));
		}

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