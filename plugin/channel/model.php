<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
/**
* channel插件方法类
*
* 
* @package   渠道商插件公共方法
* @author    Yangyang<yangyang@yunzshop.com>
* @version   v1.0
*/
if (!class_exists('ChannelModel')) {
	class ChannelModel extends PluginModel
	{
		/**
		  * 获取渠道商基础设置
		  *
      	  * @return array $set
		  */
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}
		/**
		  * 获取我的指定商品库存 
		  *
		  * @param string $openid, int $goodsid 商品id int $optionid 规格id
		  * @return int $stock
		  */
		public function getMyOptionStock($openid, $goodsid, $optionid)
		{
			global $_W;
			$cond = '';
			if (!empty($optionid)) {
				$cond = " AND optionid={$optionid}";
			}
			$stock = pdo_fetchcolumn("SELECT stock_total FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid={$_W['uniacid']} AND openid='{$openid}' AND goodsid={$goodsid}" . $cond);
			return $stock;
		}
		/**
		  * 获取我的指定商品库存明细 
		  *
		  * @param string $openid, int $goodsid 商品id int $optionid 规格id
		  * @return array $stock_log
		  */
		public function getMyOptionStockLog($openid, $goodsid, $optionid)
		{
			global $_W;
			$cond = '';
			if (!empty($optionid)) {
				$cond = " AND sl.optionid={$optionid}";
			}
			$stock_log = pdo_fetchall("SELECT g.thumb,g.title,sl.*,m.nickname,m.avatar FROM " . tablename('sz_yi_channel_stock_log') . "sl left join  " . tablename('sz_yi_member') . " m ON sl.mid = m.id LEFT JOIN " . tablename('sz_yi_goods') . " g on sl.goodsid = g.id WHERE sl.uniacid={$_W['uniacid']} AND sl.openid='{$openid}' AND sl.goodsid={$goodsid}" . $cond);
			return $stock_log;
		}
		/**
		  * 获取上级渠道商库存最多的 
		  *
		  * @param string $openid, int $goodsid 商品id int $optionid 规格id
		  * @return array $my_superior
		  */
		public function getSuperiorStock($openid, $goodsid, $optionid)
		{
			global $_W;
			$my_agent_openids = $this->getAgentOpenids($openid);
			if (!empty($my_agent_openids)) {
				$stock = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid={$_W['uniacid']} AND openid in ({$my_agent_openids}) AND goodsid={$goodsid} AND optionid={$optionid}  ORDER BY stock_total DESC");
				return $stock;
			}
		}
		/**
		  * 获取所有上级的openid 
		  *
		  * @param string $openid
		  * @return array $my_agent_openids
		  */
		public function getAgentOpenids($openid)
		{
			global $_W;
			$my_agent_openids = array();
			$member = m('member')->getInfo($openid);
			if (empty($member['agentid'])) {
				return $my_agent_openids;
			}
			$agent = m('member')->getMember($member['agentid']);
			$my_agent_openids[] = "'".$agent['openid']."'";
			if (empty($agent['agentid'])) {
				$my_agent_openids = implode(',', $my_agent_openids);
				return $my_agent_openids;
			} else {
				$this->getAgentOpenids($agent['openid']);
			}
		}
		/**
		  * 获取自己和上级的渠道商详细信息 
		  *
		  * @param string $openid, int $goodsid='', int $optionid='', int $total=''
		  * @return array $channel_info
		  */
		public function getInfo($openid, $goodsid='', $optionid='', $total='')
		{
			global $_W;
            $set = $this->getSet();
			$channel_info = array();
			$member = m('member')->getInfo($openid);
			if (empty($member)) {
				return;
			}
            $ischannelmerchant = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_channel_merchant_order') . " WHERE uniacid=:uniacid AND openid=:openid", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
            $lower_order_money = 0;
            $lower_order_ids = array();
            if (!empty($ischannelmerchant)) {
                $lower_order_money = pdo_fetchcolumn("SELECT sum(cmo.money) FROM " . tablename('sz_yi_channel_merchant_order') . " cmo LEFT JOIN " . tablename('sz_yi_order') . " o ON o.id=cmo.orderid WHERE cmo.uniacid=:uniacid AND cmo.openid=:openid AND o.status=:status AND o.iscmas=0", array(
                    ':uniacid'  => $_W['uniacid'],
                    ':openid'   => $openid,
                    ':status'   => 3
                ));
                $lower_order = pdo_fetchall("SELECT cmo.orderid FROM " . tablename('sz_yi_channel_merchant_order') . " cmo LEFT JOIN " . tablename('sz_yi_order') . " o ON o.id=cmo.orderid WHERE cmo.uniacid=:uniacid AND cmo.openid=:openid AND o.status>=:status", array(
                    ':uniacid'  => $_W['uniacid'],
                    ':openid'   => $openid,
                    ':status'   => 1
                ));
                foreach ($lower_order AS $lo) {
                    $lower_order_ids[] = $lo['orderid'];
                }
                if (empty($lower_order_ids)) {
                    $lo_ids = 0;
                } else {
                    $lo_ids = implode(',', $lower_order_ids);
                }
            }
            $channel_info['channel']['iscmas'] = $ischannelmerchant;
            $channel_info['channel']['cma_orders'] = $lower_order_ids;
			$channel_info['channel']['lower_order_ids'] = $lo_ids;
			$channel_info['channel']['lower_order_money'] = $lower_order_money;

			$channel_info['channel']['dispatchprice'] = pdo_fetchcolumn("SELECT ifnull(sum(dispatchprice),0) FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid AND status>=3 AND iscmas=0 AND ischannelself=1 AND openid=:openid", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));

			$channel_info['channel']['order_total_price'] = number_format(pdo_fetchcolumn("SELECT ifnull(sum(price),0) FROM " . tablename('sz_yi_order_goods') . " WHERE uniacid=:uniacid AND channel_id=:channel_id",array(':uniacid' => $_W['uniacid'], ':channel_id' => $member['id'])),2);

            $channel_info['channel']['ordercount'] = pdo_fetchcolumn("SELECT count(o.id) FROM " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_order') . " o on (o.id=og.orderid) WHERE og.channel_id=:channel_id AND o.userdeleted=0 AND o.deleted=0 AND o.uniacid=:uniacid ", array(':channel_id' => $member['id'], ':uniacid' => $_W['uniacid']));

            $channel_info['channel']['commission_total'] = number_format(pdo_fetchcolumn("SELECT sum(apply_money) FROM " . tablename('sz_yi_channel_apply') . " WHERE uniacid=:uniacid AND openid=:openid", array(':uniacid' => $_W['uniacid'], ':openid' => $openid)), 2);
            $channel_info['channel']['commission_pay_total'] = number_format(pdo_fetchcolumn("SELECT sum(apply_money) FROM " . tablename('sz_yi_channel_apply') . " WHERE uniacid=:uniacid AND openid=:openid AND status = 3", array(':uniacid' => $_W['uniacid'], ':openid' => $openid)), 2);
            //零售金额
            $retail_commission = pdo_fetchcolumn("SELECT ifnull(sum(ogp.profit),0) FROM " . tablename('sz_yi_channel_order_goods_profit') . " ogp LEFT JOIN " . tablename('sz_yi_order_goods') . " og ON og.id=ogp.order_goods_id LEFT JOIN " .tablename('sz_yi_order') . " o ON (o.id=og.orderid) WHERE o.uniacid=:uniacid AND og.channel_id=:channel_id AND o.status=3 AND og.channel_apply_status=0 AND og.ischannelpay=0 ", array(':uniacid' => $_W['uniacid'], ':channel_id' => $member['id']));
            //下级采购金额
            $purchase_commission = pdo_fetchcolumn("SELECT sum(o.goodsprice) FROM " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_order') . " o on (o.id=og.orderid) WHERE o.uniacid=:uniacid AND og.channel_id=:channel_id AND o.status=3 AND og.channel_apply_status=0 AND og.ischannelpay=1 ", array(':uniacid' => $_W['uniacid'], ':channel_id' => $member['id']));
            
            $channel_info['channel']['commission_ok'] = $retail_commission + $purchase_commission;

            $channel_info['channel']['mychannels'] = pdo_fetchall("SELECT * FROM " .tablename('sz_yi_member') . " WHERE uniacid=:uniacid AND channel_level<>0 AND agentid=:agentid", array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));

            $level = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid=:uniacid AND id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $member['channel_level']));
            if (!empty($level)) {
            	if (!empty($goodsid)) {
            		$up_level = $this->getUpChannel($openid, $goodsid, $optionid='', $total);
            		if (!empty($optionid)) {
            			$up_level = $this->getUpChannel($openid, $goodsid, $optionid, $total);
            		}
            	} else {
            		$up_level = $this->getUpChannel($openid);
            	}
            	$channel_info['my_level'] = $level;
            	$channel_info['up_level'] = $up_level;
            	return $channel_info;
            } else {
            	$channel_info['my_level'] = array();
            	$up_level = $this->getUpChannel($openid, $goodsid, $optionid, $total);
            	$channel_info['up_level'] = $up_level;
            	return $channel_info;
            }
		}
		/**
		  * 获取渠道商等级权重与库存条件满足的上级openid
		  *
		  * @param string $openid, int $goodsid='', int $optionid='', int $total=''
		  * @return array $up_level
		  */
		public function getUpChannel($openid, $goodsid='', $optionid='', $total='')
		{
			global $_W;
			if (empty($total)) {
				$total = 0;
			}
			$member = m('member')->getInfo($openid);
			if (empty($member['channel_level'])) {
				$member['level_num'] = -1;
			} else {
				$member['level_num'] = pdo_fetchcolumn("SELECT level_num FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid=:uniacid AND id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $member['channel_level']));
			}
			if (empty($member['agentid'])) {
				return;
			}
			$up_channel = pdo_fetch("SELECT * FROM " . tablename('sz_yi_member') . " WHERE uniacid=:uniacid AND id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $member['agentid']));
			if (!empty($up_channel['channel_level'])) {
				$up_level = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid=:uniacid AND id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $up_channel['channel_level']));
				if (!empty($goodsid)) {
					$condtion = " AND goodsid={$goodsid}";
					if (!empty($optionid)) {
						$condtion = " AND goodsid={$goodsid} AND optionid={$optionid} AND stock_total>={$total}";
					}
					$up_stock = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid=:uniacid AND openid=:openid {$condtion} AND stock_total>=:stock_total", array(':uniacid' => $_W['uniacid'], ':openid' => $up_channel['openid'], ':stock_total' => $total));
				} else {
					$up_stock = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid=:uniacid AND openid=:openid AND stock_total>0", array(':uniacid' => $_W['uniacid'], ':openid' => $up_channel['openid']));
				}
				if ($up_level['level_num'] > $member['level_num'] && !empty($up_stock)) {
					$up_level['openid'] = $up_channel['openid'];
					$up_level['stock']	= $up_stock;
					return $up_level;
				} else {
					$this->getUpChannel($up_channel['openid']);
				}
			}
		}
		/**
		  * 获取等级权重小于等于自己的上级渠道商,如果开启推荐员,把两个人的openid与推荐员利润比例存入sz_yi_channel_merchant
		  *
		  * @param string $openid 用户openid
		  */
		/*public function getChannelNum($openid)
		{
			global $_W;
			$set = $this->getSet();
			$member = m('member')->getInfo($openid);
			$my_channel_level = $this->getLevel($openid);
			if (empty($member['agentid'])) {
				return;
			}
			$up_channel = pdo_fetch("SELECT * FROM " . tablename('sz_yi_member') . " WHERE uniacid={$_W['uniacid']} AND id={$member['agentid']}");
			if (empty($up_channel['channel_level'])) {
				return;
			}
			$up_channel_level = $this->getLevel($up_channel['openid']);
			if ($my_channel_level['level_num'] >= $up_channel_level['level_num']) {
				pdo_insert('sz_yi_channel_merchant', array(
					'uniacid'		=> $_W['uniacid'],
					'openid'		=> $up_channel['openid'],
					'lower_openid'	=> $openid,
					'commission'	=> $set['setprofitproportion']
					));
			} else {
				$this->getChannelNum($up_channel['openid']);
			}
		}*/
		/**
		  * 获取用户的渠道商等级详情
		  *
		  * @param string $openid 用户openid
		  * @return array $level
		  */
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
			$level = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_channel_level') . ' WHERE uniacid=:uniacid AND id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $member['channel_level']));
			return $level;
		}
		/**
		  * 渠道商根据直属下线升级
		  *
		  * @param string $openid 用户openid
		  */
		public function upgradeLevelByAgent($openid)
		{
			global $_W;
			if (empty($openid)) {
				return false;
			}
			$set = $this->getSet();
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			$my_agents = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('sz_yi_member') . " WHERE uniacid={$_W['uniacid']} AND agentid={$member['id']} AND ischannel=1 AND channel_level>0");
			if ($set['become'] == 1) {
				$my_level = $this->getLevel($openid);
				$up_level_num = $my_level['level_num'] + 1;
				$channel_level = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid={$_W['uniacid']} AND level_num={$up_level_num}");
				if ($my_agents >= $channel_level['team_count']) {
					pdo_update('sz_yi_member', array('channel_level' => $channel_level['id']), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
				}
			}
		}
		/**
		  * 渠道商自提扣除自己库存
		  *
		  * @param int $orderid 订单的id
		  */
		public function deductChannelStock($orderid)
		{
			global $_W;
			$openid = pdo_fetchcolumn("SELECT openid FROM " . tablename('sz_yi_order') . " WHERE uniacid={$_W['uniacid']} AND id={$orderid}");
			$my_info = $this->getInfo($openid);
            $order_goods = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_order_goods') . " WHERE uniacid={$_W['uniacid']} AND orderid={$orderid}");
            foreach ($order_goods as $og) {
                $channel_cond = " WHERE uniacid={$_W['uniacid']} AND goodsid={$og['goodsid']} AND openid='{$openid}'";
                if (!empty($og['optionid'])) {
                    $channel_cond .= " AND optionid={$og['optionid']}";
                }
                $channel_stock = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_stock') . $channel_cond);
                $data = array(
                    'uniacid'   => $_W['uniacid'],
                    'openid'    => $openid,
                    'goodsid'   => $og['goodsid']
                    );
                if (empty($my_info['up_channel'])) {
                	$mid = 0;
                } else {
                	$up_mem = m('member')->getInfo($my_info['up_channel']['openid']);
                	$mid = $up_mem['id'];
                }
                $log_data = array(
                	'openid'		=> $openid,
                    'goodsid'       => $og['goodsid'],
                    'order_goodsid' => $og['id'],
                    'every_turn'	=> $og['total'],
                    'uniacid'       => $_W['uniacid'],
                    'type'          => 4,
                    'paytime'		=> time(),
                    'mid'			=> $mid
                    );
                if (!empty($channel_stock)) {
                    $stock_total = $channel_stock['stock_total'] - $og['total'];
                    if (!empty($og['optionid'])) {
                        $data['optionid']       = $og['optionid'];
                        $log_data['optionid']   = $og['optionid'];
                        
                    }
                    $log_data['surplus_stock']	= $stock_total;
                    pdo_update('sz_yi_channel_stock', array('stock_total' => $stock_total), $data);
                    pdo_insert('sz_yi_channel_stock_log', $log_data);
                }
            }
		}
		/**
		  * 根据进货金额或进货次数升级
		  *
		  * @param int $orderid 订单的id
		  */
		public function checkOrderFinishOrPay($orderid = '')
		{
			global $_W, $_GPC;
			if (empty($orderid)) {
				return;
			}
			$set = $this->getSet();
			if(empty($set['become'])){
				return;
			}
			$order = pdo_fetch('SELECT id,openid,ordersn,goodsprice,agentid,paytime,finishtime FROM ' . tablename('sz_yi_order') . ' WHERE id=:id AND status>=1 AND uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($order)) {
				return;
			}
			$openid = $order['openid'];
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			$order_goods = pdo_fetchall('select g.id,g.title,og.total,og.price,og.realprice, og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1,og.channel_id,og.ischannelpay from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
			foreach ($order_goods as $og) {
				$goods = '';
				$pricetotal = 0;
				$goods .= "" . $og['title'] . '( ';
                if (!empty($og['optiontitle'])) {
                    $goods .= " 规格: " . $og['optiontitle'];
                }
                @$goods .= ' 单价: ' . ($og['realprice'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['realprice'] . "); ";
				$pricetotal += $og['realprice'];
				$level = $this->getLevel($openid);
				$message = array(
					'nickname' 		=> $member['nickname'],
                    'ordersn' 		=> $order['ordersn'],
                    'price' 		=> $pricetotal,
                    'goods' 		=> $goods,
                    'level_name'	=> $level['level_name']
					);
				if (!empty($og['ischannelpay'])) {
					if (!empty($og['channel_id'])) {
						$up_openid = pdo_fetchcolumn("SELECT openid FROM " . tablename('sz_yi_member') . " WHERE uniacid={$_W['uniacid']} AND id={$og['channel_id']}");
						$this->sendMessage($up_openid, $message, TM_LOWERCHANNEL_ORDER);
					} else {
						$this->sendMessage($openid, $message, TM_CHANNELPURCHASE_ORDER);
					}
				} else if (!empty($og['channel_id'])) {
					$up_member = pdo_fetch("SELECT openid,nickname FROM " . tablename('sz_yi_member') . " WHERE uniacid={$_W['uniacid']} AND id={$og['channel_id']}");
					$message['up_nickname'] = $up_member['nickname'];
					$this->sendMessage($up_member['openid'], $message, TM_CHANNELRETAIL_ORDER);
				}
			}
		}
		/**
		  * 渠道商通知
		  *
		  * @param string $openid array $data 相关数据 string $message_type 通知类型
		  */
		function sendMessage($openid = '', $data = array(), $message_type = '')
		{
			global $_W, $_GPC;
			$set = $this->getSet();
			$member = m('member')->getInfo($openid);
			$tm = $set['tm'];
			if ($message_type == TM_CHANNEL_APPLY && !empty($tm['channel_apply'])) {
				$message = $tm['channel_apply'];
				$message = str_replace('[昵称]', $member['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[金额]', $data['commission'], $message);
				$message = str_replace('[提现方式]', $data['type'], $message);
				$msg = array('keyword1' => array('value' => !empty($tm['channel_applytitle']) ? $tm['channel_applytitle'] : '渠道商提现申请提交通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
			} else if ($message_type == TM_CHANNEL_APPLY_FINISH && !empty($tm['channel_check'])) {
				$message = $tm['channel_check'];
				$message = str_replace('[昵称]', $member['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[金额]', $data['commission'], $message);
				$message = str_replace('[提现方式]', $data['type'], $message);
				$msg = array('keyword1' => array('value' => !empty($tm['channel_checktitle']) ? $tm['channel_checktitle'] : '渠道商提现申请审核完成通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
			} else if ($message_type == TM_CHANNEL_BECOME && !empty($tm['channel_become'])) {
				$message = $tm['channel_become'];
				$message = str_replace('[昵称]', $member['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$msg = array('keyword1' => array('value' => !empty($tm['channel_becometitle']) ? $tm['channel_becometitle'] : '成为渠道商通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
			} else if ($message_type == TM_CHANNEL_UPGRADE && !empty($tm['channel_upgrade'])) {
				$message = $tm['channel_upgrade'];
				$message = str_replace('[昵称]', $member['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[旧等级]', $data['oldlevelname'], $message);
				$message = str_replace('[旧等级采购折扣]', $data['old_purchase_discount'], $message);
				$message = str_replace('[新等级]', $data['newlevelname'], $message);
				$message = str_replace('[新等级采购折扣]', $data['new_purchase_discount'], $message);
				$msg = array('keyword1' => array('value' => !empty($tm['channel_upgradetitle']) ? $tm['channel_upgradetitle'] : '渠道商等级升级通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
			} else if ($message_type == TM_LOWERCHANNEL_ORDER && !empty($tm['channel_lowerpurchase'])) {
				$message = $tm['channel_lowerpurchase'];
				$message = str_replace('[昵称]', $data['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[订单号]', $data['ordersn'], $message);
				$message = str_replace('[渠道等级]', $data['level_name'], $message);
				$message = str_replace('[商品]', $data['goods'], $message);
				$msg = array('keyword1' => array('value' => !empty($tm['channel_upgradetitle']) ? $tm['channel_upgradetitle'] : '下级渠道商采购通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
			} else if ($message_type == TM_CHANNELPURCHASE_ORDER && !empty($tm['channel_purchase'])) {
				$message = $tm['channel_purchase'];
				$message = str_replace('[昵称]', $data['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[订单号]', $data['ordersn'], $message);
				$message = str_replace('[渠道等级]', $data['level_name'], $message);
				$message = str_replace('[商品]', $data['goods'], $message);
				$msg = array('keyword1' => array('value' => !empty($tm['channel_upgradetitle']) ? $tm['channel_upgradetitle'] : '渠道商采购通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
			} else if ($message_type == TM_CHANNELRETAIL_ORDER && !empty($tm['channel_retail'])) {
				$message = $tm['channel_retail'];
				$message = str_replace('[上级昵称]', $data['up_nickname'], $message);
				$message = str_replace('[昵称]', $data['nickname'], $message);
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[订单号]', $data['ordersn'], $message);
				$message = str_replace('[渠道等级]', $data['level_name'], $message);
				$message = str_replace('[商品]', $data['goods'], $message);
				$msg = array('keyword1' => array('value' => !empty($tm['channel_retailtitle']) ? $tm['channel_retailtitle'] : '渠道商零售订单通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
			}
			m('message')->sendCustomNotice($openid, $msg);
		}
		/**
		  * 团队人数达到**成为渠道商
		  *
		  * @param string $openid 用户openid
		  */
		function becomeChannelByAgent($openid){
			global $_W;
			if (empty($openid)) {
				return false;
			}
			$set = $this->getSet();
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			$my_agents = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('sz_yi_member') . " WHERE uniacid=:uniacid AND agentid=:id", array(':uniacid' => $_W['uniacid'], ':id' => $member['id']));
			if (!empty($set['default_level']) && !empty($set['become_condition_team'])) {
				if ($my_agents >= $set['become_condition_team']) {
					$up_level_id = pdo_fetchcolumn('SELECT id FROM ' . tablename('sz_yi_channel_level') . ' WHERE uniacid=:uniacid AND id = :id', array(':uniacid' => $_W['uniacid'],':id' => $set['default_level']));
					if (!$up_level_id) {
						$up_level_id = pdo_fetchcolumn('SELECT id FROM ' . tablename('sz_yi_channel_level') . ' WHERE uniacid=:uniacid ORDER BY level_num ASC LIMIT 1', array(':uniacid' => $_W['uniacid']));
					}	
					pdo_update('sz_yi_member', array('ischannel' => 1,'channel_level' => $up_level_id), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
					$this->sendMessage($openid, array('nickname' => $member['nickname']), TM_CHANNEL_BECOME);
				} else {
					return;
				}				
					
				
			}
		}
		/**
		  * 累计进货(元/次)达到**成为渠道商
		  *
		  * @param string $openid 用户openid int $orderid 订单id
		  */
		function becomeChannelByOrder($openid, $orderid){
			global $_W;
			if (empty($openid) || empty($orderid)) {
				return false;
			}
			$set = $this->getSet();
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			if ($set['become_condition_order'] == 0) {
				$condtion .= ' AND o.status >= 1 AND o.status < 3';
			} elseif ($set['become_condition_order'] == 1){
				$condtion .= ' AND o.status >= 3';
			}
			$orderinfo = pdo_fetch('SELECT sum(og.realprice) AS ordermoney,count(distinct og.orderid) AS ordercount FROM ' . tablename('sz_yi_order') . ' o ' . ' LEFT JOIN  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' WHERE o.openid=:openid ' . $condtion . ' AND o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			$up_level_num = pdo_fetchcolumn('SELECT id FROM ' . tablename('sz_yi_channel_level') . ' WHERE uniacid=:uniacid AND id = :id', array(':uniacid' => $_W['uniacid'],':id' => $set['default_level']));
			if (!$up_level_num) {
				$up_level_num = pdo_fetchcolumn('SELECT id FROM ' . tablename('sz_yi_channel_level') . ' WHERE uniacid=:uniacid ORDER BY level_num ASC LIMIT 1', array(':uniacid' => $_W['uniacid']));
			}
			if ($set['become_condition'] == 3) {
				if ($orderinfo['ordermoney'] >= $set['become_condition_money']) {
					pdo_update('sz_yi_member', array('ischannel' => 1,'channel_level' => $up_level_num), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
					$this->sendMessage($openid, array('nickname' => $member['nickname']), TM_CHANNEL_BECOME);
				}
			} elseif ($set['become_condition'] == 4){
				if ($orderinfo['ordercount'] >= $set['become_condition_count']) {
					pdo_update('sz_yi_member', array('ischannel' => 1,'channel_level' => $up_level_num), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
					$this->sendMessage($openid, array('nickname' => $member['nickname']), TM_CHANNEL_BECOME);
				}
			} else {
				return;
			}
			
		}
		/**
		  * 购买指定商品成为渠道商
		  *
		  * @param string $openid 用户openid int $orderid 订单id
		  */
		function becomeChannelByGood($openid, $orderid)
        {
            // 如果购买的商品可以指定成为分销商
            global $_W;
            $set = $this->getSet();
            if (empty($openid) || empty($orderid)) {
				return false;
			}
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			if ($set['become_condition_order'] == 0) {
				$condtion .= ' AND o.status >= 1 AND o.status < 3';
			} elseif ($set['become_condition_order'] == 1){
				$condtion .= ' AND o.status >= 3';
			}
            $goods = pdo_fetch('SELECT og.goodsid FROM ' . tablename('sz_yi_order_goods') . '  og LEFT JOIN ' . tablename('sz_yi_order') . '  o  ON og.orderid = o.id WHERE o.id=:orderid AND o.openid = :openid AND og.uniacid=:uniacid AND og.goodsid = :goodsid' . $condtion, array(
                ':orderid' => $orderid,
                ':uniacid' => $_W['uniacid'],
                ':openid' => $openid,
                ':goodsid' => $set['become_condition_goodsid']
            ));
            $up_level_num = pdo_fetchcolumn('SELECT id FROM ' . tablename('sz_yi_channel_level') . ' WHERE uniacid=:uniacid AND id = :id', array(':uniacid' => $_W['uniacid'],':id' => $set['default_level']));
			if (!$up_level_num) {
				$up_level_num = pdo_fetchcolumn('SELECT id FROM ' . tablename('sz_yi_channel_level') . ' WHERE uniacid=:uniacid ORDER BY level_num ASC LIMIT 1', array(':uniacid' => $_W['uniacid']));
			}
            if ($set['become_condition'] == 5) {
            	if ($goods) {
            		pdo_update('sz_yi_member', array('ischannel' => 1,'channel_level' => $up_level_num), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
					$this->sendMessage($openid, array('nickname' => $member['nickname']), TM_CHANNEL_BECOME); 
            	}
            }else{
            	return;
            }
                     
        }
        /**
		  * 购买指定商品升级渠道商 edit by yangyang 12-21
		  *
		  * @param string $openid 用户openid int $orderid 订单id
		  */
        function upgradelevelByGood($openid, $orderid)
        {
            global $_W;
            $set = $this->getSet();
            if (empty($openid) || empty($orderid)) {
				return false;
			}
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			$my_level = $this->getLevel($openid);
			$level_num = $my_level['level_num'] + 1;
			if ($set['become_order'] == 0) {
				$condtion .= ' AND status >= 1 AND status < 3';
			} elseif ($set['become_order'] == 1){
				$condtion .= ' AND status >= 3';
			}
            
        	$order = pdo_fetch("SELECT id FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid AND id=:id" . $condtion ,array(
        			':uniacid'	=> $_W['uniacid'],
        			':id'		=> $orderid
        		));
        	if (!empty($order)) {
        		$goodsid = pdo_fetchcolumn("SELECT goodsid FROM " . tablename('sz_yi_order_goods') . " 	WHERE uniacid=:uniacid AND orderid=:orderid", array(
        				':uniacid'	=> $_W['uniacid'],
        				':orderid'	=> $order['id']
        			));
        		$channel_level = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid=:uniacid AND goods_id=:goodsid ORDER BY level_num ASC", array(
            		':uniacid'	=> $_W['uniacid'],
            		':goodsid'	=> $goodsid
            	));
            	if (!empty($channel_level)) {
            		foreach ($channel_level as $value) {
            			if ($value['level_num'] > $my_level['level_num']) {
            				pdo_update('sz_yi_member', array('ischannel' => 1,'channel_level' => $channel_level['id']), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
							$this->sendMessage($openid, array('nickname' => $member['nickname'], 'oldlevelname' => $my_level['level_name'], 'old_purchase_discount' => $my_level['purchase_discount'], 'newlevelname' => $channel_level['level_name'], 'new_purchase_discount' => $channel_level['purchase_discount']), TM_CHANNEL_UPGRADE);
							break;
            			}
            		}
            	}
        	}     
        }
        /**
		  * 其他方式(团队人数、累计进货量、累计进货次数)升级渠道商（可多选）
		  *
		  * @param string $openid 用户openid int $orderid 订单id
		  */
        function upgradelevelByOther($openid, $orderid = '')
        {
            // 如果购买的商品可以指定成为分销商
            global $_W;
            $set = $this->getSet();
            if (empty($openid)) {
				return false;
			}
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			if ($set['become_order'] == 0) {
				$condtion .= ' AND o.status >= 1 AND o.status < 3';
			} elseif ($set['become_order'] == 1){
				$condtion .= ' AND o.status >= 3';
			}
			$my_level = $this->getLevel($openid);
			$up_level_num = $my_level['level_num'] + 1;
			$channel_level = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid={$_W['uniacid']} AND level_num={$up_level_num}");
			if (empty($channel_level)) {
				return;
			}
			$status = array();
            if ($set['become'] == 2) {//其他方式
            	if (in_array(1,$set['become_other'])) {//团队总人数
            		$my_agents = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('sz_yi_member') . " WHERE uniacid={$_W['uniacid']} AND agentid={$member['id']} AND ischannel=1 AND channel_level>0");		
					if ($my_agents >= $channel_level['team_count']) {
						$status[1]['status'] = 1;
					} else {
						$status[1]['status'] = 0;
					}
            	}
				$order = pdo_fetchall("SELECT id FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid AND openid=:openid", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
				$ordermoney = 0;
				$ordercount = 0;
				foreach ($order as $value) {
					$ordercount += 1;
					$order_goods = pdo_fetchall("SELECT realprice FROM " . tablename('sz_yi_order_goods') . " WHERE uniacid=:uniacid AND orderid=:orderid AND ischannelpay=1", array(':uniacid' => $_W['uniacid'],':orderid' => $value['id']));
					foreach ($order_goods as $v) {
						$ordermoney += $v['realprice'];
					}
				}
            	if (in_array(2,$set['become_other'])) {//累计进货量(金额)
					if ($ordermoney >= $channel_level['order_money']) {
						$status[2]['status'] = 1;
					} else {
						$status[2]['status'] = 0;
					}
            	}
            	if (in_array(3,$set['become_other'])) {//累计进货次数(次)
					if ($ordercount >= $channel_level['order_count']) {
						$status[3]['status'] = 1;
					} else {
						$status[3]['status'] = 0;
					}
            	}
            	$finish_status = 0;
            	foreach ($status as $row) {
            		if ($row['status'] == 1) {
            			$finish_status = 1;
            		}
            	}
            	if ($finish_status == 1) {
            		pdo_update('sz_yi_member', array('channel_level' => $channel_level['id']), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
					$this->sendMessage($openid, array('nickname' => $member['nickname'], 'oldlevelname' => $my_level['level_name'], 'old_purchase_discount' => $my_level['purchase_discount'], 'newlevelname' => $channel_level['level_name'], 'new_purchase_discount' => $channel_level['purchase_discount']), TM_CHANNEL_UPGRADE);
            	} else {
            		return;
            	}				
            }        
        }

        //11-19 edit by yangyang
        public function recursive_access_to_superior ($openid, $goodsid='', $optionid='', $total='')
        {
            global $_W;
            $member = m('member')->getInfo($openid);
            if ($member['agentid'] == 0) {
                return array();
            }
            if ($member['ischannel'] == 0) {
                $level_leight = -1;
            } else {
                $level = $this->getLevel($openid);
                $level_leight = $level['level_num'];
            }
            $superior = pdo_fetch("SELECT * FROM " . tablename('sz_yi_member') . " WHERE uniacid=:uniacid AND id=:id", array(
                ':uniacid'  => $_W['uniacid'],
                ':id'       => $member['agentid']
            ));
            if ($superior['ischannel'] == 0) {
                if ($superior['agentid'] == 0) {
                    return array();
                }
                return $this->recursive_access_to_superior($superior['openid'], $goodsid, $optionid, $total);
            } else {
                $superior_level = $this->getLevel($superior['openid']);
                if ($superior_level > $level_leight) {
                    $condtion = " AND goodsid={$goodsid} AND stock_total>={$total}";
                    if (!empty($optionid)) {
                        $condtion .= " AND optionid={$optionid}";
                    }
                    $superior_stock = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid=:uniacid AND openid=:openid {$condtion}", array(':uniacid' => $_W['uniacid'], ':openid' => $superior['openid']));
                    if (empty($superior_stock)) {
                        if ($superior['agentid'] == 0) {
                            return array();
                        }
                        return $this->recursive_access_to_superior($superior['openid'], $goodsid, $optionid, $total);
                    } else {
                        $superior_level['openid'] = $superior['openid'];
                        $superior_level['stock'] = $superior_stock;
                        return $superior_level;
                    }
                } else {
                    if ($superior['agentid'] == 0) {
                        return array();
                    }
                    return $this->recursive_access_to_superior($superior['openid'], $goodsid, $optionid, $total);
                }
            }
        }

        //edit by yangyang 12-02 content:渠道商推荐员,下单时触发
        public function isChannelMerchant($orderid)
        {
            global $_W;
            $set = $this->getSet();
            if ($set['closerecommenderchannel'] == 1) {
                return;
            }
            if (empty($orderid)) {
                return;
            }
            $order = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid AND id=:id", array(
                ':uniacid'  => $_W['uniacid'],
                ':id'       => $orderid
            ));
            if (empty($order)) {
                return;
            }
            $order_by_member = m('member')->getInfo($order['openid']);
            if ($order_by_member['agentid'] == 0 && $order_by_member['ischannel'] == 0) {
                return;
            }
            $superior_member = pdo_fetch("SELECT * FROM " . tablename('sz_yi_member') . " WHERE uniacid=:uniacid AND id=:id", array(
                ':uniacid'  => $_W['uniacid'],
                ':id'       => $order_by_member['agentid']
            ));
            if ($superior_member['ischannel'] == 0) {
                return;
            }
            $member_level = $this->getLevel($order_by_member['openid']);
            $superior_level = $this->getLevel($superior_member['openid']);
            if (empty($member_level) || empty($superior_level)) {
                return;
            }
            if ($member_level['level_num'] < $superior_level['level_num']) {
                return;
            }
            $money = $order['goodsprice']*$set['setprofitproportion']/100;
            $data = array(
                'uniacid'       => $_W['uniacid'],
                'openid'        => $superior_member['openid'],
                'orderid'       => $orderid,
                'commission'    => $set['setprofitproportion'],
                'money'         => $money
            );
            pdo_insert('sz_yi_channel_merchant_order', $data);
        }

        //edit by yangyang 12-14 content:渠道商采购，订单支付后加库存
        public function changeStock($orderid)
        {
        	global $_W;
        	if (empty($orderid)) {
        		return;
        	}
        	$order = pdo_fetch("SELECT * FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid AND id=:id", array(
        			':uniacid' 	=> $_W['uniacid'],
        			':id'		=> $orderid
        		));
        	if (empty($order)) {
        		return;
        	}
        	$openid = $order['openid'];
        	$order_goods = pdo_fetchall("SELECT goodsid,total,optionid,id,ischannelpay,channel_id FROM " . tablename('sz_yi_order_goods') . " WHERE uniacid=:uniacid AND orderid=:orderid", array(
        			':uniacid'	=> $_W['uniacid'],
        			':orderid'	=> $orderid
        		));
        	foreach ($order_goods as $key => $value) {
                $marketprice = pdo_fetchcolumn("SELECT marketprice FROM " . tablename('sz_yi_goods') . " WHERE uniacid=:uniacid AND id=:id", array(
                    ':uniacid'	=> $_W['uniacid'],
                    ':id'		=> $value['goodsid']
                ));
                $my_info = $this->getInfo($openid,$value['goodsid'],$goods['optionid'],$value['total']);
                $every_turn_price = $marketprice*($my_info['my_level']['purchase_discount']/100);
                if (!empty($value['ischannelpay'])) {
                    $channel_cond = '';
                    if (!empty($value['optionid'])) {
                        $channel_cond = " AND optionid={$value['optionid']}";
                    }
                    $ischannelstock  = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid=:uniacid AND openid=:openid AND goodsid=:goodsid {$channel_cond}", array(
                            ':uniacid'	=> $_W['uniacid'],
                            ':openid'	=> $openid,
                            ':goodsid'	=> $value['goodsid']
                        ));
                    if (empty($ischannelstock)) {
                        pdo_insert('sz_yi_channel_stock', array(
                            'uniacid'       => $_W['uniacid'],
                            'openid'        => $openid,
                            'goodsid'       => $value['goodsid'],
                            'optionid'      => $value['optionid'],
                            'stock_total'   => $value['total']
                        ));
                    } else {
                        $stock_total = $ischannelstock['stock_total'] + $value['total'];
                        pdo_update('sz_yi_channel_stock', array(
                            'stock_total'   => $stock_total
                        ), array(
                            'uniacid'       => $_W['uniacid'],
                            'openid'        => $openid,
                            'optionid'      => $value['optionid'],
                            'goodsid'       => $value['goodsid']
                        ));
                    }
                    $surplus_stock = pdo_fetchcolumn("SELECT stock_total FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid=:uniacid AND openid=:openid AND goodsid=:goodsid {$channel_cond}", array(
                            ':uniacid'	=> $_W['uniacid'],
                            ':openid'	=> $openid,
                            ':goodsid'	=> $value['goodsid']
                        ));
                    $member = m('member')->getInfo($openid);
                    $stock_log = array(
                        'uniacid'             => $_W['uniacid'],
                        'openid'              => $openid,
                        'goodsid'             => $value['goodsid'],
                        'optionid'            => $value['optionid'],
                        'every_turn'          => $value['total'],
                        'every_turn_price'    => $every_turn_price,
                        'every_turn_discount' => $my_info['my_level']['purchase_discount'],
                        'goods_price'         => $marketprice,
                        'paytime'             => time(),
                        'type'                => 1,
                        'surplus_stock'       => $surplus_stock,
                        'mid'                 => $member['id']
                    );
                    pdo_insert('sz_yi_channel_stock_log', $stock_log);
                }
                if (!empty($my_info['up_level']['stock'])) {
                    $up_stock = $my_info['up_level']['stock']['stock_total'] - $value['total'];
                    pdo_update('sz_yi_channel_stock', array(
                        'stock_total' => $up_stock
                    ), array(
                        'uniacid'   => $_W['uniacid'],
                        'goodsid'   => $value['goodsid'],
                        'openid'    => $my_info['up_level']['openid'],
                        'optionid'  => $value['optionid']
                    ));
                    $up_member = m('member')->getInfo($order['openid']);
                    $log_data = array(
                        'openid'		=> $my_info['up_level']['openid'],
                        'goodsid'       => $value['goodsid'],
                        'optionid'      => $value['optionid'],
                        'order_goodsid' => $value['id'],
                        'uniacid'       => $_W['uniacid'],
                        'every_turn'    => $value['total'],
                        'goods_price'   => $marketprice,
                        'surplus_stock' => $up_stock,
                        'mid'           => $up_member['id'],
                        'paytime'       => time()
                        );
                    if (!empty($value['ischannelpay'])) {
                        $log_data['every_turn_price'] = $marketprice*$my_info['my_level']['purchase_discount']/100;
                        $log_data['every_turn_discount'] = $my_info['my_level']['purchase_discount'];
                        $log_data['type'] = 2;
                    } else {
                        $log_data['every_turn_price'] = $marketprice;
                        $log_data['every_turn_discount'] = 0;
                        $log_data['type'] = 3;
                    }
                    pdo_insert('sz_yi_channel_stock_log', $log_data);
                }
        	}
        }

        //edit by yangyang 12-19 comment 退款加库存
        public function channelRefund($orderid, $uniacid, $openid)
        {
            global $_W;
            $order_goods = pdo_fetchall("SELECT channel_id,goodsid,optionid,total FROM " . tablename('sz_yi_order_goods') . " WHERE uniacid=:uniacid AND orderid=:orderid", array(
                ':uniacid'  => $uniacid,
                ':orderid'  => $orderid
            ));
            foreach ($order_goods as $og) {
                if ($og['channel_id'] > 0) {
                    $up_openid = pdo_fetchcolumn("SELECT openid FROM " . tablename('sz_yi_member') . " WHERE id=:id", array(
                        ':id'   => $og['channel_id']
                    ));
                    $stock = $this->getMyOptionStock($up_openid,$og['goodsid'],$og['optionid']);
                    $stock += $og['total'];
                    pdo_update('sz_yi_channel_stock',
                        array(
                            'stock_total' => $stock
                        ),
                        array(
                            'uniacid'   => $_W['uniacid'],
                            'openid'    => $up_openid,
                            'goodsid'   => $og['goodsid'],
                            'optionid'  => $og['optionid']
                        )
                    );
                }
            }
        }
	}
}