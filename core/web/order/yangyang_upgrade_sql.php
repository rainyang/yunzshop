<?php
if(!pdo_fieldexists('sz_yi_article', 'is_helper')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article')." ADD `is_helper` int(11) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_article_category', 'is_helper')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article_category')." ADD `is_helper` int(11) NOT NULL DEFAULT '0';");
}

if (!pdo_fieldexists('sz_yi_goods', 'isforceyunbi')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `isforceyunbi` TINYINT(1) NOT NULL DEFAULT '0';");
}

if (!pdo_fieldexists('sz_yi_order', 'basis_money')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `basis_money` decimal(10,2) DEFAULT NULL;");
}

if (!pdo_fieldexists('sz_yi_cashier_store', 'bonus')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_cashier_store')." ADD `bonus` decimal(10,2) DEFAULT NULL;");
}

if (!pdo_fieldexists('sz_yi_af_supplier', 'diymemberid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_af_supplier')." ADD `diymemberid` int(11) NOT NULL DEFAULT '0';");
}

if (!pdo_fieldexists('sz_yi_af_supplier', 'diymemberfields')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_af_supplier')." ADD `diymemberfields` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
}

if (!pdo_fieldexists('sz_yi_af_supplier', 'diymemberdata')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_af_supplier')." ADD `diymemberdata` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
}
$sql = "
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_supplier_order')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `orderid` int(11) NOT NULL,
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '金额',
  `isopenbonus` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_merchant_order')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `orderid` int(11) NOT NULL,
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '金额',
  `isopenbonus` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
";
pdo_query($sql);
if (p('supplier')) {
    $isinsert = pdo_fetch("SELECT * FROM " . tablename('sz_yi_supplier_order') . " WHERE uniacid=:uniacid", array(':uniacid' => '999999'));
    if (empty($isinsert)) {
        $roleid = p('supplier')->getRoleId();
        if (!empty($roleid)) {
            $suppliers = pdo_fetchall("SELECT uid,uniacid FROM " . tablename('sz_yi_perm_user') . " WHERE roleid=:roleid AND status=1", array(':roleid' => $roleid));
            if (!empty($suppliers)) {
                $orders = array();
                foreach ($suppliers as $key => $value) {
                    $supplierorders = pdo_fetchall("SELECT o.id,o.uniacid,og.goods_op_cost_price,og.total FROM " . tablename('sz_yi_order') . " o LEFT JOIN " . tablename(sz_yi_order_goods) . " og on og.orderid=o.id WHERE o.uniacid=:uniacid AND o.supplier_uid=:supplier_uid AND o.status<=3 AND o.status>=0 AND og.supplier_apply_status=0", array(':uniacid' => $value['uniacid'], ':supplier_uid' => $value['uid']));
                    if (!empty($supplierorders)) {
                        foreach ($supplierorders as $val) {
                            $orders[] = $val;
                        }
                    }
                }
                $i = 0;
                foreach ($orders as $o) {
                    $result = pdo_fetch("SELECT * FROM " . tablename('sz_yi_supplier_order') . " WHERE uniacid=:uniacid AND orderid=:orderid", array(':uniacid' => $o['uniacid'], ':orderid' => $o['id']));
                    if (empty($result)) {
                        $money = $o['goods_op_cost_price']*$o['total'];
                        pdo_insert('sz_yi_supplier_order', array('uniacid' => $o['uniacid'], 'orderid' => $o['id'], 'money' => $money, 'isopenbonus' => 2));
                        $i += 1;
                    }
                }
                if ($i != 0) {
                    pdo_insert('sz_yi_supplier_order', array('uniacid' => '999999', 'orderid' => '999999', 'money' => '999999', 'isopenbonus' => '999999'));
                }
            }
        }
    }
}
if (p('merchant')) {
    $isinsert = pdo_fetch("SELECT * FROM " . tablename('sz_yi_merchant_order') . " WHERE uniacid=:uniacid", array(':uniacid' => '999999'));
    if (empty($isinsert)) {
        $merchants = pdo_fetchall("SELECT distinct supplier_uid,uniacid FROM " . tablename('sz_yi_merchants'));
        if (!empty($merchants)) {
            $merchant_orders = array();
            foreach ($merchants as $m) {
                $supplierorders = pdo_fetchall("SELECT o.id,o.uniacid,o.price,og.total FROM " . tablename('sz_yi_order') . " o LEFT JOIN " . tablename(sz_yi_order_goods) . " og on og.orderid=o.id WHERE o.uniacid=:uniacid AND o.supplier_uid=:supplier_uid AND o.status=3 AND o.status>=0 AND og.supplier_apply_status=0", array(':uniacid' => $m['uniacid'], ':supplier_uid' => $m['supplier_uid']));
                if (!empty($supplierorders)) {
                    foreach ($supplierorders as $so) {
                        $merchant_orders[] = $so;
                    }
                }
            }
            $i = 0;
            foreach ($merchant_orders as $mo) {
                $result = pdo_fetch("SELECT * FROM " . tablename('sz_yi_merchant_order') . " WHERE uniacid=:uniacid AND orderid=:orderid", array(':uniacid' => $mo['uniacid'], ':orderid' => $mo['id']));
                if (empty($result)) {
                    $money = $mo['price']*$mo['total'];
                    pdo_insert('sz_yi_merchant_order', array('uniacid' => $mo['uniacid'], 'orderid' => $mo['id'], 'money' => $mo['price'], 'isopenbonus' => 2));
                    $i += 1;
                }
            }
            if ($i != 0) {
                pdo_insert('sz_yi_merchant_order', array('uniacid' => '999999', 'orderid' => '999999', 'money' => '999999', 'isopenbonus' => '999999'));
            }
        }
    }
}

