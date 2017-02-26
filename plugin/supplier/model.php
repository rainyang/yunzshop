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
         * @name 某个供应商下的招商员
         * @author yangyang
         * @param int $uid
         * @return array $merchants
         */
        public function getSupplierMerchants($uid)
        {
            global $_W, $_GPC;
            if (empty($uid)) {
                return array();
            }
            $params = array(
                ':uniacid'      => $_W['uniacid'],
                ':supplier_uid' => $uid
            );
            //供应商supplier_uid下的所有招商员
            $sql  = 'SELECT mc.*, m.avatar, m.nickname, m.realname, m.mobile FROM ' . tablename('sz_yi_merchants');
            $sql .= ' mc LEFT JOIN ' . tablename('sz_yi_member');
            $sql .= ' m ON m.openid = mc.openid ';
            $sql .= ' WHERE mc.uniacid = :uniacid AND mc.supplier_uid = :supplier_uid';
            $sql .= ' ORDER BY mc.id DESC ';
            $merchants = pdo_fetchall($sql, $params);

            return $merchants;
        }

        /**
         * @name 供应商角色权限id
         * @author yangyang
         * @return int $roleid
         */
        public function getRoleId()
        {
            global $_W;
            //权限id
            $params = array(
                ':status'   => 1
            );
            $sql    = 'SELECT id FROM ' . tablename('sz_yi_perm_role');
            $sql   .= ' WHERE status1 = :status';
            $roleid = pdo_fetchcolumn($sql, $params);
            return $roleid;
        }

        /**
         * @name 商城下所有的供应商
         * @author yangyang
         * @return array $all_suppliers
         */
        public function AllSuppliers()
        {
            global $_W;
            $roleid = $this->getRoleId();
            $params = array(
                ':uniacid'  => $_W['uniacid'],
                ':roleid'   => $roleid
            );
            $sql    = 'SELECT * FROM ' . tablename('sz_yi_perm_user');
            $sql   .= ' WHERE uniacid = :uniacid AND roleid = :roleid';

            $all_suppliers = pdo_fetchall($sql, $params);
            return $all_suppliers;
        }

        /**
         * @name 获取供应商订单佣金相关数据
         * @author yangyang
         * @param  int $uid
         * @return array $supplierinfo
         */
        public function getSupplierInfo($uid)
        {
            global $_W;
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
            $supplierinfo['totalmoney'] = pdo_fetchcolumn("select sum(apply_money) from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and uid={$uid}");
            return $supplierinfo;
        }

        /**
         * @name通过前台用户openid获取供应商uid和username
         * @author yangyang
         * @param  string $openid
         * @return array $supplieruser
         */
        public function getSupplierUidAndUsername($openid)
        {
            global $_W;
            //查询uid和username
            if (empty($openid)) {
                return;
            }
            $params = array(
                ':uniacid'  => $_W['uniacid'],
                ':openid'   => $openid
            );
            $sql = 'SELECT uid, username FROM ' . tablename('sz_yi_perm_user');
            $sql .= ' WHERE uniacid = :uniacid AND openid = :openid';
            $supplieruser = pdo_fetch($sql, $params);
            return $supplieruser;
        }

        /**
         * @name 前台判断用户是否为供应商
         * @author yangyang
         * @param  string $openid
         * @return array $issupplier
         */
        public function isSupplier($openid)
        {
            global $_W;
            //不为空时，该用户是供应商
            if (empty($openid)) {
                return;
            }
            $roleid = $this->getRoleId();
            $params = array(
                ':uniacid'  => $_W['uniacid'],
                ':openid'   => $openid,
                ':roleid'   => $roleid
            );
            $sql = 'SELECT * FROM ' . tablename('sz_yi_perm_user');
            $sql .= ' WHERE uniacid = :uniacid AND openid = :openid AND roleid = :roleid';
            $issupplier = pdo_fetch($sql, $params);
            return $issupplier;
        }

        /**
         * @name 获取供应商权限角色id
         * @author yangyang
         * @return int $permid
         */
        public function getSupplierPermId()
        {
            global $_W;
            $roleid = $this->getRoleId();
            if(empty($roleid)){
                $data = array(
                    'rolename'  => '供应商',
                    'status'    => 1,
                    'status1'   => 1,
                    'perms'     => 'shop,shop.goods,shop.goods.view,shop.goods.add,shop.goods.edit,shop.goods.delete,order,order.view,order.view.status_1,order.view.status0,order.view.status1,order.view.status2,order.view.status3,order.view.status4,order.view.status5,order.view.status9,order.op,order.op.pay,order.op.send,order.op.sendcancel,order.op.finish,order.op.verify,order.op.fetch,order.op.close,order.op.refund,order.op.export,order.op.changeprice,exhelper,exhelper.print,exhelper.print.single,exhelper.print.more,exhelper.exptemp1,exhelper.exptemp1.view,exhelper.exptemp1.add,exhelper.exptemp1.edit,exhelper.exptemp1.delete,exhelper.exptemp1.setdefault,exhelper.exptemp2,exhelper.exptemp2.view,exhelper.exptemp2.add,exhelper.exptemp2.edit,exhelper.exptemp2.delete,exhelper.exptemp2.setdefault,exhelper.senduser,exhelper.senduser.view,exhelper.senduser.add,exhelper.senduser.edit,exhelper.senduser.delete,exhelper.senduser.setdefault,exhelper.short,exhelper.short.view,exhelper.short.save,exhelper.printset,exhelper.printset.view,exhelper.printset.save,exhelper.dosen,taobao,taobao.fetch',
                    'deleted'   => 0
                );
                pdo_insert('sz_yi_perm_role' , $data);
                $permid = pdo_insertid();
            }else{
                $permid = $roleid;
            }
            return $permid;
        }

        /**
         * @name 验证用户是否为供应商，$perm_role不为空是供应商。
         * @author yangyang
         * @param   int $uid
         * @return int $permid
         */
        public function verifyUserIsSupplier($uid)
        {
            global $_W;
            $params = array(
                ':uniacid'  => $_W['uniacid'],
                ':uid'      => $uid
            );
            $role_sql  = 'SELECT roleid FROM ' . tablename('yz_perm_user');
            $role_sql .= ' WHERE uniacid = :uniacid AND uid = :uid';
            $roleid    = pdo_fetchcolumn($role_sql, $params);
            if ($roleid != 0) {
                $perm_sql = 'SELECT status1 FROM ' . tablename('sz_yi_perm_role');
                $perm_sql .= ' WHERE id = :id';
                $perm_role = pdo_fetchcolumn($perm_sql, array(
                    ':id'   => $roleid
                ));
                return $perm_role;
            }
        }

        /**
         * @name 支出设置
         * @author yangyang
         */
        public function getSet()
        {
            $set = parent::getSet();
            return $set;
        }

        /**
         * @name 通知设置
         * @author yangyang
         * @param string $openid array $data string $becometitle
         */
        public function sendMessage($openid = '', $data = array(), $becometitle = '')
        {
            global $_W;
            $member = m('member')->getMember($openid);
            if ($becometitle == TM_SUPPLIER_PAY) {
                $message = '恭喜您，您的提现将通过 [提现方式] 转账提现金额为[金额]已在[时间]转账到您的账号，敬请查看';
                $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
                $message = str_replace('[金额]', $data['money'], $message);
                $message = str_replace('[提现方式]', $data['type'], $message);
                $msg = array(
                    'keyword1'  => array(
                        'value' => '供应商打款通知',
                        'color' => '#73a68d'
                    ),
                    'keyword2'  => array(
                        'value' => $message,
                        'color' => '#73a68d'
                    )
                );
                m('message')->sendCustomNotice($openid, $msg);
            }
        }

        /**
         * @name 推送申请审核结果
         * @author yangyang
         * @param string $openid int $status
         */
        public function sendSupplierInform($openid = '', $status = '')
        {
            global $_W;
            if ($status == 1) {
                $resu = '驳回';
            } else {
                $resu = '通过';
            }
            $set     = $this->getSet();
            $tm      = $set['tm'];
            $message = $tm['commission_become'];
            $message = str_replace('[状态]', $resu, $message);
            $message = str_replace('[时间]', date('Y-m-d H:i', time()), $message);
            if (!empty($tm['commission_becometitle'])) {
                $title = $tm['commission_becometitle'];
            } else {
                $title = '会员申请供应商通知';
            }
            $msg = array(
                'keyword1' => array(
                    'value' => $title,
                    'color' => '#73a68d'
                ),
                'keyword2' => array(
                    'value' => $message,
                    'color' => '#73a68d'
                )
            );
            m('message')->sendCustomNotice($openid, $msg);
        }
    }
}
