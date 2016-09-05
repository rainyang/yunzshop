<?php
//收银台添加会员中心是否可以编辑的字段
if(!pdo_fieldexists('sz_yi_cashier_store', 'centercan')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_cashier_store')." ADD    `centercan` tinyint(1) DEFAULT '1';");
}
//优惠券指定供应商新加字段
if(!pdo_fieldexists('sz_yi_coupon', 'getsupplier')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD    `getsupplier` tinyint(1) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_coupon', 'supplierids')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD    `supplierids` text(0) DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_coupon', 'suppliernames')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD    `suppliernames` text(0) DEFAULT '';");
}