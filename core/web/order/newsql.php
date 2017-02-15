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
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_member_log') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `type` tinyint(3) DEFAULT NULL COMMENT '0 充值 1 提现',
  `logno` varchar(255) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `createtime` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0' COMMENT '0 生成 1 成功 2 失败',
  `money` decimal(10,2) DEFAULT '0.00',
  `rechargetype` varchar(255) DEFAULT '' COMMENT '充值类型',
  `gives` decimal(10,2) DEFAULT NULL,
  `couponid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_type` (`type`),
  KEY `idx_createtime` (`createtime`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_fetchall($sql);

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

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_hotel_room') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `goodsid` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `oprice` decimal(10,2) DEFAULT '0.00',
  `cprice`  decimal(10,2) DEFAULT '0.00',
  `deposit` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_book') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `mobile` varchar(30) DEFAULT '',
  `time` varchar(255) DEFAULT '',
  `contact` text,
  `goods` int(11) DEFAULT '0',
  `message` text,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` int(11) DEFAULT '0',
  `status` int(1) DEFAULT '0',
  `delete` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_print_list') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(45) DEFAULT '',
  `key` varchar(30) DEFAULT '',
  `print_no` varchar(30) DEFAULT '',
  `type` int(1) DEFAULT '0',
  `status` int(3) DEFAULT '0',
  `member_code` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='酒店房间价格表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_hotel_room_price') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roomid` int(11) DEFAULT '0',
  `roomdate` int(11) DEFAULT '0',
  `thisdate` varchar(255) DEFAULT '',
  `oprice`  decimal(10,2) DEFAULT '0.00',
  `cprice`  decimal(10,2) DEFAULT '0.00',
  `mprice`  decimal(10,2) DEFAULT '0.00',
  `num` varchar(255) DEFAULT '',
  `status` int(11) DEFAULT '0',

  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_order_room') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` int(11) DEFAULT '0',
  `roomdate` int(11) DEFAULT '0',
  `thisdate` varchar(255) DEFAULT '',
  `oprice` decimal(10,2) DEFAULT '0.00',
  `cprice` decimal(10,2) DEFAULT '0.00',
  `mprice` decimal(10,2) DEFAULT '0.00',
  `roomid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_book') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `mobile` varchar(30) DEFAULT '',
  `time` varchar(255) DEFAULT '',
  `contact` text,
  `goods` int(11) DEFAULT '0',
  `message` text,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` int(11) DEFAULT '0',
  `status` int(1) DEFAULT '0',
  `delete` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_print_list') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(45) DEFAULT '',
  `key` varchar(30) DEFAULT '',
  `print_no` varchar(30) DEFAULT '',
  `type` int(1) DEFAULT '0',
  `status` int(3) DEFAULT '0',
  `member_code` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='酒店房间价格表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_coupon') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `catid` int(11) DEFAULT '0',
  `couponname` varchar(255) DEFAULT '',
  `gettype` tinyint(3) DEFAULT '0',
  `getmax` int(11) DEFAULT '0',
  `usetype` tinyint(3) DEFAULT '0' COMMENT '消费方式 0 付款使用 1 下单使用',
  `returntype` tinyint(3) DEFAULT '0' COMMENT '退回方式 0 不可退回 1 取消订单(未付款) 2.退款可以退回',
  `bgcolor` varchar(255) DEFAULT '',
  `enough` decimal(10,2) DEFAULT '0.00',
  `timelimit` tinyint(3) DEFAULT '0' COMMENT '0 领取后几天有效 1 时间范围',
  `coupontype` tinyint(3) DEFAULT '0' COMMENT '0 优惠券 1 充值券',
  `timedays` int(11) DEFAULT '0',
  `timestart` int(11) DEFAULT '0',
  `timeend` int(11) DEFAULT '0',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '折扣',
  `deduct` decimal(10,2) DEFAULT '0.00' COMMENT '抵扣',
  `backtype` tinyint(3) DEFAULT '0',
  `backmoney` varchar(50) DEFAULT '' COMMENT '返现',
  `backcredit` varchar(50) DEFAULT '' COMMENT '返积分',
  `backredpack` varchar(50) DEFAULT '',
  `backwhen` tinyint(3) DEFAULT '0',
  `thumb` varchar(255) DEFAULT '',
  `desc` text,
  `createtime` int(11) DEFAULT '0',
  `total` int(11) DEFAULT '0' COMMENT '数量 -1 不限制',
  `status` tinyint(3) DEFAULT '0' COMMENT '可用',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '购买价格',
  `respdesc` text COMMENT '推送描述',
  `respthumb` varchar(255) DEFAULT '' COMMENT '推送图片',
  `resptitle` varchar(255) DEFAULT '' COMMENT '推送标题',
  `respurl` varchar(255) DEFAULT '',
  `credit` int(11) DEFAULT '0',
  `usecredit2` tinyint(3) DEFAULT '0',
  `remark` varchar(1000) DEFAULT '',
  `descnoset` tinyint(3) DEFAULT '0',
  `pwdkey` varchar(255) DEFAULT '',
  `pwdsuc` text,
  `pwdfail` text,
  `pwdurl` varchar(255) DEFAULT '',
  `pwdask` text,
  `pwdstatus` tinyint(3) DEFAULT '0',
  `pwdtimes` int(11) DEFAULT '0',
  `pwdfull` text,
  `pwdwords` text,
  `pwdopen` tinyint(3) DEFAULT '0',
  `pwdown` text,
  `pwdexit` varchar(255) DEFAULT '',
  `pwdexitstr` text,
  `displayorder` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_coupontype` (`coupontype`),
  KEY `idx_timestart` (`timestart`),
  KEY `idx_timeend` (`timeend`),
  KEY `idx_timelimit` (`timelimit`),
  KEY `idx_status` (`status`),
  KEY `idx_givetype` (`backtype`),
  KEY `idx_catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_coupon_category') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_coupon_data') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `couponid` int(11) DEFAULT '0',
  `gettype` tinyint(3) DEFAULT '0' COMMENT '获取方式 0 发放 1 领取 2 积分商城',
  `used` int(11) DEFAULT '0',
  `usetime` int(11) DEFAULT '0',
  `gettime` int(11) DEFAULT '0' COMMENT '获取时间',
  `senduid` int(11) DEFAULT '0',
  `ordersn` varchar(255) DEFAULT '',
  `back` tinyint(3) DEFAULT '0',
  `backtime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_couponid` (`couponid`),
  KEY `idx_gettype` (`gettype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_coupon_guess') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `couponid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `times` int(11) DEFAULT '0',
  `pwdkey` varchar(255) DEFAULT '',
  `ok` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_couponid` (`couponid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_coupon_log') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `logno` varchar(255) DEFAULT '',
  `openid` varchar(255) DEFAULT '',
  `couponid` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `paystatus` tinyint(3) DEFAULT '0',
  `creditstatus` tinyint(3) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `paytype` tinyint(3) DEFAULT '0',
  `getfrom` tinyint(3) DEFAULT '0' COMMENT '0 发放 1 中心 2 积分兑换',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_couponid` (`couponid`),
  KEY `idx_status` (`status`),
  KEY `idx_paystatus` (`paystatus`),
  KEY `idx_createtime` (`createtime`),
  KEY `idx_getfrom` (`getfrom`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_member_aging_rechange') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `paymethod` tinyint(1) DEFAULT '0',
  `sendmonth` tinyint(1) DEFAULT '0',
  `sendtime` tinyint(2) DEFAULT '0',
  `ratio` decimal(10,2) DEFAULT '0.00',
  `num` decimal(10,2) DEFAULT '0.00',
  `qnum` int(11) DEFAULT '0',
  `phase` int(11) DEFAULT '0',
  `qtotal` decimal(10,2) DEFAULT '0.00',
  `sendpaytime` int(11) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_openid` (`openid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


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
INDEX `idx_uniacid` USING BTREE (`uniacid`) 
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
INDEX `idx_uniacid` USING BTREE (`uniacid`),
INDEX `idx_typeid` USING BTREE (`typeid`) ,
INDEX `idx_cid` USING BTREE (`cid`)
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
INDEX `idx_uniacid` USING BTREE (`uniacid`) ,
INDEX `idx_cid` USING BTREE (`cid`)
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
INDEX `idx_uniacid` USING BTREE (`uniacid`) ,
INDEX `idx_cate` USING BTREE (`cate`)
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
if (!pdo_fieldexists('sz_yi_goods', 'deduct2')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `deduct2`  decimal(10,2) DEFAULT '0.00';");
}
if (!pdo_fieldexists('sz_yi_goods', 'edmoney')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `edmoney`  decimal(10,2) DEFAULT '0.00';");
}
if (!pdo_fieldexists('sz_yi_goods', 'ednum')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `ednum` int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_goods', 'edareas')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `edareas` text DEFAULT '';");
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
if (!pdo_fieldexists('sz_yi_order', 'couponprice')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `couponprice`  decimal(10,2) DEFAULT '0.00';");
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
if(!pdo_fieldexists('sz_yi_order', 'couponid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD    `couponid` int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_order', 'couponprice')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `couponprice`  decimal(10,2) DEFAULT '0.00';");
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
if(!pdo_fieldexists('sz_yi_dispatch', 'supplier_uid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_dispatch')." ADD  `supplier_uid`  int(11) DEFAULT '0';");
}
//文章营销
if(!pdo_fieldexists('sz_yi_article_sys', 'article_area')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article_sys')." ADD  `article_area`  TEXT NULL COMMENT '文章阅读地区';");
}
if(!pdo_fieldexists('sz_yi_article', 'article_rule_money_total')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article')." ADD  `article_rule_money_total`  DECIMAL( 10, 2 ) NOT NULL DEFAULT '0' COMMENT '最高累计奖金' AFTER `article_rule_money`;");
}
if(!pdo_fieldexists('sz_yi_article', 'article_rule_userd_money')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article')." ADD  `article_rule_userd_money` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0' COMMENT '截止目前累计奖励金额' ");
}

//供应商增加
if(pdo_tableexists('sz_yi_af_supplier')){
    if(!pdo_fieldexists('sz_yi_af_supplier', 'status')) {
      pdo_fetchall("ALTER TABLE ".tablename('sz_yi_af_supplier')." ADD `status` TINYINT( 3 ) NOT NULL COMMENT '0申请1驳回2通过' AFTER `productname`;");
    }
}

//供应商提现字段int不对要改
if(pdo_tableexists('sz_yi_supplier_apply')){
    if(!pdo_fieldexists('sz_yi_perm_role', 'status1')) {
        pdo_query("ALTER TABLE ".tablename('sz_yi_perm_role')." ADD `status1` tinyint(3) NOT NULL COMMENT '1：供应商开启';");
    }
    if(!pdo_fieldexists('sz_yi_perm_user', 'status1')) {
        pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `status1` tinyint(3) NOT NULL COMMENT '1：供应商开启';");
    }
    if(!pdo_fieldexists('sz_yi_perm_user', 'openid')) {
        pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `openid` VARCHAR( 255 ) NOT NULL;");
    }
    if(!pdo_fieldexists('sz_yi_perm_user', 'username')) {
        pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `username` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
    }
    if(!pdo_fieldexists('sz_yi_perm_user', 'password')) {
        pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `username` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
    }
    //添加供应商品牌名称
    if(!pdo_fieldexists('sz_yi_perm_user', 'brandname')) {
        pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `brandname` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
    }
    if(!pdo_fieldexists('sz_yi_supplier_apply', 'apply_money')) {
      pdo_fetchall("ALTER TABLE ".tablename('sz_yi_supplier_apply')." ADD `apply_money` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' COMMENT '申请提现金额';");
    }
    if(pdo_fieldexists('sz_yi_supplier_apply', 'apply_money')) {
      pdo_fetchall("ALTER TABLE ".tablename('sz_yi_supplier_apply')." CHANGE `apply_money` `apply_money` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' COMMENT '申请提现金额';");
    }
    if(!pdo_fieldexists('sz_yi_supplier_apply', 'uniacid')) {
      pdo_fetchall("ALTER TABLE ".tablename('sz_yi_supplier_apply')." ADD `uniacid` int(11) NOT NULL DEFAULT '0';");
    }
    //供应商分账号uniacid
    $suppliers = pdo_fetchall("select uniacid,uid from " . tablename('sz_yi_perm_user') . " where status=1 and roleid=(select id from " . tablename('sz_yi_perm_role') . " where status=1 and status1=1 )");
    if (!empty($suppliers)) {
      foreach ($suppliers as $value) {
        $now_sup_apply_ids = pdo_fetchall("select id from " . tablename('sz_yi_supplier_apply') . " where uid={$value['uid']}");
        if (!empty($now_sup_apply_ids)) {
          foreach ($now_sup_apply_ids as $val) {
            pdo_update('sz_yi_supplier_apply', array('uniacid' => $value['uniacid']), array('id' => $val['id']));
          }
        }
      }
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
INDEX `idx_uniacid` USING BTREE (`uniacid`) 
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
pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `returns` TEXT DEFAULT '';");
}

//添加全返记录表 2016-06-14
pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_return_log'). " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `mid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `money` decimal(10,2) DEFAULT '0.00',
  `status` tinyint(2) DEFAULT '0',
  `returntype` tinyint(2) DEFAULT '0',
  `create_time` int(11) DEFAULT '0', 
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

if (!pdo_fieldexists('sz_yi_coupon', 'supplier_uid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `supplier_uid` INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'cashier')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `cashier` tinyint(1) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'realprice')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `realprice` decimal(10,2) DEFAULT '0.00';");
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

//app 首页banner表 2016-6-21
pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_banner')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0',
  `thumb_pc` varchar(500) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

//app 客户订单推送消息表 2016-6-21
pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_message')." (
   `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) NOT NULL COMMENT '用户openid',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `contents` text NOT NULL COMMENT '内容',
  `status` set('0','1') NOT NULL DEFAULT '0' COMMENT '0-未读；1-已读',
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

//app 系统推送消息表 2016-6-21
pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_push')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(50) DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `content` text,
  `time` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

//会员表 增加app绑定字段
if(!pdo_fieldexists('sz_yi_member', 'bindapp')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `bindapp` tinyint(4) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'ordersn_general')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `ordersn_general` varchar(255) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_order', 'pay_ordersn')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `pay_ordersn` varchar(255) NOT NULL DEFAULT '';");
}

//前台下单 判断是否支持配送核销字段
if(!pdo_fieldexists('sz_yi_goods', 'isverifysend')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `isverifysend` tinyint(1) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_goods', 'supplier_uid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `supplier_uid` int(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_goods', 'isreturn')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `isreturn` tinyint(1) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_goods', 'isreturnqueue')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `isreturnqueue` tinyint(1) DEFAULT '0';");
}

pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." CHANGE `realprice` `realprice` decimal(10,2) DEFAULT '0';");

//快递助手缺少
if(!pdo_fieldexists('sz_yi_order', 'address_send')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `address_send` varchar(2000) NOT NULL DEFAULT '';");
}

//收银台添加店员表结构
pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_cashier_store_waiter')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) DEFAULT NULL,
  `realname` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `uniacid` int(11) DEFAULT NULL,
  `createtime` varchar(255) DEFAULT NULL,
  `savetime` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

$sql = "
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_cashier_order')." (
  `order_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `cashier_store_id` int(11) NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收银台商户订单';

CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_cashier_store')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL COMMENT '店名',
  `thumb` varchar(255) NOT NULL,
  `contact` varchar(100) DEFAULT NULL COMMENT '联系人',
  `mobile` varchar(30) DEFAULT NULL COMMENT '电话',
  `address` varchar(500) DEFAULT NULL COMMENT '地址',
  `member_id` int(11) DEFAULT '0' COMMENT '绑定的会员微信号',
  `deduct_credit1` decimal(10,2) DEFAULT '0.00' COMMENT '抵扣设置,允许使用的积分百分比',
  `deduct_credit2` decimal(10,2) DEFAULT '0.00' COMMENT '抵扣设置,允许使用的余额百分比',
  `settle_platform` decimal(10,2) DEFAULT '0.00' COMMENT '结算比例,平台比例',
  `settle_store` decimal(10,2) DEFAULT '0.00' COMMENT '结算比例,商家比例',
  `commission1_rate` decimal(10,2) DEFAULT '0.00' COMMENT '佣金比例,一级分销,消费者在商家用收银台支付后，分销商获得的佣金比例',
  `commission2_rate` decimal(10,2) DEFAULT '0.00' COMMENT '佣金比例,二级分销',
  `commission3_rate` decimal(10,2) DEFAULT '0.00' COMMENT '佣金比例,三级分销',
  `credit1` decimal(10,2) DEFAULT '0.00' COMMENT '消费者在商家支付完成后，获得的积分奖励百分比',
  `redpack_min` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最小消费金额，才会发红包',
  `redpack` decimal(10,2) DEFAULT '0.00' COMMENT '消费者在商家支付完成后，获得的红包奖励百分比',
  `coupon_id` int(11) NOT NULL DEFAULT '0' COMMENT '优惠卷',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deredpack` tinyint(1) NOT NULL DEFAULT '0' COMMENT '扣除红包金额',
  `decommission` tinyint(1) NOT NULL DEFAULT '0' COMMENT '扣除佣金金额',
  `decredits` tinyint(1) NOT NULL DEFAULT '0' COMMENT '扣除奖励余额金额',
  `creditpack` decimal(10,2) DEFAULT '0.00' COMMENT '消费者在商家支付完成后，获得的余额奖励百分比',
  `iscontact` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否填写联系人信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_cashier_withdraw')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `withdraw_no` varchar(255) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `cashier_store_id` int(11) NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `status` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '提现状态 0 生成 1 成功 2 失败',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='收银台商户提现表';
";
pdo_fetchall($sql);

//供应商
pdo_fetchall("
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_af_supplier')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) CHARACTER SET utf8 NOT NULL,
  `uniacid` int(11) NOT NULL,
  `realname` varchar(55) CHARACTER SET utf8 NOT NULL,
  `mobile` varchar(255) CHARACTER SET utf8 NOT NULL,
  `weixin` varchar(255) CHARACTER SET utf8 NOT NULL,
  `productname` varchar(255) CHARACTER SET utf8 NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `status` tinyint(3) NOT NULL COMMENT '1审核成功2驳回',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_supplier_apply')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '供应商id',
  `uniacid` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1手动2微信',
  `applysn` varchar(255) NOT NULL COMMENT '提现单号',
  `apply_money` int(11) NOT NULL COMMENT '申请金额',
  `apply_time` int(11) NOT NULL COMMENT '申请时间',
  `status` tinyint(3) NOT NULL COMMENT '0为申请状态1为完成状态',
  `finish_time` int(11) NOT NULL COMMENT '完成时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");


if(!pdo_fieldexists('sz_yi_perm_user', 'banknumber')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `banknumber` varchar(255) NOT NULL COMMENT '银行卡号';");
}
if(!pdo_fieldexists('sz_yi_perm_user', 'accountname')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `accountname` varchar(255) NOT NULL COMMENT '开户名';");
}
if(!pdo_fieldexists('sz_yi_perm_user', 'accountbank')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `accountbank` varchar(255) NOT NULL COMMENT '开户行';");
}

if(!pdo_fieldexists('sz_yi_goods', 'supplier_uid')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_goods')." ADD `supplier_uid` INT NOT NULL COMMENT '供应商ID';");
}
if(!pdo_fieldexists('sz_yi_order', 'supplier_uid')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `supplier_uid` INT NOT NULL COMMENT '供应商ID';");
}
if(!pdo_fieldexists('sz_yi_order_goods', 'supplier_uid')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD `supplier_uid` INT NOT NULL COMMENT '供应商ID';");
}
if(!pdo_fieldexists('sz_yi_order_goods', 'supplier_apply_status')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD `supplier_apply_status` tinyint(4) NOT NULL COMMENT '1为供应商已提现';");
}

$info = pdo_fetch('select * from ' . tablename('sz_yi_plugin') . ' where identity= "supplier"  order by id desc limit 1');
$result = pdo_fetch('select * from ' . tablename('sz_yi_perm_role') . ' where status1=1');

//一级分类后台设置添加PC首页推荐广告
if(!pdo_fieldexists('sz_yi_category', 'advimg_pc')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_category')." ADD `advimg_pc` varchar(255) NOT NULL DEFAULT '';");
}

if(!pdo_fieldexists('sz_yi_category', 'advurl_pc')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_category')." ADD `advurl_pc` varchar(500) NOT NULL DEFAULT '';");
}

if (!pdo_fieldexists('sz_yi_supplier_apply', 'apply_ordergoods_ids')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_supplier_apply')." ADD  `apply_ordergoods_ids` text;");
}

//直接安装APP插件
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'app'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`, `category`) VALUES(". $displayorder .",'app','APP客户端','1.0','官方','1', 'biz');";
    pdo_fetchall($sql);
}

$sql = "
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_banner')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0',
  `thumb_pc` varchar(500) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
";
pdo_fetchall($sql);

$sql = "
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_message')." (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `openid` varchar(255) NOT NULL COMMENT '用户openid',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `contents` text NOT NULL COMMENT '内容',
  `status` set('0','1') NOT NULL DEFAULT '0' COMMENT '0-未读；1-已读',
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
";
pdo_fetchall($sql);

$sql = "
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_push')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(50) DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `content` text,
  `time` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
";
pdo_fetchall($sql);

if(!pdo_fieldexists('sz_yi_member', 'bindapp')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `bindapp` tinyint(4) NOT NULL DEFAULT '0';");
}

$plugins = pdo_fetchall('select * from ' . tablename('sz_yi_plugin') . ' order by displayorder asc');
m('cache')->set('plugins', $plugins, 'global');

//返现队列表 添加最后一次返现金额 2016-07-09 杨雷
if(pdo_tableexists('sz_yi_return')) {
    if(!pdo_fieldexists('sz_yi_return', 'last_money')) {
        pdo_fetchall("ALTER TABLE ".tablename('sz_yi_return')." ADD `last_money` DECIMAL(10,2) NOT NULL AFTER `return_money`;");
    }
    //返现队列表 添加更新时间 2016-07-09 杨雷
    if(!pdo_fieldexists('sz_yi_return', 'updatetime')) {
        pdo_fetchall("ALTER TABLE ".tablename('sz_yi_return')." ADD `updatetime` VARCHAR(255) NOT NULL AFTER `create_time`;");
    }
    //返现队列表 添加删除队列字段 2016-07-16 杨雷
    if(!pdo_fieldexists('sz_yi_return', 'delete')) {
        pdo_fetchall("ALTER TABLE ".tablename('sz_yi_return')." ADD `delete` TINYINT(1) NULL DEFAULT '0';");
    }
}

$plugins = pdo_fetchall('select * from ' . tablename('sz_yi_plugin') . ' order by displayorder asc');
m('cache')->set('plugins', $plugins, 'global');

//分销佣金消费记录金额
if(!pdo_fieldexists('sz_yi_member', 'credit20')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `credit20` DECIMAL(10,2) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('mc_members', 'credit20')) {
    pdo_fetchall("ALTER TABLE ".tablename('mc_members')." ADD `credit20` DECIMAL(10,2) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_commission_apply', 'credit20')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_commission_apply')." ADD `credit20` DECIMAL(10,2) NOT NULL DEFAULT '0';");
}

//转让记录表 2016-7-12 杨雷
$sql = "
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_member_transfer_log')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `tosell_id` int(11) DEFAULT NULL COMMENT '出让人id',
  `assigns_id` int(11) DEFAULT NULL COMMENT '受让人id',
  `createtime` int(11) NOT NULL,
  `status` tinyint(3) NOT NULL COMMENT '-1 失败 0 进行中 1 成功',
  `money` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";
pdo_fetchall($sql);

//20160718添加 代理商升级条件添加二三级
if(pdo_tableexists('sz_yi_bonus_level')){
  //下线二级人数
  if(!pdo_fieldexists('sz_yi_bonus_level', 'downcountlevel2')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_bonus_level')." ADD `downcountlevel2` int(11) DEFAULT '0';");
  }
  //下线三级人数
  if (!pdo_fieldexists('sz_yi_bonus_level', 'downcountlevel3')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_bonus_level')." ADD `downcountlevel3` int(11) DEFAULT '0';");
  }
}

//文章是否在微信显示 2016-07-18 杨雷
if (!pdo_fieldexists('sz_yi_article', 'article_state_wx')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_article')." ADD `article_state_wx` int(11) DEFAULT '0';");
}

//优惠券新加字段
if(!pdo_fieldexists('sz_yi_coupon', 'getcashier')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `getcashier` tinyint(1) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_coupon', 'usetype')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `usetype` tinyint(1) NOT NULL DEFAULT '1';");
}
if(!pdo_fieldexists('sz_yi_coupon', 'cashiersids')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `cashiersids` text NULL ;");
}
if(!pdo_fieldexists('sz_yi_coupon', 'cashiersnames')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `cashiersnames` text NULL ;");
}
if(!pdo_fieldexists('sz_yi_coupon', 'categoryids')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `categoryids` text NULL ;");
}
if(!pdo_fieldexists('sz_yi_coupon', 'categorynames')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `categorynames` text NULL ;");
}
if(!pdo_fieldexists('sz_yi_coupon', 'goodsnames')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `goodsnames` text NULL ;");
}
if(!pdo_fieldexists('sz_yi_coupon', 'goodsids')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `goodsids` text NULL ;");
}


//hotel
//商品表增加押金字段
if(!pdo_fieldexists('sz_yi_goods', 'deposit')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_goods')." ADD `deposit` decimal(10,2) DEFAULT '0.00' AFTER `isreturnqueue`;");
}
//商品表增加打印机id
if(!pdo_fieldexists('sz_yi_goods', 'print_id')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_goods')." ADD `print_id` INT(11) DEFAULT '0' AFTER `deposit`;");
}
//订单表增加字段（入住人姓名，电话，性别，发票信息，押金等）
if(!pdo_fieldexists('sz_yi_order', 'checkname')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `checkname` varchar(255) DEFAULT '' AFTER `ordersn_general`;");
}

if(!pdo_fieldexists('sz_yi_order', 'realmobile')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `realmobile` varchar(255) DEFAULT '' AFTER `checkname`;");
}

if(!pdo_fieldexists('sz_yi_order', 'realsex')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `realsex` INT(1) DEFAULT '0' AFTER `realmobile`;");
}

if(!pdo_fieldexists('sz_yi_order', 'invoice')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `invoice`  INT(1) DEFAULT '0'  AFTER `realsex`;");
}

if(!pdo_fieldexists('sz_yi_order', 'invoiceval')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `invoiceval` INT(1) DEFAULT '0' AFTER `invoice`;");
}

if(!pdo_fieldexists('sz_yi_order', 'invoicetext')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `invoicetext` varchar(255) DEFAULT '' AFTER `invoiceval`;");
}

if(!pdo_fieldexists('sz_yi_order', 'num')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `num` INT(1) DEFAULT '0' AFTER `invoicetext`;");
}

if(!pdo_fieldexists('sz_yi_order', 'btime')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `btime` INT(11) DEFAULT '0' AFTER `num`;");
}

if(!pdo_fieldexists('sz_yi_order', 'etime')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `etime` INT(11) DEFAULT '0' AFTER `btime`;");
}

if(!pdo_fieldexists('sz_yi_order', 'depositprice')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `depositprice` decimal(10,2) DEFAULT '0.00' AFTER `etime`;");
}

if(!pdo_fieldexists('sz_yi_order', 'returndepositprice')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `returndepositprice`  decimal(10,2) DEFAULT '0.00' AFTER `depositprice`;");
}

if(!pdo_fieldexists('sz_yi_order', 'depositpricetype')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `depositpricetype` INT(1) DEFAULT '0' AFTER `returndepositprice`;");
}

if(!pdo_fieldexists('sz_yi_order', 'room_number')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `room_number` varchar(11) DEFAULT '' AFTER `depositpricetype`;");
}

if(!pdo_fieldexists('sz_yi_order', 'roomid')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `roomid` INT(11) DEFAULT '0' AFTER `room_number`;");
}

if(!pdo_fieldexists('sz_yi_order', 'order_type')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `order_type`  INT(11) DEFAULT '0' AFTER `roomid`;");
}

if(!pdo_fieldexists('sz_yi_order', 'days')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `days`  INT(11) DEFAULT '0' AFTER `order_type`;");
}

//分销商升级添加提现比例
if(!pdo_fieldexists('sz_yi_commission_level', 'withdraw_proportion')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_commission_level')." ADD `withdraw_proportion`  DECIMAL( 10, 2 ) DEFAULT '0.00';");
}

//分销商等级权重字段
if(!pdo_fieldexists('sz_yi_commission_level', 'level')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_commission_level')." ADD `level`  INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_commission_level', 'downcount')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_commission_level')." ADD `downcount`  INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_commission_level', 'ordercount')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_commission_level')." ADD `ordercount`  INT(11) DEFAULT '0';");
}

//代理商添加审核图片字段
if (!pdo_fieldexists('sz_yi_member', 'check_imgs')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `check_imgs` text DEFAULT '';");
}

//分单后台合并付款需更新所有通用订单号为空的订单写入订单号
pdo_query('update ' . tablename('sz_yi_order') . ' set ordersn_general = ordersn where ordersn_general=""');


//自定义分类的其他分类
if (!pdo_fieldexists('sz_yi_goods', 'pcates')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `pcates` text DEFAULT '';");
}
if (!pdo_fieldexists('sz_yi_goods', 'pcates2')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `pcates2` text DEFAULT '';");
}
if (!pdo_fieldexists('sz_yi_goods', 'ccates')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `ccates` text DEFAULT '';");
}
if (!pdo_fieldexists('sz_yi_goods', 'ccates2')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `ccates2` text DEFAULT '';");
}
if (!pdo_fieldexists('sz_yi_goods', 'tcates')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `tcates` text DEFAULT '';");
}
if (!pdo_fieldexists('sz_yi_goods', 'tcates2')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `tcates2` text DEFAULT '';");
}

//收银台消费条件
if (!pdo_fieldexists('sz_yi_cashier_store', 'condition')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_cashier_store')." ADD `condition` decimal(10,2) DEFAULT '0.00';");
}

//优惠券指定门店新字段
if (!pdo_fieldexists('sz_yi_coupon', 'storeids')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `storeids` text DEFAULT '';");
}
if (!pdo_fieldexists('sz_yi_coupon', 'storenames')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `storenames` text DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_coupon', 'getstore')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD `getstore` tinyint(1) NOT NULL DEFAULT '0'");
}

//返现记录 返现余额积分类型 2016-07-26 杨雷
if(!pdo_fieldexists('sz_yi_return_log', 'credittype')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_return_log')." ADD `credittype` VARCHAR(60) NOT NULL AFTER `openid`;");
}
//低版本缺少字段
if(!pdo_fieldexists('sz_yi_saler', 'salername')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_saler')." ADD `salername` VARCHAR(255) DEFAULT '';");
}
//绑定手机用
if(!pdo_fieldexists('sz_yi_member', 'bonuslevel')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `bonuslevel` INT DEFAULT '0' AFTER `agentlevel`, ADD `bonus_status` TINYINT(1) DEFAULT '0' AFTER `bonuslevel`;");
}
//积分商城优惠券字段
if(!pdo_fieldexists('sz_yi_creditshop_log', 'couponid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_creditshop_log')." ADD `couponid` INT(11) DEFAULT '0' ;");
}
//快速选购新加简单详情以及全部商品字段
if(pdo_tableexists('sz_yi_chooseagent')){
    if(!pdo_fieldexists('sz_yi_chooseagent', 'isopenchannel')) {
      pdo_query("ALTER TABLE ".tablename('sz_yi_chooseagent')." ADD `isopenchannel` tinyint(1) NOT NULL COMMENT '0关闭1开启';");
    }
    if(!pdo_fieldexists('sz_yi_chooseagent', 'detail')) {
        pdo_fetchall("ALTER TABLE ".tablename('sz_yi_chooseagent')." ADD `detail` INT(11) DEFAULT '0' ;");
    }
    if(!pdo_fieldexists('sz_yi_chooseagent', 'allgoods')) {
        pdo_fetchall("ALTER TABLE ".tablename('sz_yi_chooseagent')." ADD `allgoods` INT(11) DEFAULT '0' ;");
    }
}
//优惠劵添加供应商id
if(!pdo_fieldexists('sz_yi_coupon', 'supplier_uid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_coupon')." ADD  `supplier_uid`  int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_category', 'supplier_uid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_category')." ADD  `supplier_uid`  int(11) DEFAULT '0';");
}