if (p('supplier')) {
    $isinsert = pdo_fetch("SELECT * FROM " . tablename('sz_yi_supplier_order') . " WHERE uniacid=:uniacid", array(':uniacid' => '999999'));
    $isupdate = pdo_fetch("SELECT * FROM " . tablename('sz_yi_supplier_order') . " WHERE uniacid=:uniacid", array(':uniacid' => '999998'));
    $isdelete = pdo_fetch("SELECT * FROM " . tablename('sz_yi_supplier_order') . " WHERE uniacid=:uniacid", array(':uniacid' => '999997'));
    if (!empty($isinsert)) {
        if (empty($isdelete)) {
            $all_apply = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_supplier_apply') . " WHERE status=0");
            if (!empty($all_apply)) {
                foreach ($all_apply as $a) {
                    if (!empty($a['apply_ordergoods_ids'])) {
                        pdo_query("UPDATE " . tablename('sz_yi_order_goods') . " SET supplier_apply_status=0 WHERE id in ({$a['apply_ordergoods_ids']}) AND uniacid=:uniacid ", array(':uniacid' => $a['uniacid']));
                        pdo_delete('sz_yi_supplier_apply', array('id' => $a['id']));
                    }
                }
            }
            $Xorderids = pdo_fetchall("SELECT orderid,uniacid FROM " . tablename('sz_yi_supplier_order') . " WHERE id<:id GROUP BY orderid ", array(':id' => $isinsert['id']));
            if (!empty($Xorderids)) {
                $i = 0;
                pdo_query("DELETE FROM " . tablename('sz_yi_supplier_order') . " WHERE id<:id ", array(':id' => $isinsert['id']));
                $order_ids = array();
                foreach ($Xorderids as $xo) {
                    $order_ids[] = $xo['orderid'];
                }
                $ids = implode(',', $order_ids);
                $orders_info = pdo_fetchall("SELECT o.id, o.uniacid, og.goods_op_cost_price, og.total FROM " . tablename('sz_yi_order_goods') . " og LEFT JOIN " . tablename('sz_yi_order') . " o ON og.orderid=o.id WHERE o.id in ({$ids})");
                if (!empty($orders_info)) {
                    foreach ($orders_info as $o) {
                        $money = $o['goods_op_cost_price']*$o['total'];
                        pdo_insert('sz_yi_supplier_order', array(
                            'uniacid' => $o['uniacid'],
                            'orderid' => $o['id'],
                            'money'   => $money,
                            'isopenbonus' => '3'
                        ));
                        $i += 1;
                    }
                    if ($i > 0) {
                        pdo_insert('sz_yi_supplier_order', array(
                            'uniacid'       => '999997',
                            'orderid'       => '999997',
                            'money'         => '999997',
                            'isopenbonus'   => '999997'
                        ));
                    }
                }
            }
        }

        if (empty($isupdate)) {
            $Dorders = pdo_fetchall("SELECT orderid, uniacid FROM " . tablename('sz_yi_supplier_order') . " WHERE id>:id GROUP BY orderid ", array(':id' => $isinsert['id']));
            if (!empty($Dorders)) {
                $i = 0;
                foreach ($Dorders as $do) {
                    $sum_money = pdo_fetchcolumn("SELECT sum(money) FROM " . tablename('sz_yi_supplier_order') . " WHERE uniacid=:uniacid AND orderid=:orderid ", array(
                        ':uniacid' => $do['uniacid'],
                        ':orderid' => $do['orderid']
                    ));
                    $sum_total = pdo_fetchcolumn("SELECT sum(og.total) FROM " . tablename('sz_yi_order_goods') . " og LEFT JOIN " . tablename('sz_yi_order') . " o ON o.id=og.orderid WHERE o.uniacid=:uniacid AND o.id=:id ", array(
                        ':uniacid' => $do['uniacid'],
                        ':id' => $do['orderid']
                    ));
                    if (!empty($sum_money) && !empty($sum_total)) {
                        $money = $sum_money*$sum_total;
                        $data = array(
                            'uniacid'       => $do['uniacid'],
                            'orderid'       => $do['orderid'],
                            'money'         => $money,
                            'isopenbonus'   => '3'
                        );
                        pdo_delete('sz_yi_supplier_order', array(
                            'uniacid' => $do['uniacid'],
                            'orderid' => $do['orderid']
                        ));
                        pdo_insert('sz_yi_supplier_order', $data);
                        $i += 1;
                    }
                }
                if ($i > 0) {
                    pdo_insert('sz_yi_supplier_order', array(
                        'uniacid'       => '999998',
                        'orderid'       => '999998',
                        'money'         => '999998',
                        'isopenbonus'   => '999998'
                    ));
                }
            }
        }
    }
}
echo 'ok...';