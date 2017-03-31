<?php
global $_W;
load()->func('file');

$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'yunbi'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`,`category`) VALUES(". $displayorder .",'yunbi','云币','1.0','官方','1','sale');";
  pdo_fetchall($sql);
}

//2016-8-15 商品是否返虚拟币  虚拟币返现比例 
if (!pdo_fieldexists('sz_yi_goods', 'yunbi_consumption')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `yunbi_consumption` DECIMAL(5,3) NOT NULL AFTER `isopenchannel`;");
}
if (!pdo_fieldexists('sz_yi_goods', 'isyunbi')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `isyunbi` TINYINT(1) NOT NULL DEFAULT '0' AFTER `yunbi_consumption`;");
}
if (!pdo_fieldexists('sz_yi_goods', 'yunbi_deduct')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `yunbi_deduct` DECIMAL(10,2) NOT NULL AFTER `isyunbi`;");
}

if (!pdo_fieldexists('sz_yi_member', 'virtual_currency')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `virtual_currency` DECIMAL(10,2) NOT NULL AFTER `isagency`;");
}
if (!pdo_fieldexists('sz_yi_member', 'last_money')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `last_money` DECIMAL(10,2) NOT NULL AFTER `virtual_currency`;");
}
if (!pdo_fieldexists('sz_yi_member', 'updatetime')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `updatetime` VARCHAR(255) NOT NULL AFTER `last_money`;");
}
//虚拟币抵扣
if (!pdo_fieldexists('sz_yi_order', 'deductyunbimoney')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `deductyunbimoney` DECIMAL(10,2) NOT NULL AFTER `deductenough`;");
}
if (!pdo_fieldexists('sz_yi_order', 'deductyunbi')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `deductyunbi` DECIMAL(10,2) NOT NULL AFTER `deductyunbimoney`;");
}

if (!pdo_fieldexists('sz_yi_goods', 'isforceyunbi')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `isforceyunbi` TINYINT(1) NOT NULL DEFAULT '0';");
}
pdo_fetchall("CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_yunbi_log') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `credittype` varchar(60) NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `returntype` tinyint(2) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  `remark` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

@rmdirs(IA_ROOT. "/data/tpl/app/sz_yi");

message('云币插件安装成功', $this->createPluginWebUrl('yunbi/set'), 'success');
