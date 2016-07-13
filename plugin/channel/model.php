<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

if (!class_exists('ChannelModel')) {
	class ChannelModel extends PluginModel
	{
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
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

		public function getLevel($openid)
		{
			global $_W;
			if (empty($openid)) {
				return false;
			}
			$member = m('member')->getMember($openid);
			if (empty($member['channel_level'])) {
				return false;
			}
			$level = pdo_fetch('select * from ' . tablename('sz_yi_channel_level') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $member['channel_level']));
			return $level;
		}

		public function upgradeLevelByAgent($mid)
		{
			global $_W;
			if (empty($mid)) {
				return false;
			}
			$set = $this->getSet();
			$member = m('member')->getMember($mid);
			$my_agents = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_member') . " where uniacid={$_W['uniacid']} and agentid={$mid} and status=1 and isagent=1");
			if (empty($member)) {
				return;
			}
			if ($set['become'] == 1) {
				$channel_level = pdo_fetch("select id from " . tablename('sz_yi_channel_level') . " where uniacid={$_W['uniacid']} and $my_agents<teamtotal order by teamtotal asc limit 1");
				if (!empty($channel_level) && $member['channel_level'] != $channel_level['id']) {
					pdo_update('sz_yi_member', array('channel_level' => $channel_level['id']), array('uniacid' => $_W['uniacid'], 'id' => $mid));
					//消息通知
				}
			}
		}

		public function checkOrderFinish($orderid = '')
		{
			global $_W, $_GPC;
			if (empty($orderid)) {
				return;
			}
			$set = $this->getSet();
			if(empty($set['become'])){
				return;
			}
			$order = pdo_fetch('select id,openid,ordersn,goodsprice,agentid,paytime,finishtime from ' . tablename('sz_yi_order') . ' where id=:id and status>=1 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($order)) {
				return;
			}
			$openid = $order['openid'];
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			$ordergoods = pdo_fetchall('select g.id,g.title,og.total,og.price,og.realprice, og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1 from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $order['id']));
			$goods = '';
			$realprice = 0;
			foreach ($ordergoods as $item) {
				$goods .= "" . $item['title'] . '( ';
				if (!empty($item['optiontitle'])) {
					$goods .= ' 规格: ' . $item['optiontitle'];
				}
				$goods .= ' 单价: ' . ($item['realprice'] / $item['total']) . ' 数量: ' . $item['total'] . ' 总价: ' . $item['realprice'] . '); ';
				$realprice += $item['realprice'];
			}
			$bonus_goods = pdo_fetchall('select distinct mid from ' . tablename('sz_yi_bonus_goods') . ' where uniacid=:uniacid and orderid=:orderid', array(':orderid' => $orderid, ':uniacid' => $_W['uniacid']));
			foreach ($bonus_goods as $key => $val) {
				$openid = pdo_fetchcolumn("select openid from " . tablename('sz_yi_member') . " where id=".$val['mid']." and uniacid=".$_W['uniacid']);
				//股权代理分红通知
				$agent_money = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_goods') . " where mid=".$val['mid']." and orderid=".$order['id']." and bonus_area=0 and uniacid=".$_W['uniacid']);
				if($agent_money > 0){
					$this->sendMessage($openid, array('nickname' => $member['nickname'], 'ordersn' => $order['ordersn'], 'price' => $realprice, 'goods' => $goods, 'commission' => $agent_money, 'finishtime' => $order['finishtime']), TM_BONUS_ORDER_FINISH);
				}
				//区域代理分红通知
				$agent_area_money = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_goods') . " where mid=".$val['mid']." and orderid=".$order['id']." and bonus_area > 0 and uniacid=".$_W['uniacid']);
				if($agent_area_money > 0){
					$this->sendMessage($openid, array('nickname' => $member['nickname'], 'ordersn' => $order['ordersn'], 'price' => $realprice, 'goods' => $goods, 'commission' => $agent_area_money, 'finishtime' => $order['finishtime']), TM_BONUS_ORDER_AREA_FINISH);
				}
				$this->upgradeLevelByAgent($openid);
			}
		}

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
	}
}