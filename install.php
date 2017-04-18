<?php
global $_W;

$sql = "
DROP TABLE IF EXISTS `". tablename('yz_account_open_config')."`;

CREATE TABLE `". tablename('yz_account_open_config')."` (
  `config_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '统一公众号ID',
  `app_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用公钥',
  `app_secret` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用私钥',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '接入类型 0-公众号；1-小程序；2-微信app；3-扫码',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_address
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_address')."`;

CREATE TABLE `". tablename('yz_address')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `areaname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parentid` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_agent_level
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_agent_level')."`;

CREATE TABLE `". tablename('yz_agent_level')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分销等级名称',
  `level` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `first_level` int(11) DEFAULT '0' COMMENT '一级分比例',
  `second_level` int(11) DEFAULT '0' COMMENT '二级分销比例',
  `third_level` int(11) DEFAULT '0' COMMENT '三级分销比例',
  `upgraded` text COLLATE utf8mb4_unicode_ci COMMENT '升级条件',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_agents
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_agents')."`;

CREATE TABLE `". tablename('yz_agents')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0' COMMENT '上一级ID',
  `agent_level_id` int(11) DEFAULT '0' COMMENT '分销商等级ID',
  `is_black` tinyint(1) DEFAULT '0' COMMENT '0:正常分销商 1：加入黑名单',
  `commission_total` decimal(14,2) DEFAULT '0.00' COMMENT '累计佣金',
  `commission_pay` decimal(14,2) DEFAULT NULL COMMENT '已打款佣金',
  `agent_not_upgrade` tinyint(1) DEFAULT '0' COMMENT '不自动升级 0：自动升级 1：不自动升级',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间、成为分销商时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '修改时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '删除时间',
  `parent` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid_parent` (`uniacid`,`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_balance
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_balance')."`;

CREATE TABLE `". tablename('yz_balance')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `old_money` decimal(14,2) DEFAULT NULL COMMENT '原金额值',
  `change_money` decimal(14,2) NOT NULL COMMENT '改变金额值',
  `new_money` decimal(14,2) NOT NULL COMMENT '改变后金额',
  `type` tinyint(3) NOT NULL COMMENT '1收入，2支出',
  `service_type` tinyint(11) NOT NULL COMMENT '业务类型【1充值，2消费，3转账，4抵扣，5奖励，6余额提现，7提现至余额，8抵扣取消回滚，9奖励取消回滚】',
  `serial_number` varchar(45) NOT NULL DEFAULT '' COMMENT '流水号、订单号',
  `operator` int(11) NOT NULL COMMENT '-2,会员，-1,订单，0(商城)，1++（插件）',
  `operator_id` varchar(45) NOT NULL DEFAULT '' COMMENT '关联ID值，如文章营销的某文章ID',
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注【余额详细余额好】',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_balance_recharge
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_balance_recharge')."`;

CREATE TABLE `". tablename('yz_balance_recharge')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL COMMENT '会员ID',
  `old_money` decimal(14,2) DEFAULT NULL COMMENT '充值前金额',
  `money` decimal(14,2) DEFAULT NULL COMMENT '充值金额',
  `new_money` decimal(14,2) DEFAULT NULL COMMENT '充值后金额',
  `type` int(11) DEFAULT NULL COMMENT '充值类型（微信，支付宝）',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '修改时间',
  `ordersn` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '订单编号',
  `status` tinyint(1) DEFAULT '0' COMMENT '充值状态，-1充值失败，0正常，1充值成功',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_balance_transfer
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_balance_transfer')."`;

CREATE TABLE `". tablename('yz_balance_transfer')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号ID',
  `transferor` int(11) DEFAULT NULL COMMENT '转让者',
  `recipient` int(11) DEFAULT NULL COMMENT '被转让者',
  `money` decimal(14,2) DEFAULT NULL COMMENT '转让金额',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '-1失败，1成功',
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_brand
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_brand')."`;

CREATE TABLE `". tablename('yz_brand')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '品牌名称',
  `alias` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '品牌别名',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '品牌logo',
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '品牌描述信息',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_category')."`;

CREATE TABLE `". tablename('yz_category')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '分类名称',
  `thumb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '分类图片',
  `parent_id` int(11) DEFAULT '0' COMMENT '上级分类ID,0为第一级',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '分类介绍',
  `display_order` tinyint(1) DEFAULT '0' COMMENT '排序',
  `enabled` tinyint(1) DEFAULT '1' COMMENT '是否开启',
  `is_home` tinyint(1) DEFAULT '0',
  `adv_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `adv_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `level` tinyint(1) DEFAULT '0',
  `advimg_pc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `advurl_pc` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_parentid` (`parent_id`),
  KEY `idx_displayorder` (`display_order`),
  KEY `idx_enabled` (`enabled`),
  KEY `idx_ishome` (`is_home`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_comment')."`;

CREATE TABLE `". tablename('yz_comment')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) DEFAULT '0',
  `goods_id` int(11) NOT NULL DEFAULT '0',
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `nick_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `head_img_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `level` tinyint(1) DEFAULT '0',
  `images` text COLLATE utf8mb4_unicode_ci,
  `deleted` tinyint(1) DEFAULT '0',
  `comment_id` int(11) DEFAULT '0',
  `reply_id` int(11) DEFAULT '0',
  `reply_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_orderid` (`order_id`),
  KEY `idx_goodsid` (`goods_id`),
  KEY `idx_openid` (`uid`),
  KEY `idx_createtime` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_comment_bak2
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_comment_bak2')."`;

CREATE TABLE `". tablename('yz_comment_bak2')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) DEFAULT '0',
  `goods_id` int(11) NOT NULL DEFAULT '0',
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `nick_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `head_img_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `level` tinyint(1) DEFAULT '0',
  `images` text COLLATE utf8mb4_unicode_ci,
  `deleted` tinyint(1) DEFAULT '0',
  `comment_id` int(11) DEFAULT '0',
  `reply_id` int(11) DEFAULT '0',
  `reply_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_orderid` (`order_id`),
  KEY `idx_goodsid` (`goods_id`),
  KEY `idx_openid` (`uid`),
  KEY `idx_createtime` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_commission_edit_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_commission_edit_log')."`;

CREATE TABLE `". tablename('yz_commission_edit_log')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '操作人',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '操作内容',
  `type` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '操作内容',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_commission_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_commission_order')."`;

CREATE TABLE `". tablename('yz_commission_order')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `ordertable_type` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '佣金类型',
  `ordertable_id` int(11) DEFAULT NULL COMMENT '类型ID',
  `buy_id` int(11) DEFAULT NULL COMMENT '购买商品人ID',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '获得佣金人',
  `commission_amount` decimal(14,2) DEFAULT '0.00' COMMENT '分销金额',
  `formula` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '分销金额计算公式',
  `hierarchy` int(11) DEFAULT '1' COMMENT '分销层级',
  `commission_rate` int(11) DEFAULT '0' COMMENT '佣金比例',
  `commission` decimal(14,2) DEFAULT '0.00' COMMENT '佣金',
  `status` tinyint(1) DEFAULT '0' COMMENT '0=>预计佣金,1=>未结算,2=>已结算,3=>未提现,4=>已提现',
  `withdraw` tinyint(3) NOT NULL DEFAULT '0',
  `recrive_at` int(11) DEFAULT NULL COMMENT '收货时间',
  `settle_days` int(11) DEFAULT '0' COMMENT '结算天数',
  `statement_at` int(11) DEFAULT NULL COMMENT '结算时间',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_commission_order_goods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_commission_order_goods')."`;

CREATE TABLE `". tablename('yz_commission_order_goods')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `commission_order_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '商品名称',
  `thumb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '商品图片',
  `has_commission` tinyint(1) DEFAULT NULL COMMENT '是否启用独立规则0:未启用1：启用',
  `commission_rate` int(11) DEFAULT NULL COMMENT '独立比例',
  `commission_pay` decimal(14,2) DEFAULT NULL COMMENT '独立固定金额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_coupon
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_coupon')."`;

CREATE TABLE `". tablename('yz_coupon')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `cat_id` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `get_type` tinyint(3) DEFAULT '0' COMMENT '领券中心是否可获取 (0不可以; 1可以)',
  `get_max` int(11) DEFAULT '0' COMMENT '每人限领, -1为不限制数量',
  `use_type` tinyint(3) unsigned DEFAULT '0' COMMENT '适用范围，0商城通用  1指定分类 2指定商品',
  `return_type` tinyint(3) DEFAULT '0' COMMENT '退回方式 0 不可退回 1 取消订单(未付款) 2.退款可以退回',
  `bgcolor` varchar(255) DEFAULT '',
  `enough` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '消费满多少可用, 空或0 不限制',
  `coupon_type` tinyint(3) DEFAULT '0' COMMENT '0 优惠券 1 充值券 (没有充值券了)',
  `time_limit` tinyint(3) DEFAULT '0' COMMENT '0 领取后几天有效, 1 时间范围',
  `time_days` int(11) DEFAULT '0',
  `time_start` int(11) DEFAULT '0',
  `time_end` int(11) DEFAULT '0',
  `coupon_method` tinyint(4) NOT NULL COMMENT '优惠方式: 1为立减deduct, 2为折扣discount',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '折扣',
  `deduct` decimal(10,2) DEFAULT '0.00' COMMENT '立减',
  `back_type` tinyint(3) DEFAULT '0' COMMENT '返现方式',
  `back_money` varchar(50) DEFAULT '' COMMENT '返余额',
  `back_credit` varchar(50) DEFAULT '' COMMENT '返积分',
  `back_redpack` varchar(50) DEFAULT '' COMMENT '返现金',
  `back_when` tinyint(3) DEFAULT '0' COMMENT '返利方式  交易完成后（过退款期限自动返利）   订单完成后（收货后）   订单付款后',
  `thumb` varchar(255) DEFAULT '',
  `desc` text COMMENT '给用户看的使用说明',
  `total` int(11) DEFAULT '0' COMMENT '优惠券总数量 ( -1 不限制数量)',
  `status` tinyint(3) DEFAULT '0' COMMENT '可用',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '领取需要多少余额',
  `resp_desc` text COMMENT '推送描述',
  `resp_thumb` varchar(255) DEFAULT '' COMMENT '推送图片',
  `resp_title` varchar(255) DEFAULT '' COMMENT '推送标题',
  `resp_url` varchar(255) DEFAULT '' COMMENT '推送连接',
  `credit` int(11) DEFAULT '0' COMMENT '领取需要的积分',
  `usecredit2` tinyint(3) DEFAULT '0' COMMENT '优先使用余额支付',
  `remark` varchar(1000) DEFAULT '',
  `descnoset` tinyint(3) DEFAULT '0' COMMENT '是否使用统一说明',
  `display_order` int(11) DEFAULT '0',
  `supplier_uid` int(11) DEFAULT '0',
  `getcashier` tinyint(1) NOT NULL DEFAULT '0',
  `cashiersids` text,
  `cashiersnames` text,
  `category_ids` text,
  `categorynames` text,
  `goods_names` text,
  `goods_ids` text,
  `storeids` text,
  `storenames` text,
  `getstore` tinyint(1) NOT NULL DEFAULT '0',
  `getsupplier` tinyint(1) DEFAULT '0',
  `supplierids` text,
  `suppliernames` text,
  `createtime` int(11) DEFAULT '0',
  `created_at` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_catid` (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_coupon_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_coupon_category')."`;

CREATE TABLE `". tablename('yz_coupon_category')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `display_order` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_displayorder` (`display_order`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_coupon_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_coupon_log')."`;

CREATE TABLE `". tablename('yz_coupon_log')."` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_designer
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_designer')."`;

CREATE TABLE `". tablename('yz_designer')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号',
  `page_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '页面名称',
  `page_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '页面类型',
  `page_info` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '页面信息',
  `keyword` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '关键字',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认页面',
  `datas` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL COMMENT '页面创建时间',
  `updated_at` int(11) NOT NULL COMMENT '页面最后保存时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '页面删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_pagetype` (`page_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_designer_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_designer_menu')."`;

CREATE TABLE `". tablename('yz_designer_menu')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uniacid` int(11) DEFAULT '0' COMMENT '公众号ID',
  `menu_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '菜单名称',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '是否默认',
  `created_at` int(11) DEFAULT '0' COMMENT '创建时间',
  `menus` text COLLATE utf8mb4_unicode_ci COMMENT '菜单',
  `params` text COLLATE utf8mb4_unicode_ci COMMENT '参数',
  `updated_at` int(11) DEFAULT NULL COMMENT '修改时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_isdefault` (`is_default`),
  KEY `idx_createtime` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_dispatch
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_dispatch')."`;

CREATE TABLE `". tablename('yz_dispatch')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0' COMMENT '公众号id',
  `dispatch_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '配送模板名称',
  `display_order` int(11) DEFAULT '0' COMMENT '排序',
  `first_weight_price` int(10) unsigned DEFAULT '0' COMMENT '首重价格',
  `another_weight_price` decimal(11,0) DEFAULT '0' COMMENT '续重价格',
  `first_weight` int(11) DEFAULT '0' COMMENT '首重克数',
  `another_weight` int(11) DEFAULT '0' COMMENT '续重克数',
  `areas` text COLLATE utf8mb4_unicode_ci COMMENT '配送区域',
  `carriers` text COLLATE utf8mb4_unicode_ci,
  `enabled` tinyint(1) DEFAULT '0' COMMENT '是否显示（1：是；0：否）',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '是否默认模板（1：是；0：否）',
  `calculate_type` tinyint(1) DEFAULT '0' COMMENT '计费方式',
  `first_piece_price` int(11) DEFAULT '0' COMMENT '首件价格',
  `another_piece_price` int(11) DEFAULT '0' COMMENT '续件价格',
  `first_piece` int(11) DEFAULT '0' COMMENT '首件个数',
  `another_piece` int(11) DEFAULT '0' COMMENT '续件个数',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `is_plugin` int(11) NOT NULL DEFAULT '0' COMMENT '0 = 不是插件， 1 = 插件',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_dispatch_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_dispatch_type')."`;

CREATE TABLE `". tablename('yz_dispatch_type')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '配送方式',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `plugin` int(11) NOT NULL COMMENT '所属插件',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_goods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods')."`;

CREATE TABLE `". tablename('yz_goods')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `brand_id` int(11) NOT NULL COMMENT '品牌ID',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1为实体，2为虚拟',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1上架，0下架',
  `display_order` int(11) DEFAULT '0' COMMENT '排序',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '商品图',
  `thumb_url` text COMMENT '缩略图地址',
  `sku` varchar(5) DEFAULT '' COMMENT '商品单位 unit',
  `description` varchar(1000) DEFAULT '' COMMENT '分享描述',
  `content` text COMMENT '商品详情',
  `goods_sn` varchar(50) DEFAULT '' COMMENT '商品编号',
  `product_sn` varchar(50) DEFAULT '' COMMENT '商品条码',
  `market_price` decimal(14,2) DEFAULT '0.00' COMMENT '原价',
  `price` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '商品现价',
  `cost_price` decimal(14,2) DEFAULT '0.00' COMMENT '成本价',
  `stock` int(10) NOT NULL DEFAULT '0' COMMENT '商品库存 原total',
  `reduce_stock_method` int(11) DEFAULT '0' COMMENT '减库存方式 0 拍下减库存 1 付款减库存 2 永久不减  totalcnf',
  `show_sales` int(11) DEFAULT '0' COMMENT '已出售数量',
  `real_sales` int(11) DEFAULT '0' COMMENT '实际出售数量',
  `weight` decimal(10,2) DEFAULT '0.00' COMMENT '重量',
  `has_option` int(11) DEFAULT '0' COMMENT '启用商品规格 0 不启用 1 启用\n启用商品规格 0 不启用 1 启用',
  `is_new` tinyint(1) DEFAULT '0' COMMENT '新上',
  `is_hot` tinyint(1) DEFAULT '0' COMMENT '热卖',
  `is_discount` tinyint(1) DEFAULT '0' COMMENT '促销',
  `is_recommand` tinyint(1) DEFAULT '0' COMMENT '推荐',
  `is_comment` tinyint(1) DEFAULT '0' COMMENT '允许评论',
  `is_deleted` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `created_at` int(11) DEFAULT NULL COMMENT '建立时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '软删除',
  `updated_at` int(11) DEFAULT NULL COMMENT '更新时间',
  `comment_num` int(11) NOT NULL DEFAULT '0',
  `is_plugin` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`is_deleted`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_isnew` (`is_new`),
  KEY `idx_ishot` (`is_hot`),
  KEY `idx_isdiscount` (`is_discount`),
  KEY `idx_isrecommand` (`is_recommand`),
  KEY `idx_iscomment` (`is_comment`),
  KEY `idx_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_goods_area
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_area')."`;

CREATE TABLE `". tablename('yz_goods_area')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `area_id` int(11) NOT NULL COMMENT '商品区域id',
  PRIMARY KEY (`id`),
  KEY `idx_goodid` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品与商品区域插件关联表';



# Dump of table ims_yz_goods_bonus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_bonus')."`;

CREATE TABLE `". tablename('yz_goods_bonus')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `bonus_money` int(11) DEFAULT '0' COMMENT '分红金额',
  PRIMARY KEY (`id`),
  KEY `idx_good_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品与分红关联表';



# Dump of table ims_yz_goods_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_category')."`;

CREATE TABLE `". tablename('yz_goods_category')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) DEFAULT NULL COMMENT '商品ID',
  `category_id` int(11) DEFAULT NULL COMMENT '商品分类ID，最小的分类',
  `category_ids` varchar(255) DEFAULT NULL COMMENT '分类树，以逗号分割，冗余',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ims_yz_goods_commission
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_commission')."`;

CREATE TABLE `". tablename('yz_goods_commission')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `is_commission` int(11) DEFAULT NULL COMMENT '是否参与分销',
  `show_commission_button` tinyint(1) NOT NULL DEFAULT '0' COMMENT '显示\"我要分销\"按钮',
  `poster_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '海报图片',
  `has_commission` tinyint(1) DEFAULT '0' COMMENT '独立规则 1启用独立佣金比例',
  `first_level_rate` int(11) DEFAULT NULL COMMENT '一级分销 独立比例',
  `first_level_pay` decimal(14,2) DEFAULT NULL COMMENT '一级分销 独立固定金额',
  `second_level_rate` int(11) DEFAULT NULL COMMENT '二级分销 独立比例',
  `second_level_pay` decimal(14,2) DEFAULT NULL COMMENT '二级分销 独立固定金额',
  `third_level_rate` int(11) DEFAULT NULL COMMENT '三级分销 独立比例',
  `third_level_pay` decimal(14,2) DEFAULT NULL COMMENT '三级分销 独立固定金额',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_good_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_discount
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_discount')."`;

CREATE TABLE `". tablename('yz_goods_discount')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `level_discount_type` tinyint(1) NOT NULL COMMENT '等级折扣类型（1：会员等级；2：分销商等级；）',
  `discount_method` tinyint(1) NOT NULL COMMENT '折扣方式（1：折扣；2：固定金额）',
  `level_id` int(11) NOT NULL COMMENT '会员等级id',
  `discount_value` decimal(14,2) NOT NULL COMMENT '具体折扣数值 ',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_goodid` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_discount_detail
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_discount_detail')."`;

CREATE TABLE `". tablename('yz_goods_discount_detail')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL COMMENT '折扣id',
  `level_id` int(11) DEFAULT NULL COMMENT '等级id(折扣关联表中的折扣方式为会员等级，则为会员等级id，否则为分销商等级id)',
  `discount` decimal(3,2) DEFAULT NULL COMMENT '折扣值（0.01-9.99）%',
  `amount` int(11) DEFAULT NULL COMMENT '固定金额',
  PRIMARY KEY (`id`),
  KEY `idx_discountid` (`discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品折扣与商品折扣明细关联表';



# Dump of table ims_yz_goods_dispatch
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_dispatch')."`;

CREATE TABLE `". tablename('yz_goods_dispatch')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `dispatch_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '运费设置  1:统一运费 2:运费模板',
  `dispatch_price` int(11) DEFAULT '0' COMMENT '统一运费金额',
  `dispatch_id` int(11) DEFAULT NULL COMMENT '运费模板ID',
  `is_cod` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否支持货到付款 1:不支持2：支持',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_good_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_diyform
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_diyform')."`;

CREATE TABLE `". tablename('yz_goods_diyform')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `good_id` int(11) NOT NULL COMMENT '商品id',
  `diyform_id` int(11) DEFAULT NULL COMMENT '自定义表单id',
  `diyform_enable` tinyint(1) DEFAULT '0' COMMENT '自定义表单开关（0：关闭；1：开启；默认：0）',
  PRIMARY KEY (`id`),
  KEY `idx_goodid` (`good_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品与自定义表单关联表';



# Dump of table ims_yz_goods_level_returns
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_level_returns')."`;

CREATE TABLE `". tablename('yz_goods_level_returns')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `good_return_id` int(11) NOT NULL COMMENT '返现商品ID',
  `level_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '等级类型 1：会员等级2：分销商等级',
  `level_id` int(11) NOT NULL COMMENT '级别ID',
  `amount` int(11) NOT NULL COMMENT '返现金额',
  PRIMARY KEY (`id`),
  KEY `idx_good_return_id` (`good_return_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品等级返现规则表';



# Dump of table ims_yz_goods_notices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_notices')."`;

CREATE TABLE `". tablename('yz_goods_notices')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL COMMENT '商家通知 uid',
  `type` tinyint(1) DEFAULT NULL COMMENT '通知方式 0:下单通知1：付款通知2:买家收货通知',
  PRIMARY KEY (`id`),
  KEY `idx_good_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_option
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_option')."`;

CREATE TABLE `". tablename('yz_goods_option')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '规格ID',
  `uniacid` int(11) DEFAULT '0' COMMENT '所属公众号ID',
  `goods_id` int(10) DEFAULT '0' COMMENT '所属商品ID',
  `title` varchar(50) DEFAULT NULL COMMENT '拼接规格名称，冗余',
  `thumb` varchar(60) DEFAULT NULL COMMENT '此规格展示图片',
  `product_price` decimal(10,2) DEFAULT '0.00' COMMENT '此规格现价',
  `market_price` decimal(10,2) DEFAULT '0.00' COMMENT '此规格原价',
  `cost_price` decimal(10,2) DEFAULT '0.00' COMMENT '此规格成本',
  `stock` int(11) DEFAULT '0' COMMENT '此规格库存',
  `weight` decimal(10,2) DEFAULT '0.00' COMMENT '重量',
  `display_order` int(11) DEFAULT '0' COMMENT '排序',
  `specs` text COMMENT '规格项ID组合编号',
  `skuId` varchar(255) DEFAULT '',
  `goods_sn` varchar(255) DEFAULT '' COMMENT '商品编码',
  `product_sn` varchar(255) DEFAULT '' COMMENT '产品编码',
  `virtual` int(11) DEFAULT '0',
  `red_price` varchar(50) DEFAULT '' COMMENT '红包价格',
  `created_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_goodsid` (`goods_id`),
  KEY `idx_displayorder` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_goods_param
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_param')."`;

CREATE TABLE `". tablename('yz_goods_param')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0' COMMENT '店铺ID',
  `goods_id` int(10) DEFAULT '0' COMMENT '商品ID',
  `title` varchar(50) DEFAULT NULL COMMENT '参数名',
  `value` text COMMENT '参数值',
  `displayorder` int(11) DEFAULT '0' COMMENT '排序',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_goodsid` (`goods_id`),
  KEY `idx_displayorder` (`displayorder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_goods_privilege
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_privilege')."`;

CREATE TABLE `". tablename('yz_goods_privilege')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `show_levels` text COLLATE utf8mb4_unicode_ci COMMENT '会员等级浏览权限',
  `show_groups` text COLLATE utf8mb4_unicode_ci COMMENT '会员组浏览权限',
  `buy_levels` text COLLATE utf8mb4_unicode_ci COMMENT '会员等级购买权限',
  `buy_groups` text COLLATE utf8mb4_unicode_ci COMMENT '会员组购买权限',
  `once_buy_limit` int(11) DEFAULT '0' COMMENT '每次限购数量',
  `total_buy_limit` int(11) DEFAULT '0' COMMENT '总共限购数量',
  `time_begin_limit` int(11) DEFAULT NULL COMMENT '限购开始时间',
  `time_end_limit` int(11) DEFAULT NULL COMMENT '限购结束时间',
  `enable_time_limit` tinyint(1) NOT NULL COMMENT '限购开关（1：开启限购；0：关闭限购）',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_goodid` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_return
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_return')."`;

CREATE TABLE `". tablename('yz_goods_return')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `good_id` int(11) NOT NULL,
  `is_level_return` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否参与等级返现',
  `level_return_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '等级返现类型 1：会员等级 2：分销商等级',
  `is_order_return` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否参与订单全返',
  `is_queue_return` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否参与排列全返',
  `add_pool_amount` int(11) DEFAULT '0' COMMENT '加入分红资金池',
  PRIMARY KEY (`id`),
  KEY `idx_good_id` (`good_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品与全返关联表';



# Dump of table ims_yz_goods_sale
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_sale')."`;

CREATE TABLE `". tablename('yz_goods_sale')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `max_point_deduct` int(11) DEFAULT '0' COMMENT '积分抵扣 最多抵扣',
  `max_balance_deduct` int(11) DEFAULT '0' COMMENT '余额抵扣 最多抵扣',
  `is_sendfree` int(11) DEFAULT '0' COMMENT '是否包邮',
  `ed_num` int(11) DEFAULT '0' COMMENT '单品满件包邮 件数',
  `ed_money` int(11) DEFAULT '0' COMMENT '单品满额包邮 金额',
  `ed_areas` text COLLATE utf8mb4_unicode_ci COMMENT '不参与单品包邮地区',
  `point` int(11) DEFAULT '0' COMMENT '赠送积分',
  `bonus` int(11) DEFAULT '0' COMMENT '红包',
  PRIMARY KEY (`id`),
  KEY `idx_good_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_share
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_share')."`;

CREATE TABLE `". tablename('yz_goods_share')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `need_follow` tinyint(1) DEFAULT NULL COMMENT '强制关注',
  `no_follow_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '未关注提示消息',
  `follow_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '关注引导信息',
  `share_title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分享标题',
  `share_thumb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分享图片',
  `share_desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分享描述',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_goodid` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_spec
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_spec')."`;

CREATE TABLE `". tablename('yz_goods_spec')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0' COMMENT '公众号ID',
  `goods_id` int(11) DEFAULT '0' COMMENT '商品ID',
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `description` varchar(1000) DEFAULT NULL COMMENT '介绍',
  `display_type` tinyint(3) DEFAULT '0' COMMENT '显示类型',
  `content` text COMMENT '内容',
  `display_order` int(11) DEFAULT '0' COMMENT '排序',
  `propId` varchar(255) DEFAULT NULL COMMENT '淘宝插件',
  `created_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_goodsid` (`goods_id`),
  KEY `idx_displayorder` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_goods_spec_item
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_goods_spec_item')."`;

CREATE TABLE `". tablename('yz_goods_spec_item')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0' COMMENT '公众号ID',
  `specid` int(11) DEFAULT '0' COMMENT '规格ID',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `thumb` varchar(255) DEFAULT NULL COMMENT '图片',
  `show` int(11) DEFAULT '0' COMMENT '显示',
  `display_order` int(11) DEFAULT '0' COMMENT '排序',
  `valueId` varchar(255) DEFAULT NULL COMMENT '淘宝插件',
  `virtual` int(11) DEFAULT '0' COMMENT '虚拟物品',
  `created_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_specid` (`specid`),
  KEY `idx_show` (`show`),
  KEY `idx_displayorder` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_member
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member')."`;

CREATE TABLE `". tablename('yz_member')."` (
  `member_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL COMMENT '统一公众号',
  `parent_id` int(11) DEFAULT NULL COMMENT '上级ID',
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户组ID',
  `level_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员等级ID',
  `inviter` int(11) DEFAULT '0' COMMENT '分享链接邀请人',
  `is_black` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-普通会员;1-黑名单会员',
  `province_name` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '省',
  `city_name` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '市',
  `area_name` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '区',
  `province` int(11) DEFAULT '0' COMMENT '省ID',
  `city` int(11) DEFAULT '0' COMMENT '市ID',
  `area` int(11) DEFAULT '0' COMMENT '区ID',
  `address` text COLLATE utf8mb4_unicode_ci COMMENT '详细地址',
  `referralsn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '推荐码',
  `is_agent` tinyint(1) DEFAULT '0' COMMENT '是否有推广资格',
  `alipayname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '支付宝姓名',
  `alipay` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '支付宝账号',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `status` int(11) DEFAULT '0' COMMENT '推广审核状态0-未申请；1-审核中；2-审核通过',
  `child_time` int(11) DEFAULT '0' COMMENT '成为下线时间',
  `agent_time` int(11) DEFAULT '0' COMMENT '获取发展下线资格时间',
  `apply_time` int(11) DEFAULT '0' COMMENT '资格申请时间',
  `relaton` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '会员3级关系链',
  KEY `idx_member_id` (`member_id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_app_wechat
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_app_wechat')."`;

CREATE TABLE `". tablename('yz_member_app_wechat')."` (
  `app_wechat_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL COMMENT '统一公众号ID',
  `member_id` int(11) NOT NULL COMMENT '用户uid',
  `openid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '唯一用户标识',
  `nickname` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '昵称',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '头像',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别 0-男；1-女',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`app_wechat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_cart
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_cart')."`;

CREATE TABLE `". tablename('yz_member_cart')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `member_id` int(11) NOT NULL COMMENT '会员id',
  `uniacid` int(11) NOT NULL COMMENT '所属公众号id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `total` int(11) NOT NULL COMMENT '加入购物车数量',
  `option_id` int(11) NOT NULL COMMENT '商品规格id',
  `created_at` int(11) NOT NULL COMMENT '加入购物车时间',
  `updated_at` int(11) NOT NULL COMMENT '最后一次修改时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '移除购物车时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_coupon
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_coupon')."`;

CREATE TABLE `". tablename('yz_member_coupon')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `uid` varchar(255) DEFAULT '',
  `coupon_id` int(11) DEFAULT '0',
  `get_type` tinyint(3) DEFAULT '0' COMMENT '获取方式 0 发放, 1 领取, 2 积分商城',
  `used` int(11) DEFAULT '0',
  `use_time` int(11) DEFAULT '0',
  `get_time` int(11) DEFAULT '0' COMMENT '获取优惠券的时间',
  `send_uid` int(11) DEFAULT '0',
  `order_sn` varchar(255) DEFAULT '' COMMENT '使用优惠券的订单号',
  `back` tinyint(3) DEFAULT '0',
  `back_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_couponid` (`coupon_id`),
  KEY `idx_gettype` (`get_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_member_favorite
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_favorite')."`;

CREATE TABLE `". tablename('yz_member_favorite')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `member_id` int(11) NOT NULL COMMENT '会员id',
  `uniacid` int(11) NOT NULL COMMENT '所属公众号id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_group')."`;

CREATE TABLE `". tablename('yz_member_group')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uniacid` int(11) NOT NULL COMMENT '所属公众号',
  `group_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分组名称',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `is_default` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_history')."`;

CREATE TABLE `". tablename('yz_member_history')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `member_id` int(11) NOT NULL COMMENT '会员id',
  `uniacid` int(11) NOT NULL COMMENT '所属公众号id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `updated_at` int(11) NOT NULL COMMENT '修改时间，最后一次浏览时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '删除时间，为空则未删除状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_income
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_income')."`;

CREATE TABLE `". tablename('yz_member_income')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '店铺ID',
  `member_id` int(11) NOT NULL COMMENT '会员ID',
  `incometable_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '收入类型',
  `incometable_id` int(11) DEFAULT NULL,
  `type_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '类型名称',
  `amount` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '收入金额',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态；0未提现，1已提现',
  `pay_status` tinyint(3) NOT NULL DEFAULT '0',
  `detail` text COLLATE utf8mb4_unicode_ci COMMENT '收入明细（json）',
  `create_month` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_level
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_level')."`;

CREATE TABLE `". tablename('yz_member_level')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uniacid` int(11) NOT NULL COMMENT '所属公众号',
  `level` int(11) NOT NULL COMMENT '等级权重，数值越大权重越高',
  `level_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '会员等级名称',
  `order_money` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '升级条件，订单满足金额值',
  `order_count` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '升级条件，满足订单数量值',
  `goods_id` int(11) DEFAULT NULL COMMENT '升级条件，购买指定商品升级',
  `discount` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '等级享受折扣',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `is_default` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_mini_app
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_mini_app')."`;

CREATE TABLE `". tablename('yz_member_mini_app')."` (
  `mini_app_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '统一公众号ID',
  `member_id` int(11) NOT NULL COMMENT '用户uid',
  `openid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户唯一标识',
  `nickname` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '昵称',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '头像',
  `gender` tinyint(1) NOT NULL COMMENT '性别0-男；1-女',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`mini_app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_qq
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_qq')."`;

CREATE TABLE `". tablename('yz_member_qq')."` (
  `qq_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '统一公众号ID',
  `member_id` int(11) NOT NULL,
  `nickname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '昵称',
  `figureurl` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '大小为30×30像素的QQ空间头像URL',
  `figureurl_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '大小为50×50像素的QQ空间头像URL',
  `figureurl_2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '大小为100×100像素的QQ空间头像URL。',
  `figureurl_qq_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '大小为40×40像素的QQ头像URL。',
  `figureurl_qq_2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '大小为100×100像素的QQ头像URL。需要注意，不是所有的用户都拥有QQ的100x100的头像，但40x40像素则是一定会有。',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别 0-男；1-女',
  `is_yellow_year_vip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '标识用户是否为黄钻用户 0- 不是；1-是',
  `vip` int(11) NOT NULL DEFAULT '0' COMMENT '标识用户是否为黄钻用户0－不是；1－是',
  `yellow_vip_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '黄钻等级',
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '黄钻等级',
  `is_yellow_vip` tinyint(1) NOT NULL DEFAULT '0' COMMENT '识是否为年费黄钻用户 0－不是； 1－是',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`qq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_relation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_relation')."`;

CREATE TABLE `". tablename('yz_member_relation')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uniacid` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否启用关系链 0-关闭；1-开启',
  `become` tinyint(1) NOT NULL DEFAULT '0' COMMENT '成为分销商条件 0-无条件；1-申请；2-消费x次；3-消费x元；4-购买商品',
  `become_order` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费条件统计的方式 0-付款后；1-完成后',
  `become_child` tinyint(1) NOT NULL DEFAULT '0' COMMENT '成为下线条件 0-分享链接；1-首次下单；2-首次付款',
  `become_ordercount` int(11) DEFAULT '0' COMMENT '消费x次',
  `become_moneycount` decimal(5,2) DEFAULT '0.00' COMMENT '消费x元',
  `become_goods_id` int(11) DEFAULT '0' COMMENT '购买的商品',
  `become_info` tinyint(1) NOT NULL DEFAULT '1' COMMENT '完善信息 0-不需要；1-需要',
  `become_check` tinyint(1) NOT NULL DEFAULT '1' COMMENT '成为分销商是否需要审核 0-不需要；1-需要',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_unique
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_unique')."`;

CREATE TABLE `". tablename('yz_member_unique')."` (
  `unique_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '统一公众号',
  `unionid` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '统一微信开放平台unionid',
  `member_id` int(11) NOT NULL COMMENT '统一用户ID',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '终端类型 0-公众号；1-小程序；2-微信app；3-扫码',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`unique_id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_unionid` (`unionid`),
  KEY `idx_member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_wechat
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_member_wechat')."`;

CREATE TABLE `". tablename('yz_member_wechat')."` (
  `wechat_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '统一公众号ID',
  `member_id` int(11) NOT NULL COMMENT '用户uid',
  `openid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '唯一用户标识',
  `nickname` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '昵称',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别 0-男；1-女',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '头像',
  `province` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '省',
  `city` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '市',
  `country` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '国家',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`wechat_id`),
  KEY `idx_member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_menu')."`;

CREATE TABLE `". tablename('yz_menu')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `item` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标识',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '路由或链接地址',
  `url_params` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '路由参数',
  `permit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '权限控制 1是 0否',
  `menu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '菜单显示 1是 0否',
  `icon` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图标',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '上级',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 1启用 0禁用',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_options
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_options')."`;

CREATE TABLE `". tablename('yz_options')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `option_value` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_order')."`;

CREATE TABLE `". tablename('yz_order')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '订单ID',
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号ID',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'mc_member uid',
  `order_sn` varchar(23) NOT NULL DEFAULT '' COMMENT '订单号',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单最终金额',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品最终金额',
  `goods_total` int(11) NOT NULL DEFAULT '1' COMMENT '订单商品总数',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '-1取消状态，0待支付，1为已付款，2为已发货，3为成功',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '下单时间',
  `is_deleted` tinyint(3) NOT NULL DEFAULT '0' COMMENT '删除',
  `is_member_deleted` tinyint(3) NOT NULL DEFAULT '0' COMMENT '用户删除',
  `finish_time` int(11) NOT NULL DEFAULT '0' COMMENT '交易完成时间',
  `pay_time` int(11) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `send_time` int(11) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `cancel_time` int(11) NOT NULL DEFAULT '0' COMMENT '取消时间',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `cancel_pay_time` int(11) NOT NULL DEFAULT '0' COMMENT '取消支付时间',
  `cancel_send_time` int(11) NOT NULL DEFAULT '0' COMMENT '取消发货时间',
  `dispatch_type_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '配送方式id',
  `dispatch_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费价格',
  `discount_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单优惠价格',
  `pay_type_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '支付方式id',
  `order_goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单商品销售价',
  `deduction_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '抵扣金额',
  `refund_id` int(11) NOT NULL DEFAULT '0',
  `is_plugin` int(11) NOT NULL DEFAULT '0' COMMENT '0 = 不是插件， 1 = 插件',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_address
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_order_address')."`;

CREATE TABLE `". tablename('yz_order_address')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `address` varchar(255) NOT NULL DEFAULT '0' COMMENT '地址',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '姓名',
  `updated_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_change_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_order_change_log')."`;

CREATE TABLE `". tablename('yz_order_change_log')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_role` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_express
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_order_express')."`;

CREATE TABLE `". tablename('yz_order_express')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `express_company_name` varchar(50) NOT NULL DEFAULT '0',
  `express_sn` varchar(50) NOT NULL DEFAULT '0',
  `express_code` varchar(20) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_goods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_order_goods')."`;

CREATE TABLE `". tablename('yz_order_goods')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号id',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `total` int(11) NOT NULL DEFAULT '1' COMMENT '订单商品件数',
  `create_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '真实价格',
  `goods_sn` varchar(50) NOT NULL DEFAULT '' COMMENT '商品编码',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '会员身份标识',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '商品缩略图',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '商品标题',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品快照价格',
  `goods_option_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品规格id',
  `goods_option_title` varchar(50) NOT NULL DEFAULT '' COMMENT '商品规格标题',
  `product_sn` varchar(23) NOT NULL DEFAULT '' COMMENT '产品条码',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `discount_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠金额',
  `comment_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '评价状态(0未评价,1已评价)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_ORDER_ID_GOODS_ID` (`order_id`,`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_goods_change_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_order_goods_change_log')."`;

CREATE TABLE `". tablename('yz_order_goods_change_log')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_goods_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_mapping
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_order_mapping')."`;

CREATE TABLE `". tablename('yz_order_mapping')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_order_id` int(11) NOT NULL COMMENT '旧商城的订单ID',
  `new_order_id` int(11) NOT NULL COMMENT '重构商城对应的订单ID',
  `old_openid` char(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '旧商城的用户openid',
  `new_member_id` int(11) NOT NULL COMMENT '重构商城对应的member_id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_order_operation_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_order_operation_log')."`;

CREATE TABLE `". tablename('yz_order_operation_log')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0' COMMENT '订单id',
  `before_operation_status` tinyint(1) DEFAULT '0' COMMENT '操作前订单状态',
  `after_operation_status` tinyint(1) DEFAULT '0' COMMENT '操作后订单状态',
  `operator` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '操作人',
  `operation_time` int(11) DEFAULT '0' COMMENT '操作时间',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT NULL,
  `type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_order_refund
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_order_refund')."`;

CREATE TABLE `". tablename('yz_order_refund')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号ID',
  `uid` int(11) NOT NULL COMMENT '会员id',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `refund_sn` varchar(255) NOT NULL DEFAULT '' COMMENT '退单编号',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '原因',
  `images` text NOT NULL COMMENT '图片',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(11) DEFAULT '0' COMMENT '建立时间',
  `status` tinyint(3) DEFAULT '0' COMMENT '0 申请; 1 通过; 2 驳回;',
  `reply` text COMMENT '回复',
  `refund_way_type` tinyint(3) DEFAULT '0' COMMENT '类型 0 退回帐户余额 1 退款和退余额混合 2 整单退款',
  `apply_price` decimal(10,2) DEFAULT '0.00' COMMENT '申请金额',
  `order_price` decimal(10,2) DEFAULT '0.00' COMMENT '订单金额',
  `refund_type` tinyint(1) DEFAULT '0' COMMENT '申请类型 0 退款(仅退款不退货) 1 退款退货 2 换货',
  `refund_proof_imgs` text COMMENT '退款申请时上传凭证',
  `refund_time` int(11) DEFAULT '0' COMMENT '返回时间',
  `refund_address` text COMMENT '退换货地址',
  `remark` text COMMENT '卖家留言',
  `operate_time` int(11) DEFAULT '0' COMMENT '操作时间',
  `send_time` int(11) DEFAULT '0' COMMENT '发送时间',
  `return_time` int(11) DEFAULT '0' COMMENT '返回时间',
  `refund_express_company_code` varchar(100) NOT NULL DEFAULT '' COMMENT '退货快递简称',
  `refund_express_company_name` varchar(100) NOT NULL DEFAULT '' COMMENT '退货快递公司',
  `refund_express_sn` varchar(100) NOT NULL DEFAULT '' COMMENT '退货快递单号',
  `refund_address_id` int(11) NOT NULL DEFAULT '0' COMMENT '退换货地址ID',
  `end_time` int(11) DEFAULT '0' COMMENT '结束时间',
  `alipay_batch_sn` varchar(255) DEFAULT '' COMMENT '支付宝批次号',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_create_time` (`create_time`),
  KEY `idx_shop_id` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_remark
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_order_remark')."`;

CREATE TABLE `". tablename('yz_order_remark')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `remark` char(255) NOT NULL,
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_pay_access_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_pay_access_log')."`;

CREATE TABLE `". tablename('yz_pay_access_log')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uniacid` int(11) NOT NULL COMMENT '统一公众号',
  `member_id` int(11) NOT NULL COMMENT '用户ID',
  `url` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '访问地址',
  `http_method` char(7) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HTTP数据传输方法',
  `ip` varchar(135) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '远程客户端的IP主机地址',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_pay_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_pay_log')."`;

CREATE TABLE `". tablename('yz_pay_log')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uniacid` int(11) NOT NULL COMMENT '统一公众号',
  `member_id` int(11) NOT NULL COMMENT '用户ID',
  `type` tinyint(4) NOT NULL COMMENT '支付种类 1-订单 2-充值 3-提现 4-退款',
  `third_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付方式 ',
  `price` decimal(14,2) NOT NULL COMMENT '支付金额',
  `operation` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT ' 操作内容',
  `ip` varchar(135) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '远程客户端的IP主机地址',
  `created_at` int(13) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(13) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_pay_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_pay_order')."`;

CREATE TABLE `". tablename('yz_pay_order')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uniacid` int(11) NOT NULL COMMENT '统一公众号',
  `member_id` int(11) NOT NULL COMMENT '用户ID',
  `int_order_no` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '支付单号',
  `out_order_no` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '订单号',
  `trade_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '第三方支付单号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态 0-未支付；1-待支付；2-已支付',
  `price` decimal(14,2) NOT NULL COMMENT '支付金额',
  `type` tinyint(1) NOT NULL COMMENT '支付类型(1支付、2充值)',
  `third_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '第三方支付类型',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更像时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_member_id` (`member_id`),
  KEY `idx_order_no` (`out_order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_pay_request_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_pay_request_data')."`;

CREATE TABLE `". tablename('yz_pay_request_data')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uniacid` int(11) NOT NULL COMMENT '统一公众号',
  `out_order_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '订单号',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付种类 1-订单支付 2-￥',
  `third_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付类型 1-微信；2-支付宝；3-余额',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '请求数据',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_pay_response_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_pay_response_data')."`;

CREATE TABLE `". tablename('yz_pay_response_data')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uniacid` int(11) NOT NULL COMMENT '统一公众号',
  `out_order_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '订单号',
  `third_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付类型 1-微信；2-支付宝；3-余额',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '请求数据',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_pay_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_pay_type')."`;

CREATE TABLE `". tablename('yz_pay_type')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '支付方式',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `plugin_id` int(11) NOT NULL COMMENT '所属插件',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_permission
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_permission')."`;

CREATE TABLE `". tablename('yz_permission')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL COMMENT '1:user 2:role 3:account',
  `item_id` int(11) NOT NULL COMMENT '目标ID:user_id role_id uniacid',
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_plugin_article
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_plugin_article')."`;

CREATE TABLE `". tablename('yz_plugin_article')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号ID',
  `category_id` int(11) NOT NULL COMMENT '分类ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题',
  `desc` text COMMENT '文章介绍（封面）',
  `thumb` text COMMENT '文章图片（封面）',
  `content` longtext NOT NULL COMMENT '文章内容',
  `virtual_created_at` int(11) DEFAULT NULL COMMENT '虚拟发布时间',
  `author` varchar(20) NOT NULL DEFAULT '' COMMENT '发布作者',
  `virtual_read_num` int(11) DEFAULT NULL COMMENT '虚拟阅读数',
  `read_num` int(11) NOT NULL DEFAULT '0' COMMENT '真是阅读数',
  `virtual_like_num` int(11) DEFAULT NULL COMMENT '虚拟点赞数',
  `like_num` int(11) NOT NULL DEFAULT '0' COMMENT '真是点赞数',
  `link` varchar(255) DEFAULT NULL COMMENT '原文链接',
  `per_person_per_day` int(11) DEFAULT NULL COMMENT '每人每天参与次数',
  `total_per_person` int(11) DEFAULT NULL COMMENT '所有参与次数',
  `point` int(11) DEFAULT NULL COMMENT '赠送积分',
  `credit` int(11) DEFAULT NULL COMMENT '赠送余额',
  `bonus_total` int(11) DEFAULT NULL COMMENT '累计奖金',
  `bonus_total_now` int(11) DEFAULT NULL COMMENT '截止目前累计奖励金额',
  `no_copy_url` tinyint(1) DEFAULT NULL COMMENT '页面禁止复制url',
  `no_share` tinyint(1) DEFAULT NULL COMMENT '页面禁止分享到朋友圈',
  `no_share_to_friend` tinyint(1) DEFAULT NULL COMMENT '页面禁止分享给好友',
  `keyword` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `report_enabled` tinyint(1) DEFAULT NULL COMMENT '举报开关',
  `advs_type` tinyint(1) DEFAULT NULL COMMENT '营销产品显示类型设置',
  `advs_title` varchar(255) DEFAULT NULL COMMENT '营销产品显示标题',
  `advs_title_footer` varchar(255) DEFAULT NULL COMMENT '营销产品底部标题',
  `advs_link` varchar(255) DEFAULT NULL COMMENT '营销产品底部链接',
  `advs` text COMMENT '营销产品具体信息',
  `state` tinyint(1) DEFAULT NULL COMMENT '启用状态',
  `state_wechat` tinyint(1) DEFAULT NULL COMMENT '微信启用状态',
  `created_at` int(11) DEFAULT NULL COMMENT '文章发布时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '文章更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_plugin_article_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_plugin_article_category')."`;

CREATE TABLE `". tablename('yz_plugin_article_category')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号ID',
  `name` varchar(50) DEFAULT NULL COMMENT '分类名称',
  `member_level_limit` int(11) DEFAULT NULL COMMENT '会员等级阅读权限',
  `commission_level_limit` int(11) DEFAULT NULL COMMENT '分销商等级浏览权限',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_plugin_article_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_plugin_article_log')."`;

CREATE TABLE `". tablename('yz_plugin_article_log')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL COMMENT '文章ID',
  `read_num` int(11) DEFAULT NULL COMMENT '阅读数',
  `like_num` int(11) DEFAULT NULL COMMENT '点赞数',
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_plugin_article_report
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_plugin_article_report')."`;

CREATE TABLE `". tablename('yz_plugin_article_report')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号ID',
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `article_id` int(11) DEFAULT NULL COMMENT '文章ID',
  `type` varchar(255) DEFAULT NULL COMMENT '违规分类',
  `desc` varchar(255) DEFAULT NULL COMMENT '举报描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_plugin_article_share
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_plugin_article_share')."`;

CREATE TABLE `". tablename('yz_plugin_article_share')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号ID',
  `article_id` int(11) DEFAULT NULL COMMENT '文章ID',
  `share_uid` int(11) DEFAULT NULL COMMENT '分享人',
  `click_uid` int(11) DEFAULT NULL COMMENT '点击人',
  `click_time` int(11) DEFAULT NULL COMMENT '点击时间',
  `point` int(11) DEFAULT NULL COMMENT '添加的积分',
  `credit` text COMMENT '添加的余额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_plugin_goods_assistant
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_plugin_goods_assistant')."`;

CREATE TABLE `". tablename('yz_plugin_goods_assistant')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `uniacid` int(11) NOT NULL COMMENT '公众号ID',
  `itemid` varchar(255) DEFAULT NULL COMMENT '抓取商品ItemID',
  `source` varchar(255) DEFAULT NULL COMMENT '商品资源  taobao\\jingdong\\alibaba',
  `url` varchar(255) DEFAULT NULL COMMENT '商品地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_point_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_point_log')."`;

CREATE TABLE `". tablename('yz_point_log')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员id',
  `point` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '收入or支出多少积分',
  `point_income_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1 = 收入 -1 = 支出',
  `point_mode` tinyint(5) NOT NULL DEFAULT '0' COMMENT '获取方式',
  `before_point` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '改变前积分',
  `after_point` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '改变后积分',
  `remark` varchar(255) NOT NULL DEFAULT '0' COMMENT '备注',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_poster
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_poster')."`;

CREATE TABLE `". tablename('yz_poster')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL COMMENT '微信公众号 ID',
  `title` varchar(50) NOT NULL COMMENT '海报名称',
  `type` tinyint(4) NOT NULL COMMENT '海报类型: 1为活动海报, 2为长期海报',
  `keyword` varchar(30) NOT NULL COMMENT '关键字(不超过30个字符)',
  `time_start` int(10) unsigned DEFAULT '0' COMMENT '活动开始时间(如果是长期海报,值为0)',
  `time_end` int(10) unsigned DEFAULT '0' COMMENT '活动结束时间(如果是长期海报,值为0)',
  `background` varchar(255) DEFAULT '' COMMENT '海报背景图片',
  `style_data` text NOT NULL COMMENT '海报设计样式数据',
  `response_title` varchar(50) DEFAULT '' COMMENT '推送图文的标题',
  `response_thumb` varchar(255) DEFAULT '' COMMENT '推送图文的封面',
  `response_desc` varchar(255) DEFAULT '' COMMENT '推送图文的文字描述',
  `response_url` varchar(255) DEFAULT '' COMMENT '推送图文的链接',
  `is_open` tinyint(4) DEFAULT '0' COMMENT '是否允许没有发展下线资格的人生成海报(默认不允许)',
  `auto_sub` tinyint(4) DEFAULT '1' COMMENT '扫码关注是否自动成为下线(默认为\"是\")',
  `status` tinyint(4) DEFAULT '1' COMMENT '是否启用: 0为不启用, 1为启用 (默认启用)',
  `created_at` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_poster_award
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_poster_award')."`;

CREATE TABLE `". tablename('yz_poster_award')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL COMMENT '公众号 ID',
  `poster_id` int(10) unsigned NOT NULL COMMENT '海报 ID (yz_poster数据表)',
  `subscriber_memberid` int(10) unsigned NOT NULL,
  `recommender_memberid` int(10) unsigned NOT NULL,
  `recommender_credit` int(10) unsigned DEFAULT '0' COMMENT '推荐者获得的积分奖励数量',
  `recommender_bonus` int(10) unsigned DEFAULT '0' COMMENT '推荐者获得的现金奖励金额',
  `recommender_coupon_id` int(10) unsigned DEFAULT '0' COMMENT '推荐者获得的优惠券奖励 - 优惠券的ID',
  `recommender_coupon_num` int(10) unsigned DEFAULT '0' COMMENT '推荐者获得的优惠券奖励 - 优惠券的张数',
  `subscriber_credit` int(10) unsigned DEFAULT '0' COMMENT '关注者获得的积分奖励数量',
  `subscriber_bonus` int(10) unsigned DEFAULT '0' COMMENT '关注者获得的现金奖金额',
  `subscriber_coupon_id` int(10) unsigned DEFAULT '0' COMMENT '关注者获得的优惠券奖励 - 优惠券的ID',
  `subscriber_coupon_num` int(10) unsigned DEFAULT '0' COMMENT '关注者获得的优惠券奖励 - 优惠券的张数',
  `created_at` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_subscriber_memberid` (`subscriber_memberid`),
  KEY `idx_recommender_memberid` (`recommender_memberid`),
  KEY `poster_id` (`poster_id`),
  CONSTRAINT `". tablename('yz_poster_award_ibfk_1')."` FOREIGN KEY (`poster_id`) REFERENCES `". tablename('yz_poster')."` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_poster_qrcode
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_poster_qrcode')."`;

CREATE TABLE `". tablename('yz_poster_qrcode')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL COMMENT '公众号 ID',
  `poster_id` int(10) unsigned NOT NULL COMMENT '海报 ID (yz_poster数据表)',
  `qrcode_id` int(10) unsigned NOT NULL COMMENT '二维码 ID (qrcode数据表)',
  `memberid` int(11) unsigned NOT NULL COMMENT '生成二维码的用户(推荐者)ID',
  `created_at` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_posterid` (`poster_id`),
  KEY `idx_qrcodeid` (`qrcode_id`),
  CONSTRAINT `". tablename('yz_poster_qrcode_ibfk_1')."` FOREIGN KEY (`poster_id`) REFERENCES `". tablename('yz_poster')."` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_poster_scan
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_poster_scan')."`;

CREATE TABLE `". tablename('yz_poster_scan')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL COMMENT '公众号 ID',
  `poster_id` int(10) unsigned NOT NULL COMMENT '海报 ID (yz_poster数据表)',
  `subscriber_memberid` int(10) unsigned NOT NULL COMMENT '扫码关注者的 memberid',
  `recommender_memberid` int(10) unsigned NOT NULL COMMENT '推荐者的 memberid',
  `event_type` tinyint(4) NOT NULL COMMENT '扫码的事件类型, 1 为 subscribe, 2 为 scan',
  `sign_up_this_time` tinyint(4) NOT NULL COMMENT '扫码者是否在本次扫码中注册商城, 0 为\"否\", 1 为\"是\"',
  `created_at` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_posterid` (`poster_id`),
  KEY `idx_subscriber_memberid` (`subscriber_memberid`),
  KEY `idx_recommender_memberid` (`recommender_memberid`),
  CONSTRAINT `". tablename('yz_poster_scan_ibfk_1')."` FOREIGN KEY (`poster_id`) REFERENCES `". tablename('yz_poster')."` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_poster_supplement
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_poster_supplement')."`;

CREATE TABLE `". tablename('yz_poster_supplement')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poster_id` int(10) unsigned NOT NULL COMMENT '海报 ID (yz_poster数据表)',
  `not_start_reminder` varchar(140) DEFAULT '' COMMENT '活动还未开始时的提示',
  `finish_reminder` varchar(140) DEFAULT '' COMMENT '活动已经结束时的提示',
  `wait_reminder` varchar(140) DEFAULT '' COMMENT '等待海报生成时的提示',
  `not_open_reminder` varchar(140) DEFAULT '' COMMENT '未开放权限时的提示',
  `not_open_reminder_url` varchar(255) DEFAULT '' COMMENT '未开放权限时的说明链接',
  `recommender_credit` int(10) unsigned DEFAULT '0' COMMENT '奖励给推荐者的积分',
  `recommender_bonus` decimal(14,2) unsigned DEFAULT '0.00' COMMENT '奖励给推荐者的现金(单位为元, 字段容量限制在999.99以下)',
  `recommender_coupon_id` int(10) unsigned DEFAULT '0' COMMENT '奖励给推荐者的优惠券的 ID',
  `recommender_coupon_name` varchar(15) DEFAULT '' COMMENT '奖励给推荐者的优惠券的名称',
  `recommender_coupon_num` int(10) unsigned DEFAULT '0' COMMENT '奖励给推荐者的优惠券的张数',
  `subscriber_credit` int(10) unsigned DEFAULT '0' COMMENT '奖励给关注者的积分',
  `subscriber_bonus` decimal(14,2) unsigned DEFAULT '0.00' COMMENT '奖励给关注者的现金(单位为元, 字段容量限制在999.99以下)',
  `subscriber_coupon_id` int(10) unsigned DEFAULT '0' COMMENT '奖励给关注者的优惠券的ID',
  `subscriber_coupon_name` varchar(15) DEFAULT '' COMMENT '奖励给关注者的优惠券的名称',
  `subscriber_coupon_num` int(10) unsigned DEFAULT '0' COMMENT '奖励给关注者的优惠券的张数',
  `bonus_method` tinyint(4) DEFAULT '1' COMMENT '现金奖励方式: 1为余额 2为微信钱包(默认为余额)',
  `recommender_award_notice` varchar(140) DEFAULT '' COMMENT '推荐者获得奖励的通知',
  `subscriber_award_notice` varchar(140) DEFAULT '' COMMENT '关注者获得奖励的通知',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_poster_id` (`poster_id`),
  CONSTRAINT `". tablename('yz_poster_supplement_ibfk_1')."` FOREIGN KEY (`poster_id`) REFERENCES `". tablename('yz_poster')."` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_qq_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_qq_config')."`;

CREATE TABLE `". tablename('yz_qq_config')."` (
  `config_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '统一公众号ID',
  `app_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用公钥',
  `app_secret` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用私钥',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '接入类型 0-网站；1-移动',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_refund_express
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_refund_express')."`;

CREATE TABLE `". tablename('yz_refund_express')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refund_id` int(11) NOT NULL DEFAULT '0',
  `express_company_name` varchar(50) NOT NULL DEFAULT '0',
  `express_sn` varchar(50) NOT NULL DEFAULT '0',
  `express_code` varchar(20) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`refund_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_role')."`;

CREATE TABLE `". tablename('yz_role')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uniacid` int(11) NOT NULL COMMENT '统一账号',
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '删除时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_setting
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_setting')."`;

CREATE TABLE `". tablename('yz_setting')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '统一账号',
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'shop' COMMENT '分组',
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置key名',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '值类型',
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_slide
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_slide')."`;

CREATE TABLE `". tablename('yz_slide')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `slide_name` varchar(100) DEFAULT NULL COMMENT '幻灯片名称',
  `link` varchar(255) DEFAULT NULL COMMENT '链接',
  `thumb` varchar(255) DEFAULT NULL COMMENT '图片',
  `display_order` int(11) DEFAULT NULL COMMENT '排序',
  `enabled` tinyint(3) DEFAULT NULL COMMENT '启用状态 0:禁用 1：启用',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_sms_send_limit
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_sms_send_limit')."`;

CREATE TABLE `". tablename('yz_sms_send_limit')."` (
  `sms_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uniacid` int(11) NOT NULL COMMENT '统一公众号',
  `mobile` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '手机号',
  `total` tinyint(1) NOT NULL COMMENT '发送数量',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '短信发送时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`sms_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_street
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_street')."`;

CREATE TABLE `". tablename('yz_street')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `areaname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parentid` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_supplier
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_supplier')."`;

CREATE TABLE `". tablename('yz_supplier')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员id',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '账号',
  `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '密码',
  `realname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '姓名',
  `mobile` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '电话',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = 申请状态 1 = 通过   -1 = 驳回',
  `apply_time` int(11) NOT NULL DEFAULT '0' COMMENT '申请时间',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `salt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_supplier_dispatch
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_supplier_dispatch')."`;

CREATE TABLE `". tablename('yz_supplier_dispatch')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dispatch_id` int(11) NOT NULL DEFAULT '0' COMMENT '运费模板id',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商id',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员id',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='供应商运费模板表';



# Dump of table ims_yz_supplier_goods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_supplier_goods')."`;

CREATE TABLE `". tablename('yz_supplier_goods')."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品id',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商id',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员id',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `uniacid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_supplier_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_supplier_order')."`;

CREATE TABLE `". tablename('yz_supplier_order')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商id',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员id',
  `apply_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '提现状态   0 = 未提现      1 = 提交提现申请  2 = 通过申请打款成功 -1 = 驳回申请',
  `supplier_profit` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '供应商利润',
  `order_goods_information` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '订单商品信息',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `uniacid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_supplier_withdraw
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_supplier_withdraw')."`;

CREATE TABLE `". tablename('yz_supplier_withdraw')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员ID',
  `supplier_id` int(11) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = 提交提现申请  2 = 审核通过 3 = 打款成功 -1 = 驳回申请',
  `money` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '提现金额',
  `order_ids` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '当前提现记录的所有orderids',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `uniacid` int(11) DEFAULT '0',
  `apply_sn` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `type` tinyint(1) DEFAULT '0' COMMENT '1 提现到银行卡 2 提现到微信',
  `pay_time` int(11) DEFAULT '0' COMMENT '打款时间,完成时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_template_message
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_template_message')."`;

CREATE TABLE `". tablename('yz_template_message')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'system' COMMENT '模板类型 ',
  `item` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '发送标识',
  `parent_item` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '上级',
  `title` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `template_id_short` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板库中模板的编号',
  `template_id` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板的ID',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '详细内容',
  `example` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容示例',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 1成功 0失败',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_template_message_record
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_template_message_record')."`;

CREATE TABLE `". tablename('yz_template_message_record')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '统一账号',
  `member_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '会员',
  `openid` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '接收用户openid',
  `template_id` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板ID',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '跳转URL',
  `top_color` char(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '全局颜色',
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '数据',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `send_time` int(11) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0未发送 1发送中  2发送失败 3发送成功 ',
  `msgid` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '微信消息id',
  `result` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发送结果',
  `wechat_send_at` int(11) NOT NULL DEFAULT '0' COMMENT '微信发送时间',
  `sended_count` tinyint(1) NOT NULL DEFAULT '1' COMMENT '发送次数',
  `extend_data` text COLLATE utf8mb4_unicode_ci COMMENT '扩展数据',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_user_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_user_role')."`;

CREATE TABLE `". tablename('yz_user_role')."` (
  `user_id` int(11) NOT NULL COMMENT '用户',
  `role_id` int(11) NOT NULL COMMENT '角色',
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_withdraw
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_withdraw')."`;

CREATE TABLE `". tablename('yz_withdraw')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `withdraw_sn` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uniacid` int(11) DEFAULT NULL COMMENT '店铺ID',
  `member_id` int(11) DEFAULT NULL COMMENT '会员ID ',
  `type` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '提现类型',
  `type_id` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '关联 收入订单ID',
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amounts` decimal(14,2) DEFAULT NULL COMMENT '提现金额',
  `poundage` decimal(14,2) DEFAULT NULL COMMENT '手续费',
  `poundage_rate` int(11) DEFAULT NULL COMMENT '手续费比例',
  `pay_way` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '打款方式',
  `status` tinyint(1) DEFAULT NULL COMMENT '0:未审核 1：未打款 2：已打款 -1：无效',
  `created_at` int(11) DEFAULT NULL,
  `audit_at` int(11) DEFAULT NULL,
  `pay_at` int(11) DEFAULT NULL,
  `arrival_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `actual_amounts` decimal(14,2) NOT NULL,
  `actual_poundage` decimal(14,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_withdraw_relation_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `". tablename('yz_withdraw_relation_order')."`;

CREATE TABLE `". tablename('yz_withdraw_relation_order')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `withdraw_id` int(11) DEFAULT '0',
  `order_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `". tablename('yz_menu')."` (`id`, `name`, `item`, `url`, `url_params`, `permit`, `menu`, `icon`, `parent_id`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`)
VALUES
  (15, '评论管理', 'comment', 'goods.comment.index', '', 1, 1, 'fa-columns', 6, 5, 1, 0, 1491794656, NULL),
  (14, '删除分类', 'goods.category.delete', 'goods.category.deleted-category', '', 1, 0, 'fa-sliders', 11, 3, 1, 0, 1492130370, NULL),
  (13, '修改分类', 'goods.category.edit', 'goods.category.edit-category', '', 1, 0, 'fa-edit', 11, 2, 1, 1490182203, 1492129756, NULL),
  (12, '创建分类', 'goods.category.add', 'goods.category.add-category', '', 1, 1, 'fa-plus', 11, 0, 1, 1490182203, 1492129680, NULL),
  (11, '商品分类', 'goods.category', 'goods.category.index', '', 1, 1, 'fa-sitemap', 6, 2, 1, 1490182203, 1491794744, NULL),
  (10, '删除品牌', 'goods.brand.delete', 'goods.brand.deleted-brand', '', 1, 0, 'fa-remove', 7, 3, 1, 1490182114, 1492130396, NULL),
  (9, '修改品牌', 'goods.brand.edit', 'goods.brand.edit', '', 1, 0, 'fa-edit', 7, 2, 1, 1490182066, 1492130384, NULL),
  (8, '创建品牌', 'goods.brand.add', 'goods.brand.add', '', 1, 1, 'fa-plus', 7, 0, 1, 1490182024, 1492129584, NULL),
  (7, '品牌管理', 'goods.brand', 'goods.brand.index', '', 1, 1, 'fa-briefcase', 6, 3, 1, 1490181968, 1491794806, NULL),
  (6, '商品管理', 'goods', '', '', 1, 1, 'fa-outdent', 0, 2, 1, 1490181906, 1491790770, NULL),
  (5, '删除菜单', 'menu.delete', 'menu.delete', '', 1, 0, 'fa-remove', 2, 0, 1, 1490172576, 1490172576, NULL),
  (4, '修改菜单', 'menu.edit', 'menu.edit', '', 1, 0, 'fa-edit', 2, 0, 1, 1490172528, 1490172528, NULL),
  (3, '创建菜单', 'menu.add', 'menu.add', '', 1, 0, 'fa-plus', 2, 0, 1, 1490172477, 1490172477, NULL),
  (1, '系统管理', 'system', '', '', 1, 1, 'fa-asterisk', 0, 1, 1, 1490172379, 1491790782, NULL),
  (2, '菜单管理', 'menu', 'menu.index', '', 1, 1, 'fa-align-justify', 1, 0, 1, 1490172436, 1490172436, NULL),
  (16, '创建评论', 'goods.comment.add', 'goods.comment.add-comment', '', 1, 1, 'fa-plus', 15, 0, 1, 1490182203, 1490599752, NULL),
  (17, '修改评论', 'goods.comment.edit', 'goods.comment.updated', '', 1, 0, 'fa-edit', 15, 0, 1, 1490182203, 1492130416, NULL),
  (18, '删除评论', 'goods.comment.delete', 'goods.comment.deleted', '', 1, 0, 'fa-circle-o', 15, 0, 1, 0, 1492130453, NULL),
  (19, '回复评论', 'goods.comment.reply', 'goods.comment.reply', '', 1, 0, 'fa-circle-o', 15, 0, 1, 0, 1492130469, NULL),
  (20, '商城设置', 'Setting', 'setting.shop.shop', '', 1, 1, 'fa-cog', 1, 0, 1, 1490410074, 1490410496, NULL),
  (21, '基础设置', 'setting.shop.index', 'setting.shop.index', '', 0, 0, 'fa-sliders', 20, 0, 1, 1490411291, 1492425368, NULL),
  (22, '分享引导设置', 'setting.shop.share', 'setting.shop.share', '', 1, 1, 'fa-sliders', 20, 5, 1, 1490411371, 1491963442, NULL),
  (23, '消息提醒设置', 'setting.shop.notice', 'setting.shop.notice', '', 1, 1, 'fa-sliders', 20, 6, 1, 1490412887, 1491794955, NULL),
  (24, '交易设置', 'setting.shop.trade', 'setting.shop.trade', '', 1, 1, 'fa-sliders', 20, 0, 1, 1490412921, 1490755443, NULL),
  (25, '支付方式', 'setting.shop.pay', 'setting.shop.pay', '', 1, 1, 'fa-sliders', 20, 0, 1, 1490412947, 1490755461, NULL),
  (26, '短信设置', 'setting.shop.sms', 'setting.shop.sms', '', 1, 1, 'fa-sliders', 20, 0, 1, 1490412969, 1492049625, 1492049625),
  (27, '商品发布', 'goods.goods', 'goods.goods.index', '', 1, 1, 'fa-sliders', 6, 0, 1, 1490422819, 1492087957, NULL),
  (28, '订单管理', 'order', 'order.list', '', 1, 1, 'fa-list', 0, 4, 1, 1490607478, 1491793900, NULL),
  (29, '全部订单', 'order.list', 'order.list', '', 1, 1, 'fa-sliders', 28, 0, 1, 1490607665, 1490607665, NULL),
  (30, '角色管理', 'role', 'user.role.index', '', 1, 1, 'fa-user', 1, 0, 1, 1490620449, 1490620449, NULL),
  (31, '添加角色', 'user.role.store', 'user.role.store', '', 1, 1, 'fa-plus', 30, 0, 1, 1490620540, 1492129560, NULL),
  (32, '修改角色', 'user.role.update', 'user.role.update', '', 1, 0, 'fa-pencil-square-o', 30, 0, 1, 1490620730, 1492129772, NULL),
  (33, '删除角色', 'user.role.destory', 'user.role.destory', '', 1, 0, 'fa-remove', 30, 0, 1, 1490620828, 1492129786, NULL),
  (34, '操作员', 'user.user.index', 'user.user.index', '', 1, 1, 'fa-list-ul', 1, 0, 1, 1490621121, 1490621121, NULL),
  (35, '添加操作员', 'user.user.store', 'user.user.store', '', 1, 1, 'fa-plus', 34, 0, 1, 1490621173, 1492088023, NULL),
  (36, '修改操作员', 'user.user.update', 'user.user.update', '', 1, 0, 'fa-edit', 34, 0, 1, 1490621232, 1492129799, NULL),
  (37, '删除操作员', 'user.user.destroy', 'user.user.destroy', '', 1, 0, 'fa-remove', 34, 0, 1, 1490621499, 1492129814, NULL),
  (38, '会员管理', 'user', '', '', 1, 1, 'fa-users', 0, 3, 1, 1490683165, 1491793920, NULL),
  (39, '全部会员', 'user_all', 'member.member.index', '', 1, 1, 'fa-user', 38, 0, 1, 1490683467, 1490683467, NULL),
  (40, '会员关系设置', 'user_relation', 'member.member-relation.index', '', 1, 1, 'fa-sliders', 100, 0, 1, 1490683533, 1492477734, NULL),
  (41, '插件管理', 'plugin', '', '', 1, 1, 'fa-sliders', 0, 0, 1, 1490699993, 1490749380, 1490749380),
  (42, '文章营销', 'article', '', '', 1, 1, 'fa-sliders', 41, 0, 1, 1490700042, 1490749374, 1490749374),
  (43, '基础设置', 'article.set', 'plugin.article.admin.set', '', 1, 1, 'fa-sliders', 42, 0, 1, 1490700108, 1490749369, 1490749369),
  (44, '文章管理', 'article.list', 'plugin.article.admin.article', '', 1, 1, 'fa-sliders', 42, 0, 1, 1490700168, 1490749363, 1490749363),
  (45, '文章分类', 'article.category', 'plugin.article.admin.category', '', 1, 1, 'fa-sliders', 42, 0, 1, 1490700208, 1490749358, 1490749358),
  (46, '文章采集', 'article.collect', 'plugin.article.admin.article.collect', '', 1, 1, 'fa-sliders', 42, 0, 1, 1490700282, 1490749350, 1490749350),
  (47, '会员等级', 'member.member-level.index', 'member.member-level.index', '', 1, 1, 'fa-group', 38, 0, 1, 1490781929, 1490781929, NULL),
  (48, '添加会员等级', 'member.member-level.store', 'member.member-level.store', '', 1, 1, 'fa-plus', 47, 0, 1, 1490782606, 1492129573, NULL),
  (49, '编辑会员等级', 'member.member-level.update', 'member.member-level.update', '', 1, 0, 'fa-edit', 47, 0, 1, 1490782802, 1490782802, NULL),
  (50, '删除会员等级', 'member.memberlevel.destroy', 'member.member-level.destroy', '', 1, 0, 'fa-remove', 47, 0, 1, 1490782855, 1490783864, NULL),
  (55, '配送模板', 'goods.dispatch', 'goods.dispatch', '', 1, 1, 'fa-sliders', 6, 4, 1, 1490868383, 1491794822, NULL),
  (51, '会员分组', 'member.member-group.index', 'member.member-group.index', '', 1, 1, 'fa-group', 38, 0, 1, 1490783444, 1490783444, NULL),
  (52, '添加会员分组', 'member.member-group.store', 'member.member-group.store', '', 1, 1, 'fa-plus', 51, 0, 1, 1490783552, 1492129528, NULL),
  (53, '修改会员分组', 'member.member-group.update', 'member.member-group.update', '', 1, 0, 'fa-pencil-square-o', 51, 0, 1, 1490783596, 1490783596, NULL),
  (54, '删除会员分组', 'member.member-group.destroy', 'member.member-group.destroy', '', 1, 0, 'fa-remove', 51, 0, 1, 1490783643, 1490783643, NULL),
  (56, '模板管理', 'goods.dispatch.index', 'goods.dispatch.index', '', 1, 1, 'fa-sliders', 55, 0, 1, 1490868425, 1490868425, NULL),
  (57, '添加模板', 'goods.dispatch.add', 'goods.dispatch.add', '', 1, 1, 'fa-sliders', 55, 0, 1, 1490868459, 1490868459, NULL),
  (58, '财务管理', 'finance', '', '', 1, 1, 'fa-sliders', 0, 5, 1, 1490943530, 1492130499, NULL),
  (59, '提现设置', 'withdraw', 'finance.withdraw.set', '', 1, 1, 'fa-sliders', 58, 0, 1, 1490943603, 1490943603, NULL),
  (60, '余额基础设置', 'balance.set', 'finance.balance', '', 1, 1, 'fa-sliders', 91, 0, 1, 1490943653, 1492129381, NULL),
  (61, '用户余额管理', 'finance.balance.member', 'finance.balance.member', '', 1, 1, 'fa-sliders', 91, 0, 1, 1491039625, 1492129407, NULL),
  (62, '余额充值记录', 'finance.balance.rechargeRecord', 'finance.balance.rechargeRecord', '', 1, 1, 'fa-sliders', 91, 0, 1, 1491039706, 1492129480, NULL),
  (63, '余额转让记录', 'finance.balance.tansferRecord', 'finance.balance.transferRecord', '', 1, 1, 'fa-sliders', 91, 0, 1, 1491124776, 1492129504, NULL),
  (64, '余额充值', 'finance.balance.recharge', 'finance.balance.recharge', '', 1, 0, 'fa-sliders', 58, 0, 0, 1491375327, 1492046650, NULL),
  (65, 'test提现设置', 'test.withdraw', 'finance.withdraw.set', '', 1, 1, 'fa-sliders', 58, 0, 1, 1491616100, 1491616118, 1491616118),
  (66, '提现记录', 'finance.withdraw', 'finance.withdraw', '', 1, 1, 'fa-sliders', 58, 0, 1, 1491616318, 1491616354, NULL),
  (87, '待发货订单', 'order.list.waitSend', 'order.list.waitSend', '', 1, 1, 'fa-circle-o', 28, 2, 1, 1492050639, 1492051011, NULL),
  (86, '待支付订单', 'order.list.waitPay', 'order.list.waitPay', '', 1, 1, 'fa-circle-o', 28, 1, 1, 1492049766, 1492050985, NULL),
  (84, '积分明细', 'point.log', 'finance.point-log', '', 1, 1, 'fa-circle-o', 81, 0, 1, 1492004354, 1492004354, NULL),
  (85, '余额明细', 'finance.balance.balanceDetail', 'finance.balance.balanceDetail', '', 1, 1, 'fa-file-text', 91, 0, 1, 1492046618, 1492129452, NULL),
  (83, '会员积分', 'point.member', 'finance.point-member', '', 1, 1, 'fa-circle-o', 81, 0, 1, 1492004318, 1492004318, NULL),
  (82, '积分基础设置', 'point.set', 'finance.point-set', '', 1, 1, 'fa-circle-o', 81, 0, 1, 1492004268, 1492004268, NULL),
  (81, '积分', 'finance.point', '', '', 1, 1, 'fa-circle-o', 58, 0, 1, 1492004231, 1492004231, NULL),
  (80, '积分基础设置', 'point', 'finance.point-set', '', 1, 1, 'fa-circle-o', 58, 0, 1, 1492004143, 1492004174, 1492004174),
  (79, '插件管理', 'plugins-manage', 'plugins.get-plugin-data', '', 1, 1, 'fa-circle-o', 1, 0, 1, 1491987643, 1492130529, NULL),
  (78, '资格申请', 'agent_apply', 'member.member-relation.apply', '', 1, 1, 'fa-sliders', 100, 0, 1, 1491981655, 1492477754, NULL),
  (88, '待收货订单', 'order.list.waitReceive', 'order.list.waitReceive', '', 1, 1, 'fa-circle-o', 28, 3, 1, 1492050693, 1492051034, NULL),
  (89, '已完成订单', 'order.list.completed', 'order.list.completed', '', 1, 1, 'fa-circle-o', 28, 5, 1, 1492050737, 1492439034, NULL),
  (90, '申请协议', 'applyprotocol', 'member.member-relation.applyprotocol', '', 1, 1, 'fa-circle-o', 100, 0, 1, 1492093073, 1492477776, NULL),
  (91, '余额管理', 'balance', 'balance', '', 1, 1, 'fa-circle-o', 58, 0, 1, 1492129346, 1492129346, NULL),
  (92, '待审核提现', 'withdraw-status-wait-audit', 'finance.withdraw', '&search[status]=0', 1, 1, 'fa-circle-o', 66, 0, 1, 1492149299, 1492150356, NULL),
  (93, '待打款提现', 'withdraw-status-wait-pay', 'finance.withdraw', '&search[status]=1', 1, 1, 'fa-circle-o', 66, 0, 1, 1492150423, 1492150701, NULL),
  (94, '已打款提现', 'withdraw-status-pay', 'finance.withdraw', '&search[status]=2', 1, 1, 'fa-circle-o', 66, 0, 1, 1492150485, 1492150485, NULL),
  (95, '已到账提现', 'withdraw-status-arrival', 'finance.withdraw', '&search[status]=3', 1, 1, 'fa-circle-o', 66, 0, 1, 1492150586, 1492150753, NULL),
  (96, '无效提现', 'withdraw-status-invalid', 'finance.withdraw', '&search[status]=-1', 1, 1, 'fa-circle-o', 66, 0, 1, 1492150645, 1492150736, NULL),
  (97, '退换货订单', 'refund', 'order.list.refund', '', 1, 1, 'fa-circle-o', 28, 6, 1, 1492170755, 1492439021, NULL),
  (98, '已退款', 'order.list.refunded', 'order.list.refunded', '', 1, 1, 'fa-circle-o', 28, 7, 1, 1492438967, 1492438967, NULL),
  (99, '已关闭订单', 'order.list.cancelled', 'order.list.cancelled', '', 1, 1, 'fa-circle-o', 28, 5, 1, 1492439282, 1492439282, NULL),
  (100, '会员关系', 'relation', '', '', 1, 1, 'fa-circle-o', 38, 0, 1, 1492477575, 1492477575, NULL),
  (101, '优惠券管理', 'coupon', 'coupon.coupon.index', '', 1, 1, 'fa-circle-o', 6, 6, 1, 1492504682, 1492504850, NULL),
  (102, '优惠券列表', 'coupon.coupon.index', 'coupon.coupon.index', '', 1, 1, 'fa-circle-o', 101, 1, 1, 1492504792, 1492504918, NULL),
  (103, '创建优惠券', 'coupon.coupon.create', 'coupon.coupon.create', '', 1, 1, 'fa-circle-o', 101, 2, 1, 1492504909, 1492504909, NULL);

";


pdo_fetchall($sql);