//分销商折扣  2016-7-29 杨雷
if(!pdo_fieldexists('sz_yi_goods', 'discounttype')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `discounttype` TINYINT DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_goods', 'discounts2')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `discounts2` TEXT  DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_goods', 'returns2')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `returns2` TEXT DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_goods', 'returntype')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `returntype` TINYINT DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_goods', 'discountway')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `discountway` TINYINT DEFAULT '0';");
}

pdo_fetchall("UPDATE ".tablename('sz_yi_goods')." SET  `discounttype` =1,`returntype` =1,`discountway` =1 WHERE discounttype = 0 AND returntype = 0 AND discountway = 0;");

pdo_fetchall("ALTER TABLE ".tablename('sz_yi_message')." CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '编号'");
pdo_fetchall("ALTER TABLE ".tablename('sz_yi_banner')." CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '编号'");
pdo_fetchall("ALTER TABLE ".tablename('sz_yi_push')." CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '编号'");

// 会员表增加 支付宝信息字段
//支付宝账号
if(!pdo_fieldexists('sz_yi_member', 'alipay')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_member')." ADD `alipay`  varchar(255) DEFAULT '' AFTER `credit20`;");
}
//姓名
if(!pdo_fieldexists('sz_yi_member', 'alipayname')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_member')." ADD `alipayname`  varchar(255) DEFAULT '' AFTER `alipay`;");
}

