<?php
//金额不能用int, apply表少uniacid字段
global $_W;
$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_merchants') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) CHARACTER SET utf8 NOT NULL,
  `uniacid` int(11) NOT NULL,
  `supplier_uid` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `commissions` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_merchant_apply') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `applysn` varchar(255) NOT NULL COMMENT '提现单号',
  `member_id` int(11) NOT NULL,
  `type` tinyint(3) DEFAULT '0' ,
  `money` decimal(10,2) DEFAULT '0.00',
  `status` tinyint(3) DEFAULT '0' ,
  `apply_time` int(11) NOT NULL,
  `finish_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_merchant_level') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `level_name` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '等级名称',
  `commission` decimal(10,2) DEFAULT '0.00' COMMENT '比例',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_merchant_center') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `openid` varchar(255) CHARACTER SET utf8 NOT NULL,
  `realname` varchar(50) CHARACTER SET utf8 NOT NULL,
  `mobile` varchar(50) CHARACTER SET utf8 NOT NULL,
  `level_id` int(11) NOT NULL,
  `center_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_merchant_order') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `orderid` int(11) NOT NULL,
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '金额',
  `isopenbonus` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
pdo_query($sql);
$info = pdo_fetch('select * from ' . tablename('sz_yi_plugin') . ' where identity= "merchant"  order by id desc limit 1');
if (!pdo_fieldexists('sz_yi_order', 'merchant_apply_status')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD     `merchant_apply_status` tinyint(3) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_merchants', 'center_id')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_merchants')." ADD `center_id` int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_merchant_apply', 'iscenter')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_merchant_apply')." ADD `iscenter` int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_order', 'center_apply_status')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `center_apply_status` tinyint(3) DEFAULT '0';");
}
if (pdo_fieldexists('sz_yi_merchant_level', 'commission')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_merchant_level')." MODIFY `commission` decimal(10,2) DEFAULT '0.00' COMMENT '比例';");
}
if(!$info){
    $sql = "INSERT INTO " . tablename('sz_yi_plugin'). " (`displayorder`, `identity`, `name`, `version`, `author`, `status`, `category`) VALUES(0, 'merchant', '招商员', '1.0', '官方', 1, 'biz');";
    pdo_query($sql);
}
message('芸众招商员插件安装成功', $this->createPluginWebUrl('merchant/merchants'), 'success');
