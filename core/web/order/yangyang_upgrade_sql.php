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