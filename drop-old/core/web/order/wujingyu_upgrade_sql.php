<?php
//收银台添加会员中心是否可以编辑的字段
if (!pdo_fieldexists('sz_yi_cashier_store', 'centercan')) {
    pdo_fetchall("ALTER TABLE " . tablename('sz_yi_cashier_store') . " ADD    `centercan` tinyint(1) DEFAULT '1';");
}
//优惠券指定供应商新加字段
if (!pdo_fieldexists('sz_yi_coupon', 'getsupplier')) {
    pdo_fetchall("ALTER TABLE " . tablename('sz_yi_coupon') . " ADD    `getsupplier` tinyint(1) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_coupon', 'supplierids')) {
    pdo_fetchall("ALTER TABLE " . tablename('sz_yi_coupon') . " ADD    `supplierids` text(0) DEFAULT '';");
}

if (!pdo_fieldexists('sz_yi_coupon', 'suppliernames')) {
    pdo_fetchall("ALTER TABLE " . tablename('sz_yi_coupon') . " ADD    `suppliernames` text(0) DEFAULT '';");
}

//O2O项目新增字段
if(!pdo_fieldexists('sz_yi_store', 'member_id')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `member_id` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_store', 'balance')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `balance` decimal(10,2) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_goods', 'goods_balance')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD    `goods_balance` decimal(10,2) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_goods', 'balance_with_store')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD    `balance_with_store` tinyint(1) DEFAULT '1';");
}
if(!pdo_fieldexists('sz_yi_goods', 'dispatchsend')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD    `dispatchsend` tinyint(1) DEFAULT '0';");
}
if (pdo_tableexists('sz_yi_chooseagent')) {
    if(!pdo_fieldexists('sz_yi_chooseagent', 'isstore')) {
        pdo_fetchall("ALTER TABLE ".tablename('sz_yi_chooseagent')." ADD    `isstore` tinyint(1) DEFAULT '0';");
    }
    if(!pdo_fieldexists('sz_yi_chooseagent', 'storeid')) {
        pdo_fetchall("ALTER TABLE ".tablename('sz_yi_chooseagent')." ADD    `storeid` int(11) DEFAULT '0';");
    }
}


$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_store_goods') . " (
 `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(100) DEFAULT '',
  `goodsid` int(11) DEFAULT '0',
  `total` int(11) NOT NULL DEFAULT '0',
  `marketprice` decimal(10,2) DEFAULT '0.00',
  `deleted` tinyint(1) DEFAULT '0',
  `optionid` int(11) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `diyformdata` text,
  `diyformfields` text,
  `diyformdataid` int(11) DEFAULT '0',
  `diyformid` int(11) DEFAULT '0',
  `storeid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_goodsid` (`goodsid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_deleted` (`deleted`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 comment='门店商品表';


CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_store_withdraw') . " (
 `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `withdraw_no` varchar(255) NOT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `store_id` int(11) NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `status` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '提现状态 0 生成 1 成功 2 失败',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `apply_time` varchar(255) NOT NULL,
  `refuse_time` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 comment='门店提现表';

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_cancel_goods') . " (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uniacid` int(11) NOT NULL COMMENT '公众号ID',
  `orderid` int(11) NOT NULL COMMENT '订单ID',
  `from_storeid` int(11) NOT NULL COMMENT '原来属于的门店',
  `last_storeid` int(11) NOT NULL COMMENT '最后得到的门店',
  `ismaster` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 comment='门店取消配送表';

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_store_category') . " (
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
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_enabled` (`enabled`),
  KEY `idx_parentid` (`parentid`),
  KEY `idx_isrecommand` (`isrecommand`),
  KEY `idx_ishome` (`ishome`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  comment='门店分类表' ;";

pdo_fetchall($sql);
//20161018
if(!pdo_fieldexists('sz_yi_store', 'pcate')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `pcate` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_store', 'ccate')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `ccate` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_store', 'tcate')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `tcate` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_store', 'cashierid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `cashierid` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_store_category', 'advimg_pc')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store_category')." ADD    `advimg_pc` varchar(255) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_store_category', 'advurl_pc')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store_category')." ADD    `advurl_pc` varchar(255) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_store_category', 'supplier_uid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store_category')." ADD    `supplier_uid` int(11) DEFAULT '0';");
}
//20161020
if(!pdo_fieldexists('sz_yi_store', 'city')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `city` varchar(30) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_store', 'area')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `area` varchar(30) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_store', 'province')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `province` varchar(30) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_store', 'street')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `street` varchar(30) DEFAULT '0';");
}
//20161021
if(!pdo_fieldexists('sz_yi_store', 'singleprice')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `singleprice` decimal(10,2) DEFAULT '0';");
}
//20161022
if(!pdo_fieldexists('sz_yi_store', 'info')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `info` varchar(255) DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_store', 'thumb')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `thumb` varchar(255) DEFAULT '';");
}
//20161024
if(!pdo_fieldexists('sz_yi_order_comment', 'storeid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_comment')." ADD    `storeid` int(11) DEFAULT '0';");
}
//20161031
if(!pdo_fieldexists('sz_yi_store', 'supplier_uid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_store')." ADD    `supplier_uid` int(11) DEFAULT '0';");
}
//20161103
if(!pdo_fieldexists('sz_yi_member_address', 'street')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_address')." ADD    `street` varchar(255) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_member', 'street')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD    `street` varchar(255) DEFAULT '0';");
}

if (!pdo_fieldexists('sz_yi_member_log', 'couponid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_log')." ADD `couponid` int(11) DEFAULT '0' COMMENT '优惠券id';");
}

echo '完成sql执行';
