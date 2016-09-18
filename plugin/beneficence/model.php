<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('BeneficenceModel')) {

	class BeneficenceModel extends PluginModel
	{
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}
		public function GetVirtualBeneficence($orderid)
		{
			global $_W, $_GPC;
			$set = $this->getSet();
			if ($set['isbeneficence'] != 1) {
				return false;
			}
			if (empty($orderid)) {
				return false;
			}
			$order_goods = pdo_fetchall("SELECT o.openid,o.price,m.id,m.openid as mid FROM " . tablename('sz_yi_order') . " o left join " . tablename('sz_yi_member') . " m  on o.openid = m.openid left join " . tablename("sz_yi_order_goods") . " og on og.orderid = o.id  left join " . tablename("sz_yi_goods") . " g on g.id = og.goodsid WHERE o.id = :orderid and o.uniacid = :uniacid and m.uniacid = :uniacid",
				array(':orderid' => $orderid,':uniacid' => $_W['uniacid']
			));
			if (empty($order_goods)) {
				return false;
			}
			$beneficence = 0;
			foreach($order_goods as $good){
				$beneficence += $good['price'] * $set['money'] /100;
			}
			$info = m('member')->getMember($order_goods[0]['mid']);
			$names = $info['nickname']?$info['nickname']:$info['realname'];
			$name = mb_substr($names,0,2,'utf-8')."*****".mb_substr($names,-2,2,'utf-8');
			$data = array(
				'uniacid' 		=> $_W['uniacid'],
			    'name' 			=> $name,
			    'money' 		=> $beneficence,
				'create_time'	=> time()
			);
			pdo_insert('sz_yi_beneficence', $data);
		}
		
	}
}
