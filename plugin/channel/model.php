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
		  * 获取自己和上级的渠道商详细信息 
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
		  * 获取所有上级的id 
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
			$channel_info = array();
			$member = m('member')->getInfo($openid);
			if (empty($member)) {
				return;
			}
            $channel_info['channel']['ordercount'] = pdo_fetchcolumn("SELECT count(o.id) FROM " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_order') . " o on (o.id=og.orderid) WHERE og.channel_id={$member['id']} AND o.userdeleted=0 AND o.deleted=0 AND o.uniacid={$_W['uniacid']} ");

            $channel_info['channel']['commission_total'] = number_format(pdo_fetchcolumn("SELECT sum(apply_money) FROM " . tablename('sz_yi_channel_apply') . " WHERE uniacid={$_W['uniacid']} AND openid='{$openid}'"), 2);

            $channel_info['channel']['commission_ok'] = pdo_fetchcolumn("SELECT sum(og.price) FROM " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_order') . " o on (o.id=og.orderid) WHERE o.uniacid={$_W['uniacid']} AND og.channel_id={$member['id']} AND o.status=3 AND og.channel_apply_status=0 ");


            $channel_info['channel']['mychannels'] = pdo_fetchall("SELECT * FROM " .tablename('sz_yi_member') . " WHERE uniacid={$_W['uniacid']} AND channel_level<>0 AND agentid={$member['id']}");

            $level = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid={$_W['uniacid']} AND id={$member['channel_level']}");
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
            	$up_level = $this->getUpChannel($openid);
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
			$member = m('member')->getInfo($openid);
			$member['level_num'] = pdo_fetchcolumn("SELECT level_num FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid={$_W['uniacid']} AND id={$member['channel_level']}");
			if (empty($member['level_num'])) {
				$member['level_num'] = -1;
			}
			if (empty($member['agentid'])) {
				return;
			}
			$up_channel = pdo_fetch("SELECT * FROM " . tablename('sz_yi_member') . " WHERE uniacid={$_W['uniacid']} AND id={$member['agentid']}");
			if (!empty($up_channel['channel_level'])) {
				$up_level = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid={$_W['uniacid']} AND id={$up_channel['channel_level']}");
				if (!empty($goodsid)) {
					$condtion = " AND goodsid={$goodsid}";
					if (!empty($optionid)) {
						$condtion = " AND goodsid={$goodsid} AND optionid={$optionid} AND stock_total>={$total}";
					}
					$up_stock = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid={$_W['uniacid']} AND openid='{$up_channel['openid']}' AND stock_total>0 {$condtion} AND stock_total>={$total}");
				} else {
					$up_stock = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid={$_W['uniacid']} AND openid='{$up_channel['openid']}' AND stock_total>0");
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
		  * 渠道商根据订单升级
		  *
		  * @param string $openid 用户openid
		  */
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
			$become = intval($set['become']);
			if ($become == 2 || $become == 3) {
				$level_info = $this->getLevel($member['openid']);
				if (empty($level_info['id'])) {
					$level_info = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid={$_W['uniacid']} AND level_num=0");
				}
				$my_orders = pdo_fetch('SELECT sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount FROM ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' WHERE o.openid=:openid AND o.status>=3 AND o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
				$my_ordermoney = $my_orders['ordermoney'];
				$my_ordercount = $my_orders['ordercount'];
				if ($become == 2) {
					$level = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_channel_level') . " WHERE uniacid=:uniacid  AND {$my_ordermoney} >= become AND become>0  order by become desc limit 1", array(':uniacid' => $_W['uniacid']));
					if (empty($level)) {
						return;
					}
					if (!empty($level_info['id'])) {
						if ($level_info['id'] == $level['id']) {
							return;
						}
						if ($level_info['become'] > $level['become']) {
							return;
						}
					}
				} else if ($become == 3) {
					$level = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_channel_level') . " WHERE uniacid=:uniacid  AND {$my_ordercount} >= become AND become>0  order by become desc limit 1", array(':uniacid' => $_W['uniacid']));
					if (empty($level)) {
						return;
					}
					if (!empty($level_info['id'])) {
						if ($level_info['id'] == $level['id']) {
							return;
						}
						if ($level_info['become'] > $level['become']) {
							return;
						}
					}
				}
				pdo_update('sz_yi_member', array('channel_level' => $level['id']), array('id' => $member['id']));
				//$this->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'oldlevel' => $level_info, 'newlevel' => $level,), TM_COMMISSION_UPGRADE);
			}
		}
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
			$my_agents = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('sz_yi_member') . " WHERE uniacid={$_W['uniacid']} AND agentid={$member['id']} AND status=1 AND isagent=1 AND channel_level>0");
			if (empty($member)) {
				return;
			}
			if ($set['become'] == 1) {
				$channel_level = pdo_fetch("SELECT id FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid={$_W['uniacid']} AND $my_agents>=teamtotal order by teamtotal asc limit 1");
				if (!empty($channel_level) && $member['channel_level'] != $channel_level['id']) {
					pdo_update('sz_yi_member', array('channel_level' => $channel_level['id']), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
					//消息通知
				}
			}
		}
		/**
		  * 检索该订单完成状态
		  *
		  * @param int $orderid 订单的id
		  */
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
			$order = pdo_fetch('SELECT id,openid,ordersn,goodsprice,agentid,paytime,finishtime FROM ' . tablename('sz_yi_order') . ' WHERE id=:id AND status>=1 AND uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($order)) {
				return;
			}
			$openid = $order['openid'];
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			$this->upgradeLevelByAgent($openid);
			$this->upgradeLevelByOrder($openid);
		}
		/**
		  * 检索该订单支付状态
		  *
		  * @param int $orderid 订单的id
		  */
		public function checkOrderPay($orderid = '0')
		{
			global $_W, $_GPC;
			if (empty($orderid)) {
				return;
			}
			$order = pdo_fetch('SELECT id,openid,ordersn,goodsprice,agentid,paytime FROM ' . tablename('sz_yi_order') . ' WHERE id=:id AND status>=1 AND uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($order)) {
				return;
			}
			$openid = $order['openid'];
			$member = m('member')->getMember($openid);
			if (empty($member)) {
				return;
			}
			$this->upgradeLevelByAgent($openid);
			$this->upgradeLevelByOrder($openid);
		}
	}
}