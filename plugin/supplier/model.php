<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
define('TM_SUPPLIER_PAY', 'supplier_pay');
if (!class_exists('SupplierModel')) {

	class SupplierModel extends PluginModel
	{
        public $parentAgents = "";

		public function verifyUserIsSupplier($uid)
		{
			global $_W, $_GPC;
			$roleid = pdo_fetchcolumn('select roleid from' . tablename('sz_yi_perm_user') . ' where uid='.$uid.' and uniacid=' . $_W['uniacid']);
	        if ($roleid != 0) {
	            $perm_role = pdo_fetchcolumn('select status1 from' . tablename('sz_yi_perm_role') . ' where id=' . $roleid);
	            return $perm_role;
	        }
		}
		public function getSet()
		{	
			$_var_0 = parent::getSet();
			return $_var_0;
		}
		public function sendMessage($openid = '', $data = array(), $_var_151 = '')
		{
			$_var_22 = m('member')->getMember($openid);
			if ($_var_151 == TM_SUPPLIER_PAY) {
				$_var_155 = '恭喜您，您的提现将通过 [提现方式] 转账提现金额为[金额]已在[时间]转账到您的账号，敬请查看';
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $_var_155);
				$_var_155 = str_replace('[金额]', $data['money'], $_var_155);
				$_var_155 = str_replace('[提现方式]', $data['type'], $_var_155);
				$_var_156 = array('keyword1' => array('value' => '供应商打款通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
				/*if (!empty($_var_153)) {
					m('message')->sendTplNotice($openid, $_var_153, $_var_156);
				} else {*/
				m('message')->sendCustomNotice($openid, $_var_156);
				//}
			}
		}

		public function sendSupplierInform($openid = '', $status = '')
		{	
			if ($status == 1) {
				$resu = '驳回';
			} else {
				$resu = '通过';
			}
			$set = $this->getSet();
			$_var_152 = $set['tm'];
			$_var_155 = $_var_152['commission_become'];			
			$_var_155 = str_replace('[状态]', $resu, $_var_155);
			$_var_155 = str_replace('[时间]', date('Y-m-d H:i', time()), $_var_155);
			if (!empty($_var_152['commission_becometitle'])) {
				$title = $_var_152['commission_becometitle'];
			} else {
				$title = '会员申请供应商通知';
			}
			$_var_156 = array('keyword1' => array('value' => $title, 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
			m('message')->sendCustomNotice($openid, $_var_156);
		}
		
        //订单分解
        /**订单分解修改，订单会员折扣、积分折扣、余额抵扣、使用优惠劵后订单分解按商品价格与总商品价格比例拆分，使用运费的平分运费。添加平分修改运费以及修改订单金额的信息到新的订单表中。**/
		public function order_split($orderid){
			global $_W;
			if(empty($orderid)){
				return;
			}
            $supplier_order_goods = pdo_fetchall("select distinct supplier_uid from " . tablename('sz_yi_order_goods') . " where orderid=:orderid and uniacid=:uniacid",array(
                    ':orderid' => $orderid,
                    ':uniacid' => $_W['uniacid']
            ));

            //查询不重复supplier_uid订单，如只有一个不进行拆单
            if(count($supplier_order_goods) == 1){
            	pdo_update('sz_yi_order', 
            		array(
            			"supplier_uid" => $supplier_order_goods[0]['supplier_uid']), 
            		array(
                        'id' => $orderid,
                        'uniacid' => $_W['uniacid']
                        ));
                return;
            }
            $resolve_order_goods = pdo_fetchall('select supplier_uid, id from ' . tablename('sz_yi_order_goods') . ' where orderid=:orderid and uniacid=:uniacid ',array(
                    ':orderid' => $orderid,
                    ':uniacid' => $_W['uniacid']
            ));
            $orderdata = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where  id=:id and uniacid=:uniacid limit 1', array(
                        ':uniacid' => $_W['uniacid'],
                        ':id' => $orderid
                        ));
            $issplit = ture;
            $datas = array();
            //对应供应商商品循环到对应供应商下
            foreach ($resolve_order_goods as $key => $value) {
                $datas[$value['supplier_uid']][]['id'] = $value['id'];
            }

            $num = false;
            unset($orderdata['id']);
            unset($orderdata['uniacid']);
            $dispatchprice = $orderdata['dispatchprice'];
            $olddispatchprice = $orderdata['olddispatchprice'];
            $changedispatchprice = $orderdata['changedispatchprice'];
            
            if(!empty($datas)){
                foreach ($datas as $key => $value) {
                    $order = $orderdata;
                    $price = 0;
                    $realprice = 0;
                    $oldprice = 0;
                    $changeprice = 0;
                    $goodsprice = 0;
                    $couponprice = 0;
                    $discountprice = 0;
                    $deductprice = 0;
                    $deductcredit2 = 0;
                    foreach($value as $v){
                        $resu = pdo_fetch('select price,realprice,oldprice,supplier_uid from ' . tablename('sz_yi_order_goods') . ' where id=:id and uniacid=:uniacid ',array(
                                ':id' => $v['id'],
                                ':uniacid' => $_W['uniacid']
                            ));
                        $price += $resu['price'];
                        $realprice += $resu['realprice'];
                        $oldprice += $resu['oldprice'];
                        $goodsprice += $resu['price'];
                        $supplier_uid = $key;
                        $changeprice += $resu['changeprice'];
                        //计算order_goods表中的价格占订单商品总额的比例
                        $scale = $resu['price']/$order['goodsprice'];
                        //按比例计算优惠劵金额
                        $couponprice += round($scale*$order['couponprice'],2);
                        //按比例计算会员折扣金额
                        $discountprice += round($scale*$order['discountprice'],2);
                        //按比例计算积分金额
                        $deductprice += round($scale*$order['deductprice'],2);
                        //按比例计算消费余额金额
                        $deductcredit2 += round($scale*$order['deductcredit2'],2); 
                    }

                    $order['oldprice'] = $oldprice;
                    $order['goodsprice'] = $goodsprice;
                    $order['supplier_uid'] = $supplier_uid;
                    $order['couponprice'] = $couponprice;
                    $order['discountprice'] = $discountprice;
                    $order['deductprice'] = $deductprice;
                    $order['deductcredit2'] = $deductcredit2;
                    $order['changeprice'] = $changeprice;
                    //平分实际支付运费金额
                    $order['dispatchprice'] = round($dispatchprice/(count($resu)),2);
                    //平分老的支付运费金额
                    $order['olddispatchprice'] = round($olddispatchprice/(count($resu)),2);
                    //平分修改后支付运费金额
                    $order['changedispatchprice'] = round($changedispatchprice/(count($resu)),2);
                    //新订单金额计算，实际支付金额减计算后优惠劵金额、会员折金额、积分金额、余额抵扣金额，在加上实际运费的金额。
                    $order['price'] = $realprice - $couponprice - $discountprice - $deductprice - $deductcredit2 + $order['dispatchprice'];

                    if($num == false){
                        pdo_update('sz_yi_order', $order, array(
                            'id' => $orderid,
                            'uniacid' => $_W['uniacid']
                            ));
                        $num = ture;
                    }else{
                        $order['uniacid'] = $_W['uniacid'];
                        $ordersn = m('common')->createNO('order', 'ordersn', 'SH');
                        $order['ordersn'] = $ordersn;
                        pdo_insert('sz_yi_order', $order);
                        $logid = pdo_insertid();
                        $oid = array(
                            'orderid' => $logid
                            );
                        foreach ($value as $val) {
                            pdo_update('sz_yi_order_goods',$oid ,array('id' => $val['id'],'uniacid' => $_W['uniacid']));
                        }  
                    }
                }
            }
		}
	}
}
