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
      	  * @return array $set
		  */
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}
		/**
		  * 渠道商根据订单升级
		  * @param string $mid 用户id
		  */
		public function getChannelInfo($mid)
		{
			global $_W;
			$channelinfo = array();
		}
		/**
		  * 渠道商根据订单升级
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
					$level_info = pdo_fetch("select * from " . tablename('sz_yi_channel_level') . " where uniacid={$_W['uniacid']} and level_num=0");
				}
				$my_orders = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.openid=:openid and o.status>=3 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
				$my_ordermoney = $my_orders['ordermoney'];
				$my_ordercount = $my_orders['ordercount'];
				if ($become == 2) {
					$level = pdo_fetch('select * from ' . tablename('sz_yi_channel_level') . " where uniacid=:uniacid  and {$my_ordermoney} >= become and become>0  order by become desc limit 1", array(':uniacid' => $_W['uniacid']));
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
					$level = pdo_fetch('select * from ' . tablename('sz_yi_channel_level') . " where uniacid=:uniacid  and {$my_ordercount} >= become and become>0  order by become desc limit 1", array(':uniacid' => $_W['uniacid']));
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
			$level = pdo_fetch('select * from ' . tablename('sz_yi_channel_level') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $member['channel_level']));
			return $level;
		}
		/**
		  * 渠道商根据直属下线升级
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
			$my_agents = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_member') . " where uniacid={$_W['uniacid']} and agentid={$member['id']} and status=1 and isagent=1 and channel_level>0");
			if (empty($member)) {
				return;
			}
			if ($set['become'] == 1) {
				$channel_level = pdo_fetch("select id from " . tablename('sz_yi_channel_level') . " where uniacid={$_W['uniacid']} and $my_agents>=teamtotal order by teamtotal asc limit 1");
				if (!empty($channel_level) && $member['channel_level'] != $channel_level['id']) {
					pdo_update('sz_yi_member', array('channel_level' => $channel_level['id']), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
					//消息通知
				}
			}
		}
		/**
		  * 检索该订单完成状态
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
			$order = pdo_fetch('select id,openid,ordersn,goodsprice,agentid,paytime,finishtime from ' . tablename('sz_yi_order') . ' where id=:id and status>=1 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
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
		  * @param int $orderid 订单的id
		  */
		public function checkOrderPay($orderid = '0')
		{
			global $_W, $_GPC;
			if (empty($orderid)) {
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
			$this->upgradeLevelByAgent($openid);
			$this->upgradeLevelByOrder($openid);
		}
	}
}