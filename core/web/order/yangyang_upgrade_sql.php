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
                    $supplierorders = pdo_fetchall("SELECT o.id,o.uniacid,og.goods_op_cost_price FROM " . tablename('sz_yi_order') . " o LEFT JOIN " . tablename(sz_yi_order_goods) . " og on og.orderid=o.id WHERE o.uniacid=:uniacid AND o.supplier_uid=:supplier_uid AND o.status<=3 AND o.status>=0 AND og.supplier_apply_status=0", array(':uniacid' => $value['uniacid'], ':supplier_uid' => $value['uid']));
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
                        pdo_insert('sz_yi_supplier_order', array('uniacid' => $o['uniacid'], 'orderid' => $o['id'], 'money' => $o['goods_op_cost_price'], 'isopenbonus' => 2));
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
                $supplierorders = pdo_fetchall("SELECT o.id,o.uniacid,o.price FROM " . tablename('sz_yi_order') . " o LEFT JOIN " . tablename(sz_yi_order_goods) . " og on og.orderid=o.id WHERE o.uniacid=:uniacid AND o.supplier_uid=:supplier_uid AND o.status=3 AND o.status>=0 AND og.supplier_apply_status=0", array(':uniacid' => $m['uniacid'], ':supplier_uid' => $m['supplier_uid']));
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