//分销提现表增加 字段
//分销表中收款人账号
if(!pdo_fieldexists('sz_yi_commission_apply', 'alipay')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_commission_apply')." ADD `alipay`  varchar(255) DEFAULT '' AFTER `credit20`;");
}
//收款人姓名
if(!pdo_fieldexists('sz_yi_commission_apply', 'alipayname')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_commission_apply')." ADD `alipayname`  varchar(255) DEFAULT '' AFTER `alipay`;");
}
//批次号
if(!pdo_fieldexists('sz_yi_commission_apply', 'batch_no')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_commission_apply')." ADD `batch_no`  varchar(255) DEFAULT '' AFTER `alipayname`;");
}
//到账时间
if(!pdo_fieldexists('sz_yi_commission_apply', 'finshtime')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_commission_apply')." ADD `finshtime`  int(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_member_log', 'batch_no')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_member_log')." ADD `batch_no`  varchar(255) DEFAULT '';");
}

//增加插件说明
if(!pdo_fieldexists('sz_yi_plugin', 'desc')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_plugin')." ADD `desc` varchar(800) NULL");
}
$plugins_desc = array(
  "supplier" => "厂家入驻，平台统一销售", 
  "commission" => "客户下单后上线获得返现奖励",
  "system" => "分销商关系调整、数据管理",
  "creditshop" => "积分兑换礼品或抽奖",
  "article" => "一键转发，隐形锁粉，赚奖励",
  "yunpay" => "微信支付，支付宝，银联，信用卡",
  "exhelper" => "快速打印快递单、发货单，一键发货",
  "verify" => "线上下单门店提货，配送核销",
  "qiniu" => "高效的附件存储方案",
  "taobao" => "一键批量导入淘宝商品",
  "choose" => "快速购买多件商品",
  "tmessage" => "微信无限制模板消息群发",
  "coupon" => "设置多种使用范围的优惠券",
  "diyform" => "高效灵活收集信息",
  "perm" => "让员工各尽其职",
  "poster" => "海报锁粉，获得奖励",
  "postera" => "限时不限量，高效锁粉",
  "designer" => "DIY店铺首页、专题、导航菜单",
  "app" => "苹果+安卓双版本，无限消息推送",
  "sale" => "积分、余额抵扣，满额优惠，充值满减",
  "return" => "排队全返、订单全返、订单满额返、会员等级返现",
  "virtual" => "下单自动发送虚拟卡密",
  "ranking" => "消费金额、佣金、积分排行",
  "fans" => "解决粉丝头像、昵称获取异常",
  "hotel" => "房态、房价管理，酒店、会议、餐饮预订",
  "bonus" => "代理级差分红、全球分红、区域分红",
  "customer" => "kehu",
  "merchant" => "招募供应商获得销售分红",
  "channel" => "虚拟库存，人、货、钱一体化管理",
  "cashier" => "能分销、分红、全返，奖励红包的收银台",
);

