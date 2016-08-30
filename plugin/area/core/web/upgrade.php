<?php
global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'area'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`,`category`)VALUES(". $displayorder .",'area','商品区域','1.0','官方','1','sale');";
  pdo_query($sql);
}
$sql = "

CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_category_area')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) DEFAULT NULL COMMENT '分类名称',
  `thumb` varchar(255) DEFAULT NULL COMMENT '分类图片',
  `parentid` int(11) DEFAULT '0' COMMENT '上级分类ID,0为第一级',
  `isrecommand` int(10) DEFAULT '0',
  `description` varchar(500) DEFAULT NULL COMMENT '分类介绍',
  `displayorder` tinyint(3) unsigned DEFAULT '0' COMMENT '排序',
  `enabled` tinyint(1) DEFAULT '1' COMMENT '是否开启',
  `ishome` tinyint(3) DEFAULT '0',
  `advimg` varchar(255) DEFAULT '',
  `advurl` varchar(500) DEFAULT '', 
  `level` tinyint(3) DEFAULT '0',
  `advimg_pc` varchar(255) DEFAULT NULL,
  `advurl_pc` varchar(500) DEFAULT NULL,
  `supplier_uid` int(11) DEFAULT '0',
  `detail` text DEFAULT NULL,
  `times` int(11) DEFAULT '0',
  `create_time` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_enabled` (`enabled`),
  KEY `idx_parentid` (`parentid`),
  KEY `idx_isrecommand` (`isrecommand`),
  KEY `idx_ishome` (`ishome`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品区域分类';

";
pdo_query($sql);

if(!pdo_fieldexists('sz_yi_goods', 'pcate_area')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `pcate_area` int(11) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_goods', 'ccate_area')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `ccate_area` int(11) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_goods', 'tcate_area')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `tcate_area` int(11) NOT NULL DEFAULT '0';");
}



message('芸众商品区域插件安装成功', $this->createPluginWebUrl('area'), 'success');
