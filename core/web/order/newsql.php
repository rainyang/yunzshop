<?php
/*=============================================================================
#     FileName: 1.4.2.php
#         Desc:
#       Author: RainYang - https://github.com/rainyang
#        Email: rainyang2012@qq.com
#     HomePage: http://rainyang.github.io
#      Version: 0.0.1
#   LastChange: 2016-03-29 19:28:39
#      History:
=============================================================================*/

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_exhelper_express') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '单据分类 1为快递单 2为发货单',
  `expressname` varchar(255) DEFAULT '',
  `expresscom` varchar(255) NOT NULL DEFAULT '',
  `express` varchar(255) NOT NULL DEFAULT '',
  `width` decimal(10,2) DEFAULT '0.00',
  `datas` text,
  `height` decimal(10,2) DEFAULT '0.00',
  `bg` varchar(255) DEFAULT '',
  `isdefault` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_isdefault` (`isdefault`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_category2')." (
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
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_enabled` (`enabled`),
  KEY `idx_parentid` (`parentid`),
  KEY `idx_isrecommand` (`isrecommand`),
  KEY `idx_ishome` (`ishome`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_exhelper_senduser')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `sendername` varchar(255) DEFAULT '' COMMENT '发件人',
  `sendertel` varchar(255) DEFAULT '' COMMENT '发件人联系电话',
  `sendersign` varchar(255) DEFAULT '' COMMENT '发件人签名',
  `sendercode` int(11) DEFAULT NULL COMMENT '发件地址邮编',
  `senderaddress` varchar(255) DEFAULT '' COMMENT '发件地址',
  `sendercity` varchar(255) DEFAULT NULL COMMENT '发件城市',
  `isdefault` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_isdefault` (`isdefault`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_exhelper_sys')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL DEFAULT 'localhost',
  `port` int(11) NOT NULL DEFAULT '8000',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS  ".tablename('sz_yi_express')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `express_name` varchar(50) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `express_price` varchar(10) DEFAULT '',
  `express_area` varchar(100) DEFAULT '',
  `express_url` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_diyform_category'). " (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`uniacid`  int(11) NULL DEFAULT 0 COMMENT '所属帐号' ,
`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '分类名称' ,
PRIMARY KEY (`id`),
INDEX `idx_uniacid` (`uniacid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_diyform_data'). " (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`uniacid`  int(11) NOT NULL DEFAULT 0 ,
`typeid`  int(11) NOT NULL DEFAULT 0 COMMENT '类型id' ,
`cid`  int(11) NULL DEFAULT 0 COMMENT '关联id' ,
`diyformfields`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`fields`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '字符集' ,
`openid`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '使用者openid' ,
`type`  tinyint(2) NULL DEFAULT 0 COMMENT '该数据所属模块' ,
PRIMARY KEY (`id`),
INDEX `idx_uniacid` (`uniacid`) USING BTREE ,
INDEX `idx_typeid` (`typeid`) USING BTREE ,
INDEX `idx_cid` (`cid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_diyform_temp'). " (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`uniacid`  int(11) NOT NULL DEFAULT 0 ,
`typeid`  int(11) NULL DEFAULT 0 ,
`cid`  int(11) NOT NULL DEFAULT 0 COMMENT '关联id' ,
`diyformfields`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`fields`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '字符集' ,
`openid`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '使用者openid' ,
`type`  tinyint(1) NULL DEFAULT 0 COMMENT '类型' ,
`diyformid`  int(11) NULL DEFAULT 0 ,
`diyformdata`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
PRIMARY KEY (`id`),
INDEX `idx_uniacid` (`uniacid`) USING BTREE ,
INDEX `idx_cid` (`cid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_diyform_type'). " (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`uniacid`  int(11) NOT NULL DEFAULT 0 ,
`cate`  int(11) NULL DEFAULT 0 ,
`title`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分类名称' ,
`fields`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '字段集' ,
`usedata`  int(11) NOT NULL DEFAULT 0 COMMENT '已用数据' ,
`alldata`  int(11) NOT NULL DEFAULT 0 COMMENT '全部数据' ,
`status`  tinyint(1) NULL DEFAULT 1 COMMENT '状态' ,
PRIMARY KEY (`id`),
INDEX `idx_uniacid` (`uniacid`) USING BTREE ,
INDEX `idx_cate` (`cate`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
";
pdo_fetchall($sql);

pdo_fetchall("ALTER TABLE  ".tablename('sz_yi_member')." CHANGE  `pwd`  `pwd` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");

if (!pdo_fieldexists('sz_yi_goods', 'cates')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD     `cates` text;");
}

if (!pdo_fieldexists('sz_yi_goods', 'diyformtype')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `diyformtype` tinyint(3) DEFAULT '0';");
}

if (!pdo_fieldexists('sz_yi_goods', 'manydeduct')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `manydeduct` tinyint(1) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_goods', 'dispatchtype')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `dispatchtype` tinyint(1) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_goods', 'dispatchid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `dispatchid` int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_goods', 'dispatchprice')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `dispatchprice`  decimal(10,2) DEFAULT '0.00';");
}



if (!pdo_fieldexists('sz_yi_goods', 'diyformid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `diyformid` int(11) DEFAULT '0';");
}

if (!pdo_fieldexists('sz_yi_goods', 'diymode')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `diymode` tinyint(3) DEFAULT '0';");
}

//pdo_fetchall("UPDATE ".tablename('qrcode')." SET `name` = 'SZ_YI_POSTER_QRCODE', `keyword`='SZ_YI_POSTER' WHERE `keyword` = 'EWEI_SHOP_POSTER'");

if (!pdo_fieldexists('sz_yi_member', 'regtype')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `regtype` tinyint(3) DEFAULT '1';");
}
if (!pdo_fieldexists('sz_yi_member', 'isbindmobile')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `isbindmobile` tinyint(3) DEFAULT '0';");
}

if (!pdo_fieldexists('sz_yi_member', 'isjumpbind')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `isjumpbind` tinyint(3) DEFAULT '0';");
}
//diy
if (!pdo_fieldexists('sz_yi_store', 'realname')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD `realname` varchar(255) DEFAULT '';");
}
if (!pdo_fieldexists('sz_yi_store', 'mobile')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD `mobile` varchar(255) DEFAULT '';");
}
if (!pdo_fieldexists('sz_yi_store', 'fetchtime')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD `fetchtime` varchar(255) DEFAULT '';");
}
if (!pdo_fieldexists('sz_yi_store', 'type')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD `type` tinyint(1) DEFAULT '0';");
}

if (!pdo_fieldexists('sz_yi_member', 'diymemberid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `diymemberid` int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_member', 'isblack')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `isblack` tinyint(3) DEFAULT '0';");
}

if (!pdo_fieldexists('sz_yi_member', 'diymemberdataid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `diymemberdataid` int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_member', 'diycommissionid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `diycommissionid` int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_member', 'diycommissiondataid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `diycommissiondataid` int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_member', 'diymemberfields')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `diymemberfields` text NULL;");
}
if (!pdo_fieldexists('sz_yi_member', 'diymemberdata')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `diymemberdata` text NULL;");
}
if (!pdo_fieldexists('sz_yi_member', 'diycommissionfields')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `diycommissionfields` text NULL;");
}
if(!pdo_fieldexists('sz_yi_member', 'diycommissiondata')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `diycommissiondata` text NULL;");
}
if(!pdo_fieldexists('sz_yi_member_cart', 'diyformdata')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_cart')." ADD    `diyformdata` text NULL;");
}
if(!pdo_fieldexists('sz_yi_member_cart', 'diyformfields')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_cart')." ADD    `diyformfields` text NULL;");
}
if(!pdo_fieldexists('sz_yi_member_cart', 'diyformdataid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_cart')." ADD    `diyformdataid` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_member_cart', 'diyformid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_cart')." ADD    `diyformid` int(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'diyformid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD    `diyformid` int(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order_goods', 'openid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD    `openid` varchar(255) DEFAULT '';");
}

if(!pdo_fieldexists('sz_yi_order', 'storeid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD    `storeid` int(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'diyformid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD    `diyformid` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_order', 'diyformdata')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD    `diyformdata` text NULL;");
}
if(!pdo_fieldexists('sz_yi_order', 'diyformfields')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD    `diyformfields` text NULL;");
}

if(!pdo_fieldexists('sz_yi_order_goods', 'diyformdataid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD    `diyformdataid` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_order_goods', 'diyformid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD    `diyformid` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_order_goods', 'diyformdata')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD    `diyformdata` text NULL;");
}
if(!pdo_fieldexists('sz_yi_order_goods', 'diyformfields')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD    `diyformfields` text NULL;");
}

$info = pdo_fetch('select * from ' . tablename('sz_yi_plugin') . ' where identity= "exhelper"  order by id desc limit 1');

if(!$info){
    $sql = "INSERT INTO " . tablename('sz_yi_plugin'). " (`displayorder`, `identity`, `name`, `version`, `author`, `status`, `category`) VALUES(0, 'exhelper', '快递助手', '1.0', '官方', 1, 'tool');";
    pdo_fetchall($sql);
}

$info = pdo_fetch('select * from ' . tablename('sz_yi_plugin') . ' where identity= "yunpay"  order by id desc limit 1');

if(!$info){
    $sql = "INSERT INTO " . tablename('sz_yi_plugin'). " (`displayorder`, `identity`, `name`, `version`, `author`, `status`, `category`) VALUES(0, 'yunpay', '云支付', '1.0', '云支付', 1, 'tool');";
    pdo_fetchall($sql);
}

$info = pdo_fetch('select * from ' . tablename('sz_yi_plugin') . ' where identity= "supplier"  order by id desc limit 1');

if(!$info){
    $sql = "INSERT INTO " . tablename('sz_yi_plugin'). " (`displayorder`, `identity`, `name`, `version`, `author`, `status`, `category`) VALUES(0, 'supplier', '供应商', '1.0', '官方', 1, 'biz');";
    pdo_fetchall($sql);
}

$info = pdo_fetch('select * from ' . tablename('sz_yi_plugin') . ' where identity= "diyform"  order by id desc limit 1');

if(!$info){
    $sql = "INSERT INTO " . tablename('sz_yi_plugin'). " (`displayorder`, `identity`, `name`, `version`, `author`, `status`, `category`) VALUES(0, 'diyform', '自定义表单', '1.0', '官方', 1, 'help');";
    pdo_fetchall($sql);
}

$info = pdo_fetch('select * from ' . tablename('sz_yi_plugin') . ' where identity= "system"  order by id desc limit 1');

if(!$info){
    $sql = "INSERT INTO " . tablename('sz_yi_plugin'). " (`displayorder`, `identity`, `name`, `version`, `author`, `status`, `category`) VALUES(0, 'system', '系统工具', '1.0', '官方', 1, 'help');";
    pdo_fetchall($sql);
}
else{
    $sql = "update " . tablename('sz_yi_plugin'). " set `name` = '系统工具' where `identity` = 'system';";
    pdo_fetchall($sql);
}

if(!pdo_fieldexists('sz_yi_goods', 'shorttitle')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD  `shorttitle`  VARCHAR( 500 ) DEFAULT NULL;");
}

if(!pdo_fieldexists('sz_yi_goods', 'commission_level_id')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD  `commission_level_id`  int(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'printstate')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD  `printstate`  tinyint(3) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'printstate2')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD  `printstate2`  tinyint(3) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order_goods', 'printstate')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD  `printstate`  tinyint(3) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order_goods', 'printstate2')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD  `printstate2`  tinyint(3) DEFAULT '0';");
}

//运费
if(!pdo_fieldexists('sz_yi_dispatch', 'isdefault')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_dispatch')." ADD  `isdefault`  tinyint(1) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_dispatch', 'calculatetype')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_dispatch')." ADD  `calculatetype`  tinyint(1) DEFAULT '0';");
}

//计件
if(!pdo_fieldexists('sz_yi_dispatch', 'firstnumprice')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_dispatch')." ADD  `firstnumprice`  decimal(10,2) DEFAULT '0.00';");
}
if(!pdo_fieldexists('sz_yi_dispatch', 'secondnumprice')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_dispatch')." ADD  `secondnumprice`  decimal(10,2) DEFAULT '0.00';");
}
if(!pdo_fieldexists('sz_yi_dispatch', 'firstnum')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_dispatch')." ADD  `firstnum`  int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_dispatch', 'secondnum')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_dispatch')." ADD  `secondnum`  int(11) DEFAULT '0';");
}
//文章营销
if(!pdo_fieldexists('sz_yi_article_sys', 'article_area')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article_sys')." ADD  `article_area`  TEXT NULL COMMENT '文章阅读地区';");
}
if(!pdo_fieldexists('sz_yi_article', 'article_rule_money_total')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article')." ADD  `article_rule_money_total`  DECIMAL( 10, 2 ) NOT NULL DEFAULT '0' COMMENT '最高累计奖金' AFTER `article_rule_money`;");
}
if(!pdo_fieldexists('sz_yi_article', 'article_rule_userd_money')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article')." ADD  `article_rule_userd_money` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0' COMMENT '截止目前累计奖励金额' AFTER`article_rule_money_total`");
}

//供应商增加
if(pdo_tableexists('sz_yi_af_supplier')){
    if(!pdo_fieldexists('sz_yi_af_supplier', 'status')) {
      pdo_fetchall("ALTER TABLE ".tablename('sz_yi_af_supplier')." ADD `status` TINYINT( 3 ) NOT NULL COMMENT '0申请1驳回2通过' AFTER `productname`;");
    }
}

//供应商提现字段int不对要改
if(pdo_tableexists('sz_yi_supplier_apply')){
    if(!pdo_fieldexists('sz_yi_supplier_apply', 'apply_money')) {
      pdo_fetchall("ALTER TABLE ".tablename('sz_yi_supplier_apply')." ADD `apply_money` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' COMMENT '申请提现金额';");
    }
    if(pdo_fieldexists('sz_yi_supplier_apply', 'apply_money')) {
      pdo_fetchall("ALTER TABLE ".tablename('sz_yi_supplier_apply')." CHANGE `apply_money` `apply_money` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' COMMENT '申请提现金额';");
    }
}

if(!pdo_fieldexists('sz_yi_adv', 'thumb_pc')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_adv')." ADD `thumb_pc` VARCHAR( 255 ) DEFAULT '';");
}

if(!pdo_fieldexists('sz_yi_notice', 'desc')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_notice')." ADD `desc` VARCHAR( 255 ) DEFAULT '';");
}

if(!pdo_fieldexists('sz_yi_order_goods', 'goods_op_cost_price')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD `goods_op_cost_price` DECIMAL(10,2) DEFAULT '0.00';");
}

if(!pdo_fieldexists('sz_yi_store', 'myself_support')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD `myself_support` tinyint(1) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_store', 'verity_support')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD `verity_support` tinyint(1) DEFAULT '0';");
}


//分红插件增加等级独立消息
if(pdo_tableexists('sz_yi_bonus_level')){
  if(!pdo_fieldexists('sz_yi_bonus_level', 'msgtitle')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_bonus_level')." ADD `msgtitle` varchar(100) DEFAULT '';");
  }

  if(!pdo_fieldexists('sz_yi_bonus_level', 'msgcontent')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_bonus_level')." ADD `msgcontent` varchar(255) DEFAULT '';");
  }

  if(!pdo_fieldexists('sz_yi_bonus_level', 'status')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_bonus_level')." ADD `status` tinyint(1) DEFAULT '0';");
  }
}
if(pdo_tableexists('sz_yi_bonus')){
  if(!pdo_fieldexists('sz_yi_bonus', 'sendmonth')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_bonus')." ADD `sendmonth` tinyint(1) DEFAULT '0';");
  }
}
pdo_fetchall("CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_adpc') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0',
  `thumb_pc` varchar(255) DEFAULT '',
  `location` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

if(!pdo_fieldexists('sz_yi_perm_user', 'username')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `username` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
} 
if(!pdo_fieldexists('sz_yi_perm_user', 'password')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `password` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
}

if(!pdo_fieldexists('sz_yi_exhelper_express', 'uid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_exhelper_express')." ADD  `uid`  INT(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_exhelper_senduser', 'uid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_exhelper_senduser')." ADD  `uid`  INT(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_exhelper_sys', 'uid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_exhelper_sys')." ADD  `uid`  INT(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_af_supplier', 'username')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_af_supplier')." ADD `username` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
}
if(!pdo_fieldexists('sz_yi_af_supplier', 'password')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_af_supplier')." ADD `password` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
}

if(!pdo_fieldexists('sz_yi_goods', 'redprice')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `redprice` varchar(50) DEFAULT '';");
}

if(!pdo_fieldexists('sz_yi_goods_option', 'redprice')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods_option')." ADD `redprice` varchar(50) DEFAULT '';");
}

if(!pdo_fieldexists('sz_yi_order', 'redprice')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `redprice` varchar(50) DEFAULT '';");
}

if(!pdo_fieldexists('sz_yi_order', 'refundstate')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD  `refundstate` tinyint(3) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'applyprice')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `applyprice`  DECIMAL(10,2) DEFAULT '0.00';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'orderprice')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `orderprice`  DECIMAL(10,2) DEFAULT '0.00';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'rtype')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `rtype` tinyint(1) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'imgs')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `imgs` text DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'refundtime')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `refundtime` INT(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'refundaddress')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `refundaddress` text DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'message')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `message` text DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'express')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `express` varchar(100) DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'expresscom')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `expresscom` varchar(100) DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'expresssn')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `expresssn` varchar(100) DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'operatetime')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `operatetime` INT(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'sendtime')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `sendtime` INT(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'returntime')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `returntime` INT(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'rexpress')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `rexpress` varchar(100) DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'rexpresscom')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `rexpresscom` varchar(100) DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'rexpresssn')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `rexpresssn` varchar(100) DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'refundaddressid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `refundaddressid` INT(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_order_refund', 'endtime')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_refund')." ADD  `endtime` INT(11) DEFAULT '0';");
}
pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_refund_address'). " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `title` varchar(20) DEFAULT '',
  `name` varchar(20) DEFAULT '',
  `tel` varchar(20) DEFAULT '',
  `mobile` varchar(11) DEFAULT '',
  `province` varchar(30) DEFAULT '',
  `city` varchar(30) DEFAULT '',
  `area` varchar(30) DEFAULT '',
  `address` varchar(300) DEFAULT '',
  `isdefault` tinyint(1) DEFAULT '0',
  `zipcode` varchar(255) DEFAULT '',
  `content` text,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

if (!pdo_fieldexists('sz_yi_member', 'referralsn')) {
    pdo_fetchall("ALTER TABLE  ".tablename('sz_yi_member')." ADD  `referralsn` VARCHAR( 255 ) NOT NULL");
}

if (!pdo_fieldexists('sz_yi_article_sys', 'article_text')) {
    pdo_fetchall("ALTER TABLE  ".tablename('sz_yi_article_sys')." ADD  `article_text` VARCHAR( 255 ) NOT NULL AFTER  `article_keyword`");
}
if (!pdo_fieldexists('sz_yi_article_sys', 'isarticle')) {
    pdo_fetchall("ALTER TABLE  ".tablename('sz_yi_article_sys')." ADD  `isarticle` TINYINT( 1 ) NOT NULL");
}

if (!pdo_fieldexists('sz_yi_goods', 'pcate1')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `pcate1` int(11) DEFAULT '0';");
}

if (!pdo_fieldexists('sz_yi_goods', 'ccate1')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `ccate1` int(11) DEFAULT '0';");
}

if (!pdo_fieldexists('sz_yi_goods', 'tcate1')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `tcate1` int(11) DEFAULT '0';");
}

pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_system_copyright'). " (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`uniacid`  int(11) NULL DEFAULT NULL ,
`copyright`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`bgcolor`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
PRIMARY KEY (`id`),
INDEX `idx_uniacid` (`uniacid`) USING BTREE 
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;");

if(!pdo_fieldexists('sz_yi_article_category', 'm_level')) {
pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article_category')." ADD `m_level` INT(11) NOT NULL DEFAULT '0'");
}
if(!pdo_fieldexists('sz_yi_article_category', 'd_level')) {
pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article_category')." ADD `d_level` INT(11) NOT NULL DEFAULT '0'");
}

if(!pdo_fieldexists('sz_yi_order', 'redstatus')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `redstatus` varchar(100) DEFAULT '';");
}

if (!pdo_fieldexists('sz_yi_goods', 'nobonus')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `nobonus` tinyint(1) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_goods', 'returns')) {
pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `returns` TEXT NOT NULL AFTER `discounts`");
}

//添加全返记录表 2016-06-14
pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_return_log')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `returntype` tinyint(2) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL, 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

if (!pdo_fieldexists('sz_yi_coupon', 'supplier_uid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `supplier_uid` INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'cashier')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `cashier` tinyint(1) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'realprice')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `realprice` decimal(10,2) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'deredpack')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `deredpack` tinyint(1) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'decommission')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `decommission` tinyint(1) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'decredits')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `decredits` tinyint(1) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'cashierid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `cashierid` int(11) DEFAULT '0';");
}

//过几天要删掉，临时处理全返表无自增问题
if(pdo_tableexists('sz_yi_return_log')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_return_log')." DROP `id`;");
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_return_log')." ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);");
}


//添加全返记录表 2016-06-14
pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_return_log')." (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `returntype` tinyint(2) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

//app 首页banner表 2016-6-21
pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_banner')." (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0',
  `thumb_pc` varchar(500) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

//app 客户订单推送消息表 2016-6-21
pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_message')." (
   `id` int(11) NOT NULL COMMENT '编号',
  `openid` varchar(255) NOT NULL COMMENT '用户openid',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `contents` text NOT NULL COMMENT '内容',
  `status` set('0','1') NOT NULL DEFAULT '0' COMMENT '0-未读；1-已读',
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");


//app 系统推送消息表 2016-6-21
pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_push')." (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(50) DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `content` text,
  `time` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

//会员表 增加app绑定字段
if(!pdo_fieldexists('sz_yi_member', 'bindapp')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `bindapp` tinyint(4) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'ordersn_general')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `ordersn_general` varchar(255) NOT NULL DEFAULT '';");
}
//前台下单 判断是否支持配送核销字段
if(!pdo_fieldexists('sz_yi_goods', 'isverifysend')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `isverifysend` tinyint(1) NOT NULL DEFAULT '0';");
}