$sql = "select * from ".tablename('sz_yi_plugin');
$plugin_list = pdo_fetchall($sql);
foreach ($plugin_list as $pl) {
  if ($pl['identity'] == "cashier" && $pl['category'] == 0) {
    $data = array('category' => 'biz');
    pdo_update('sz_yi_plugin', $data, array(
      'identity' => $pl['identity']
    ));
  }
    if ($pl['identity'] == "choose" && $pl['category'] == 0) {
    $data = array('category' => 'biz');
    pdo_update('sz_yi_plugin', $data, array(
      'identity' => $pl['identity']
    ));
  }
  if ($pl['desc'] == "") {
    $data = array('desc' => $plugins_desc[$pl['identity']]);
    pdo_update('sz_yi_plugin', $data, array(
      'identity' => $pl['identity']
    ));
  }
}

if(!pdo_fieldexists('sz_yi_category', 'supplier_uid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_category')." ADD  `supplier_uid`  int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_member_log', 'aging_id')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_log')." ADD `aging_id` int(11) DEFAULT '0';");
}

if (!pdo_fieldexists('sz_yi_member_log', 'paymethod')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_log')." ADD `paymethod` tinyint(1) DEFAULT '0';");
}

//爱心基金类
if(!pdo_fieldexists('sz_yi_article_category', 'loveshow')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article_category')." ADD `loveshow` tinyint(1) NOT NULL DEFAULT '0'");
}

