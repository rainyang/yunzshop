<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
define('TM_SUPPLIER_PAY', 'supplier_pay');
if (!class_exists('SupplierModel')) {

	class SupplierModel extends PluginModel
	{
        public function getSupplierName($supplier_uid){
            global $_W;
            if (m('cache')->get('supplier_' . $supplier_uid)){
                return m('cache')->get('supplier_' . $supplier_uid);
            }
            $supplierName = pdo_fetchcolumn("select username from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and uid={$supplier_uid}");
            m('cache')->set('supplier_' . $supplier_uid, $supplierName);
            return $supplierName;
        }

        /**
         * 某个供应商下的招商员
         *
         * @param int $uid
         * @return array $merchants
         */
        public function getSupplierMerchants($uid){
            global $_W, $_GPC;
            if (empty($uid)) {
                return array();
            }
            //uid下的所有招商员
            $merchants = pdo_fetchall("select * from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and supplier_uid={$uid} ORDER BY id DESC");
            //循环赋予头像等信息
            foreach ($merchants as &$value) {
                $merchants_member = m('member')->getMember($value['openid']);
                $value['avatar'] = $merchants_member['avatar'];
                $value['nickname'] = $merchants_member['nickname'];
                $value['realname'] = $merchants_member['realname'];
                $value['mobile'] = $merchants_member['mobile'];
            }
            unset($value);
            return $merchants;
        }

        /**
         * 供应商角色权限id
         *
         * @param
         * @return int $roleid
         */
        public function getRoleId(){
            global $_W, $_GPC;
            //权限id
            $roleid = pdo_fetchcolumn('select id from ' . tablename('sz_yi_perm_role') . ' where status1=1');
            return $roleid;
        }

        /**
         * 商城下所有的供应商
         *
         * @param
         * @return array $all_suppliers
         */
        public function AllSuppliers(){
            global $_W, $_GPC;
            $roleid = $this->getRoleId();
            $all_suppliers = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_perm_user') . " WHERE uniacid=:uniacid AND roleid=:roleid", array(':uniacid' => $_W['uniacid'], ':roleid' => $roleid));
            return $all_suppliers;
        }

        /**
         * 获取供应商订单佣金相关数据
         *
         * @param  int $uid
         * @return array $supplierinfo
         */
        public function getSupplierInfo($uid){
            global $_W, $_GPC;
            $supplierinfo = array();
            $set = $this->getSet();
            //提现限制
            if (!empty($set['limit_day'])) {
                $time = time();
                if (!empty($uid)) {
                    $last_apply_time = pdo_fetchcolumn("SELECT apply_time FROM " . tablename('sz_yi_supplier_apply') . "WHERE uniacid={$_W['uniacid']} AND uid={$uid} ORDER BY id DESC LIMIT 1");
                    if (!empty($last_apply_time)) {
                        $last_time = $last_apply_time + $set['limit_day']*60*60*24;
                        if ($last_time > $time) {
                            $supplierinfo['limit_day'] = true;
                            $supplierinfo['last_time'] = date('Y-m-d H:i:s', $last_time);
                        }
                    }
                }
            }
            //订单总数
            $supplierinfo['ordercount'] = 0;
            //累积佣金
            $supplierinfo['commission_total'] = 0;
            //可提现佣金
            $supplierinfo['costmoney'] = 0;
            //累积佣金
            $supplierinfo['totalmoney'] = 0;
            //订单数量
            $supplierinfo['ordercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order') . " where supplier_uid={$uid} and userdeleted=0 and deleted=0 and uniacid={$_W['uniacid']} ");
            //提现总额
            $supplierinfo['commission_total'] = pdo_fetchcolumn("select sum(apply_money) from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and uid={$uid} and status=1");
            /*$sp_goods = pdo_fetchall("select og.* from " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_order') . " o on (o.id=og.orderid) where og.uniacid={$_W['uniacid']} and og.supplier_uid={$uid} and o.status=3 and og.supplier_apply_status=0");
            foreach ($sp_goods as $key => $value) {
                if ($value['goods_op_cost_price'] > 0) {
                    $supplierinfo['costmoney'] += $value['goods_op_cost_price'] * $value['total'];
                } else {
                    $option = pdo_fetch("select * from " . tablename('sz_yi_goods_option') . " where uniacid={$_W['uniacid']} and goodsid={$value['goodsid']} and id={$value['optionid']}");
                    if ($option['costprice'] > 0) {
                        $supplierinfo['costmoney'] += $option['costprice'] * $value['total'];
                    } else {
                        $goods_info = pdo_fetch("select * from" . tablename('sz_yi_goods') . " where uniacid={$_W['uniacid']} and id={$value['goodsid']}");
                        $supplierinfo['costmoney'] += $goods_info['costprice'] * $value['total'];
                    }
                }
            }*/
            $supplierinfo['sp_goods'] = array();
            $supplierinfo['costmoney'] = 0;
            $supplierinfo['costmoney_total'] = 0;
            $supplierinfo['expect_money'] = '0.00';

            $apply_cond = "";
            $apply_conds = "";
            $now_time = time();
            //订单完成X天
            if (!empty($set['apply_day'])) {
                $apply_day = $now_time - $set['apply_day']*60*60*24;
                $apply_cond .= " AND o.finishtime<{$apply_day} ";
                $apply_conds = " AND o.finishtime>{$apply_day} ";
                $supplierinfo['expect_money'] = pdo_fetchcolumn("SELECT sum(so.money) FROM " . tablename('sz_yi_supplier_order') . " so left join " . tablename('sz_yi_order') . " o on o.id=so.orderid left join " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id where o.uniacid={$_W['uniacid']} and o.supplier_uid={$uid} and o.status=3 and og.supplier_apply_status=0 {$apply_conds}");
            }
            $supplierinfo['costmoney'] = pdo_fetchall("SELECT so.money FROM " . tablename('sz_yi_supplier_order') . " so left join " . tablename('sz_yi_order') . " o on o.id=so.orderid left join " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id where o.uniacid={$_W['uniacid']} and o.supplier_uid={$uid} and o.status=3 and og.supplier_apply_status=0");
            /*if (!empty($costmoney_total)) {
                foreach ($costmoney_total as $c) {
                    $supplierinfo['costmoney_total'] += $c['money'];
                }
            }*/
            $order_ids = pdo_fetchall('SELECT DISTINCT o.id FROM ' . tablename('sz_yi_order_goods') . ' og LEFT JOIN ' . tablename('sz_yi_order') . ' o ON o.id = og.orderid WHERE o.uniacid = :uniacid AND o.status = :status AND og.supplier_uid = :supplier_uid AND og.supplier_apply_status = :supplier_apply_status ' . $apply_cond, array(
                ':uniacid'                  => $_W['uniacid'],
                ':status'                   => 3,
                ':supplier_uid'             => $uid,
                ':supplier_apply_status'    => 0
            ));
            if (empty($order_ids)) {
                $supplierinfo['costmoney'] = 0;
            } else {
                $orderids = array();
                foreach ($order_ids AS $o) {
                    $orderids[] = $o['id'];
                }
                $supplierinfo['costmoney'] = pdo_fetchcolumn('SELECT ifnull(sum(money), 0) FROM ' . tablename('sz_yi_supplier_order') . ' WHERE uniacid = :uniacid AND orderid in(' . implode(',', $orderids) . ')', array(
                    ':uniacid'  => $_W['uniacid']
                ));
            }
            $order_goods_ids = pdo_fetchall('SELECT og.id AS ogid FROM ' . tablename('sz_yi_order_goods') . ' og LEFT JOIN ' . tablename('sz_yi_order') . ' o ON o.id = og.orderid WHERE o.uniacid = :uniacid AND o.status = :status AND og.supplier_uid = :supplier_uid AND og.supplier_apply_status = :supplier_apply_status ' . $apply_cond, array(
                ':uniacid'                  => $_W['uniacid'],
                ':status'                   => 3,
                ':supplier_uid'             => $uid,
                ':supplier_apply_status'    => 0
            ));
            $supplierinfo['sp_goods'] = $order_goods_ids;
            /*echo '<pre>';print_r($order_goods_ids);exit;
            $supplier_orders = pdo_fetchall("SELECT so.*,o.id as oid,og.id as ogid FROM " . tablename('sz_yi_supplier_order') . " so left join " . tablename('sz_yi_order') . " o on o.id=so.orderid left join " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id where o.uniacid={$_W['uniacid']} and o.supplier_uid={$uid} and o.status=3 and og.supplier_apply_status=0 {$apply_cond}  GROUP BY so.id");
			if (!empty($supplier_orders)) {
                $supplierinfo['sp_goods'] = $supplier_orders;
                $supplierinfo['costmoney'] = 0;
                foreach ($supplier_orders as $o) {
                    $supplierinfo['costmoney'] += $o['money'];

                }
            } */
            $supplierinfo['totalmoney'] = pdo_fetchcolumn("select sum(apply_money) from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and uid={$uid}");
            return $supplierinfo;
        }

        /**
         * 通过前台用户openid获取供应商uid和username
         *
         * @param  string $openid
         * @return array $supplieruser
         */
        public function getSupplierUidAndUsername($openid){
            global $_W, $_GPC;
            //查询uid和username
            $supplieruser = pdo_fetch("select uid,username from " . tablename('sz_yi_perm_user') . " where openid='{$openid}' and uniacid={$_W['uniacid']}");
            return $supplieruser;
        }

        /**
         * 前台判断用户是否为供应商
         *
         * @param  string $openid
         * @return array $issupplier
         */
        public function isSupplier($openid){
            global $_W, $_GPC;
            //不为空时，该用户是供应商
            $issupplier = pdo_fetch("select * from " . tablename('sz_yi_perm_user') . " where openid='{$openid}' and uniacid={$_W['uniacid']} and roleid=(select id from " . tablename('sz_yi_perm_role') . " where status1=1)");
            return $issupplier;
        }

        /**
         * 获取供应商权限角色id
         *
         * @param
         * @return int $permid
         */
        public function getSupplierPermId(){
            global $_W, $_GPC;
            $perms = pdo_fetch('select * from ' . tablename('sz_yi_perm_role') . ' where status1 = 1');
            $supplier_perms = 'shop,shop.goods,shop.goods.view,shop.goods.add,shop.goods.edit,shop.goods.delete,order,order.view,order.view.status_1,order.view.status0,order.view.status1,order.view.status2,order.view.status3,order.view.status4,order.view.status5,order.view.status9,order.op,order.op.pay,order.op.send,order.op.sendcancel,order.op.finish,order.op.verify,order.op.fetch,order.op.close,order.op.refund,order.op.export,order.op.changeprice,exhelper,exhelper.print,exhelper.print.single,exhelper.print.more,exhelper.exptemp1,exhelper.exptemp1.view,exhelper.exptemp1.add,exhelper.exptemp1.edit,exhelper.exptemp1.delete,exhelper.exptemp1.setdefault,exhelper.exptemp2,exhelper.exptemp2.view,exhelper.exptemp2.add,exhelper.exptemp2.edit,exhelper.exptemp2.delete,exhelper.exptemp2.setdefault,exhelper.senduser,exhelper.senduser.view,exhelper.senduser.add,exhelper.senduser.edit,exhelper.senduser.delete,exhelper.senduser.setdefault,exhelper.short,exhelper.short.view,exhelper.short.save,exhelper.printset,exhelper.printset.view,exhelper.printset.save,exhelper.dosen,taobao,taobao.fetch';
            if(empty($perms)){
                $data = array(
                    'rolename' => '供应商',
                    'status' => 1,
                    'status1' => 1,
                    'perms' => $supplier_perms,
                    'deleted' => 0
                    );
                pdo_insert('sz_yi_perm_role' , $data);
                $permid = pdo_insertid();
            }else{
                $permid = $perms['id'];
            }
            return $permid;
        }

        /**
         * 验证用户是否为供应商，$perm_role不为空是供应商。
         *
         * @param   int $uid
         * @return int $permid
         */
		public function verifyUserIsSupplier($uid)
		{
			global $_W, $_GPC;
			$roleid = pdo_fetchcolumn('select roleid from' . tablename('sz_yi_perm_user') . ' where uid='.$uid.' and uniacid=' . $_W['uniacid']);
	        if ($roleid != 0) {
	            $perm_role = pdo_fetchcolumn('select status1 from' . tablename('sz_yi_perm_role') . ' where id=' . $roleid);
	            return $perm_role;
	        }
		}
        
        //获取供应商的基础设置
		public function getSet()
		{	
			$set = parent::getSet();
			return $set;
		}

        //通知设置
		public function sendMessage($openid = '', $data = array(), $becometitle = '')
		{
            global $_W, $_GPC;
			$member = m('member')->getMember($openid);
			if ($becometitle == TM_SUPPLIER_PAY) {
				$message = '恭喜您，您的提现将通过 [提现方式] 转账提现金额为[金额]已在[时间]转账到您的账号，敬请查看';
				$message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
				$message = str_replace('[金额]', $data['money'], $message);
				$message = str_replace('[提现方式]', $data['type'], $message);
				$msg = array('keyword1' => array('value' => '供应商打款通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
				m('message')->sendCustomNotice($openid, $msg);
			}
		}

        //推送申请审核结果
		public function sendSupplierInform($openid = '', $status = '')
		{	
            global $_W, $_GPC;
			if ($status == 1) {
				$resu = '驳回';
			} else {
				$resu = '通过';
			}
			$set = $this->getSet();
			$tm = $set['tm'];
			$message = $tm['commission_become'];			
			$message = str_replace('[状态]', $resu, $message);
			$message = str_replace('[时间]', date('Y-m-d H:i', time()), $message);
			if (!empty($tm['commission_becometitle'])) {
				$title = $tm['commission_becometitle'];
			} else {
				$title = '会员申请供应商通知';
			}
			$msg = array('keyword1' => array('value' => $title, 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
			m('message')->sendCustomNotice($openid, $msg);
		}
		
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
                    $order['dispatchprice'] = round($dispatchprice/(count($resolve_order_goods)),2);
                    //平分老的支付运费金额
                    $order['olddispatchprice'] = round($olddispatchprice/(count($resolve_order_goods)),2);
                    //平分修改后支付运费金额
                    $order['changedispatchprice'] = round($changedispatchprice/(count($resolve_order_goods)),2);
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
