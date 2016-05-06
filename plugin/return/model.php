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

		public function cumulative_order_amount($orderid) {
			global $_W;
			$_var_0 = $this->getSet();
			if ($_var_0['isreturn'] == 1) {
				if (empty($orderid)) {
					return false;
				}
				$order_goods = pdo_fetchall("SELECT og.price,g.isreturn,o.openid,m.id as mid FROM " . tablename('sz_yi_order') . " o left join " . tablename('sz_yi_member') . " m  on o.openid = m.openid left join " . tablename("sz_yi_order_goods") . " og on og.orderid = o.id  left join " . tablename("sz_yi_goods") . " g on g.id = og.goodsid WHERE o.id = :orderid and o.uniacid = :uniacid",
					array(':orderid' => $orderid,':uniacid' => $_W['uniacid']
				));
				//print_r($order_goods);exit;
				$order_price = 0;
				foreach($order_goods as $good){
 					if($good['isreturn'] == 1){
 						$order_price += $good['price'];
 					}
				}
				if (empty($order_goods)) {
					return false;
				}
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
				return $returnid;
			}
			
		}

		// 查询可参加返利的 加入返利队列
		public function getmoney($orderprice,$uniacid){

			$data_money = pdo_fetchall("select * from " . tablename('sz_yi_return_money') . " where uniacid = '". $uniacid . "' and money >= '" . $orderprice . "' ");
			if($data_money){
				foreach ($data_money as $key => $value) {
					if($value['money'] >= $orderprice){
						$data = array(
							'uniacid' 		=> $value['uniacid'],
							'mid' 			=> $value['mid'],
							'money' 		=> $orderprice,
							'create_time'	=> time()
							 );
						pdo_insert('sz_yi_return', $data);
						pdo_update('sz_yi_return_money', array('money'=>$value['money']-$orderprice), array('id' => $value['id'], 'uniacid' => $uniacid));	
					}
				}
				$this->getmoney($orderprice,$uniacid);
			}

		}

	}
}