//事业基金金额
if(!pdo_fieldexists('sz_yi_article', 'love_money')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article')." ADD  `love_money`  DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' COMMENT '事业基金金额';");
}

if(!pdo_fieldexists('sz_yi_article', 'love_log_id')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article')." ADD  `love_log_id`  int( 11 ) NOT NULL DEFAULT '0' COMMENT '爱心基金记录id';");
}

//商品事业基金金额
if(!pdo_fieldexists('sz_yi_goods', 'love_money')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD  `love_money`  DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' COMMENT '事业基金金额';");
}

//代理商提现记录
pdo_fetchall("CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_bonus_log') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `applyid` int(11) DEFAULT '0',
  `mid` int(11) DEFAULT '0',
  `commission` decimal(10,2) DEFAULT '0.00',
  `createtime` int(11) DEFAULT '0',
  `commission_pay` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_applyid` (`applyid`),
  KEY `idx_mid` (`mid`),
  KEY `idx_createtime` (`createtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
//渠道商所需字段
if(!pdo_fieldexists('sz_yi_member', 'ischannel')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_member')." ADD `ischannel` INT(1) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_member', 'channel_level')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_member')." ADD `channel_level` INT(1) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_member', 'channeltime')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_member')." ADD `channeltime` INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'ischannelself')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `ischannelself` INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order_goods', 'channel_id')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD `channel_id` INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order_goods', 'channel_apply_status')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD `channel_apply_status` tinyint(1) NOT NULL COMMENT '0未提现1申请中2已提现';");
}

