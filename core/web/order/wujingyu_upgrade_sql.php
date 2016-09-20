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
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD    `goods_balance` decimal(10，2) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_goods', 'balance_with_store')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD    `balance_with_store` tinyint(1) DEFAULT '1';");
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 comment='门店取消配送表';";
pdo_fetchall($sql);