if(!pdo_fieldexists('sz_yi_goods', 'isopenchannel')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_goods')." ADD `isopenchannel` tinyint(1) NOT NULL COMMENT '0关闭1开启';");
}

if(!pdo_fieldexists('sz_yi_order_goods', 'ischannelpay')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD `ischannelpay` tinyint(1) NOT NULL COMMENT '0不是1渠道商采购订单';");
}

if(!pdo_fieldexists('sz_yi_order', 'iscmas')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `iscmas` INT(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_member', 'isagency')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `isagency` tinyint(1) DEFAULT '0';");
}

$result = pdo_fetch('select * from ' . tablename('sz_yi_perm_role') . ' where status1=1');
if(empty($result)){
    $sql = "
INSERT INTO " . tablename('sz_yi_perm_role') . " (`rolename`, `status`, `status1`, `perms`, `deleted`) VALUES
('供应商', 1, 1, 'shop,shop.goods,shop.goods.view,shop.goods.add,shop.goods.edit,shop.goods.delete,shop.dispatch,shop.dispatch.view,shop.dispatch.add,shop.dispatch.edit,shop.dispatch.delete,order,order.view,order.view.status_1,order.view.status0,order.view.status1,order.view.status2,order.view.status3,order.view.status4,order.view.status5,order.view.status9,order.op,order.op.pay,order.op.send,order.op.sendcancel,order.op.finish,order.op.verify,order.op.fetch,order.op.close,order.op.refund,order.op.export,order.op.changeprice,exhelper,exhelper.print,exhelper.print.single,exhelper.print.more,exhelper.exptemp1,exhelper.exptemp1.view,exhelper.exptemp1.add,exhelper.exptemp1.edit,exhelper.exptemp1.delete,exhelper.exptemp1.setdefault,exhelper.exptemp2,exhelper.exptemp2.view,exhelper.exptemp2.add,exhelper.exptemp2.edit,exhelper.exptemp2.delete,exhelper.exptemp2.setdefault,exhelper.senduser,exhelper.senduser.view,exhelper.senduser.add,exhelper.senduser.edit,exhelper.senduser.delete,exhelper.senduser.setdefault,exhelper.short,exhelper.short.view,exhelper.short.save,exhelper.printset,exhelper.printset.view,exhelper.printset.save,exhelper.dosend,taobao,taobao.fetch', 0);";
    pdo_query($sql);
}else{
    $gysdata = array("perms" => 'shop,shop.goods,shop.goods.view,shop.goods.add,shop.goods.edit,shop.goods.delete,shop.dispatch,shop.dispatch.view,shop.dispatch.add,shop.dispatch.edit,shop.dispatch.delete,order,order.view,order.view.status_1,order.view.status0,order.view.status1,order.view.status2,order.view.status3,order.view.status4,order.view.status5,order.view.status9,order.op,order.op.pay,order.op.send,order.op.sendcancel,order.op.finish,order.op.verify,order.op.fetch,order.op.close,order.op.refund,order.op.export,order.op.changeprice,exhelper,exhelper.print,exhelper.print.single,exhelper.print.more,exhelper.exptemp1,exhelper.exptemp1.view,exhelper.exptemp1.add,exhelper.exptemp1.edit,exhelper.exptemp1.delete,exhelper.exptemp1.setdefault,exhelper.exptemp2,exhelper.exptemp2.view,exhelper.exptemp2.add,exhelper.exptemp2.edit,exhelper.exptemp2.delete,exhelper.exptemp2.setdefault,exhelper.senduser,exhelper.senduser.view,exhelper.senduser.add,exhelper.senduser.edit,exhelper.senduser.delete,exhelper.senduser.setdefault,exhelper.short,exhelper.short.view,exhelper.short.save,exhelper.printset,exhelper.printset.view,exhelper.printset.save,exhelper.dosend,taobao,taobao.fetch');
    pdo_update('sz_yi_perm_role', $gysdata, array('rolename' => "供应商", 'status1' => 1));
}


//@rmdirs(IA_ROOT. "/data/tpl/app/sz_yi");

//2016-8-14 余额转让记录增表 加转让人当前余额 受让人当前余额
if(!pdo_fieldexists('sz_yi_member_transfer_log', 'tosell_current_credit')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_member_transfer_log')." ADD `tosell_current_credit` DECIMAL(10,2) NOT NULL AFTER `money`;");
}
if(!pdo_fieldexists('sz_yi_member_transfer_log', 'assigns_current_credit')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_member_transfer_log')." ADD `assigns_current_credit` DECIMAL(10,2) NOT NULL AFTER `tosell_current_credit`;");
}


if (!pdo_fieldexists('sz_yi_member', 'bank')) {
    pdo_fetchall("ALTER TABLE  ".tablename('sz_yi_member')." ADD  `bank` VARCHAR( 255 ) DEFAULT '' COMMENT '开户行';");
}

if (!pdo_fieldexists('sz_yi_member', 'bank_num')) {
    pdo_fetchall("ALTER TABLE  ".tablename('sz_yi_member')." ADD  `bank_num` VARCHAR( 100 ) DEFAULT '' COMMENT '银行卡号';");
}


//收银台是否加入全返字段
if (!pdo_fieldexists('sz_yi_cashier_store', 'isreturn')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_cashier_store')." ADD `isreturn` tinyint(1) DEFAULT '0';");
}

//2016-8-15 商品是否返虚拟币  虚拟币返现比例 
if (!pdo_fieldexists('sz_yi_goods', 'yunbi_consumption')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `yunbi_consumption` DECIMAL(6,3) NOT NULL AFTER `isopenchannel`;");
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

if(!pdo_fieldexists('sz_yi_member', 'referrer')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_member')." ADD `referrer`  VARCHAR(11)  AFTER `bonus_street`;");
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

echo  "完成虚拟币添加数据库！";

