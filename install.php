<?php
global $_W;

$sql = "
DROP TABLE IF EXISTS ". tablename('yz_account_open_config').";

CREATE TABLE ". tablename('yz_account_open_config')." (
  `config_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `app_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `app_secret` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_address
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_address').";

CREATE TABLE ". tablename('yz_address')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `areaname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parentid` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_agent_level
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_agent_level').";

CREATE TABLE ". tablename('yz_agent_level')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `level` int(11) NOT NULL DEFAULT '0',
  `first_level` int(11) DEFAULT '0',
  `second_level` int(11) DEFAULT '0',
  `third_level` int(11) DEFAULT '0',
  `upgraded` text COLLATE utf8mb4_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_agents
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_agents').";

CREATE TABLE ". tablename('yz_agents')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0',
  `agent_level_id` int(11) DEFAULT '0',
  `is_black` tinyint(1) DEFAULT '0',
  `commission_total` decimal(14,2) DEFAULT '0.00',
  `commission_pay` decimal(14,2) DEFAULT NULL,
  `agent_not_upgrade` tinyint(1) DEFAULT '0',
  `content` text COLLATE utf8mb4_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `parent` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid_parent` (`uniacid`,`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_balance
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_balance').";

CREATE TABLE ". tablename('yz_balance')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `old_money` decimal(14,2) DEFAULT NULL,
  `change_money` decimal(14,2) NOT NULL,
  `new_money` decimal(14,2) NOT NULL,
  `type` tinyint(3) NOT NULL,
  `service_type` tinyint(11) NOT NULL,
  `serial_number` varchar(45) NOT NULL DEFAULT '',
  `operator` int(11) NOT NULL,
  `operator_id` varchar(45) NOT NULL DEFAULT '',
  `remark` varchar(200) NOT NULL DEFAULT '',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_balance_recharge
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_balance_recharge').";

CREATE TABLE ". tablename('yz_balance_recharge')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `old_money` decimal(14,2) DEFAULT NULL,
  `money` decimal(14,2) DEFAULT NULL,
  `new_money` decimal(14,2) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `ordersn` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_balance_transfer
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_balance_transfer').";

CREATE TABLE ". tablename('yz_balance_transfer')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `transferor` int(11) DEFAULT NULL,
  `recipient` int(11) DEFAULT NULL,
  `money` decimal(14,2) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_brand
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_brand').";

CREATE TABLE ". tablename('yz_brand')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `alias` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_category').";

CREATE TABLE ". tablename('yz_category')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` tinyint(1) DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '1',
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

DROP TABLE IF EXISTS ". tablename('yz_comment').";

CREATE TABLE ". tablename('yz_comment')." (
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
  `comment_id` int(11) DEFAULT '0',
  `reply_id` int(11) DEFAULT '0',
  `reply_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint(3) DEFAULT '1',
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

DROP TABLE IF EXISTS ". tablename('yz_comment_bak2').";

CREATE TABLE ". tablename('yz_comment_bak2')." (
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

DROP TABLE IF EXISTS ". tablename('yz_commission_edit_log').";

CREATE TABLE ". tablename('yz_commission_edit_log')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_commission_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_commission_order').";

CREATE TABLE ". tablename('yz_commission_order')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `ordertable_type` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordertable_id` int(11) DEFAULT NULL,
  `buy_id` int(11) DEFAULT NULL,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `commission_amount` decimal(14,2) DEFAULT '0.00',
  `formula` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hierarchy` int(11) DEFAULT '1',
  `commission_rate` int(11) DEFAULT '0',
  `commission` decimal(14,2) DEFAULT '0.00',
  `status` tinyint(1) DEFAULT '0',
  `withdraw` tinyint(3) NOT NULL DEFAULT '0',
  `recrive_at` int(11) DEFAULT NULL,
  `settle_days` int(11) DEFAULT '0',
  `statement_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_commission_order_goods
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_commission_order_goods').";

CREATE TABLE ". tablename('yz_commission_order_goods')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `commission_order_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_commission` tinyint(1) DEFAULT NULL,
  `commission_rate` int(11) DEFAULT NULL,
  `commission_pay` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_coupon
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_coupon').";

CREATE TABLE ". tablename('yz_coupon')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `cat_id` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `get_type` tinyint(3) DEFAULT '0',
  `get_max` int(11) DEFAULT '0',
  `use_type` tinyint(3) unsigned DEFAULT '0',
  `return_type` tinyint(3) DEFAULT '0',
  `bgcolor` varchar(255) DEFAULT '',
  `enough` int(11) unsigned NOT NULL DEFAULT '0',
  `coupon_type` tinyint(3) DEFAULT '0',
  `time_limit` tinyint(3) DEFAULT '0',
  `time_days` int(11) DEFAULT '0',
  `time_start` int(11) DEFAULT '0',
  `time_end` int(11) DEFAULT '0',
  `coupon_method` tinyint(4) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT '0.00',
  `deduct` decimal(10,2) DEFAULT '0.00',
  `back_type` tinyint(3) DEFAULT '0',
  `back_money` varchar(50) DEFAULT '',
  `back_credit` varchar(50) DEFAULT '',
  `back_redpack` varchar(50) DEFAULT '',
  `back_when` tinyint(3) DEFAULT '0',
  `thumb` varchar(255) DEFAULT '',
  `desc` text,
  `total` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '0',
  `money` decimal(10,2) DEFAULT '0.00',
  `resp_desc` text,
  `resp_thumb` varchar(255) DEFAULT '',
  `resp_title` varchar(255) DEFAULT '',
  `resp_url` varchar(255) DEFAULT '',
  `credit` int(11) DEFAULT '0',
  `usecredit2` tinyint(3) DEFAULT '0',
  `remark` varchar(1000) DEFAULT '',
  `descnoset` tinyint(3) DEFAULT '0',
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
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  `deleted_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_catid` (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_coupon_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_coupon_category').";

CREATE TABLE ". tablename('yz_coupon_category')." (
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

DROP TABLE IF EXISTS ". tablename('yz_coupon_log').";

CREATE TABLE ". tablename('yz_coupon_log')." (
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
  `getfrom` tinyint(3) DEFAULT '0',
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

DROP TABLE IF EXISTS ". tablename('yz_designer').";

CREATE TABLE ". tablename('yz_designer')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `page_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `page_type` tinyint(1) NOT NULL DEFAULT '0',
  `page_info` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `datas` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_pagetype` (`page_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_designer_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_designer_menu').";

CREATE TABLE ". tablename('yz_designer_menu')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `menu_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `menus` text COLLATE utf8mb4_unicode_ci,
  `params` text COLLATE utf8mb4_unicode_ci,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_isdefault` (`is_default`),
  KEY `idx_createtime` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_dispatch
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_dispatch').";

CREATE TABLE ". tablename('yz_dispatch')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `dispatch_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `display_order` int(11) DEFAULT '0',
  `first_weight_price` int(10) unsigned DEFAULT '0',
  `another_weight_price` decimal(11,0) DEFAULT '0',
  `first_weight` int(11) DEFAULT '0',
  `another_weight` int(11) DEFAULT '0',
  `areas` text COLLATE utf8mb4_unicode_ci,
  `carriers` text COLLATE utf8mb4_unicode_ci,
  `enabled` tinyint(1) DEFAULT '0',
  `is_default` tinyint(1) DEFAULT '0',
  `calculate_type` tinyint(1) DEFAULT '0',
  `first_piece_price` int(11) DEFAULT '0',
  `another_piece_price` int(11) DEFAULT '0',
  `first_piece` int(11) DEFAULT '0',
  `another_piece` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `is_plugin` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_dispatch_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_dispatch_type').";

CREATE TABLE ". tablename('yz_dispatch_type')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `plugin` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_goods
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods').";

CREATE TABLE ". tablename('yz_goods')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `brand_id` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int(11) DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `thumb` varchar(255) NOT NULL DEFAULT '',
  `thumb_url` text,
  `sku` varchar(5) DEFAULT '',
  `description` varchar(1000) DEFAULT '',
  `content` text,
  `goods_sn` varchar(50) DEFAULT '',
  `product_sn` varchar(50) DEFAULT '',
  `market_price` decimal(14,2) DEFAULT '0.00',
  `price` decimal(14,2) NOT NULL DEFAULT '0.00',
  `cost_price` decimal(14,2) DEFAULT '0.00',
  `stock` int(10) NOT NULL DEFAULT '0',
  `reduce_stock_method` int(11) DEFAULT '0',
  `show_sales` int(11) DEFAULT '0',
  `real_sales` int(11) DEFAULT '0',
  `weight` decimal(10,2) DEFAULT '0.00',
  `has_option` int(11) DEFAULT '0',
  `is_new` tinyint(1) DEFAULT '0',
  `is_hot` tinyint(1) DEFAULT '0',
  `is_discount` tinyint(1) DEFAULT '0',
  `is_recommand` tinyint(1) DEFAULT '0',
  `is_comment` tinyint(1) DEFAULT '0',
  `is_deleted` tinyint(3) NOT NULL DEFAULT '0',
  `created_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
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

DROP TABLE IF EXISTS ". tablename('yz_goods_area').";

CREATE TABLE ". tablename('yz_goods_area')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_goodid` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品与商品区域插件关联表';



# Dump of table ims_yz_goods_bonus
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_bonus').";

CREATE TABLE ". tablename('yz_goods_bonus')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `bonus_money` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_good_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品与分红关联表';



# Dump of table ims_yz_goods_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_category').";

CREATE TABLE ". tablename('yz_goods_category')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `category_ids` varchar(255) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table ims_yz_goods_commission
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_commission').";

CREATE TABLE ". tablename('yz_goods_commission')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `is_commission` int(11) DEFAULT NULL,
  `show_commission_button` tinyint(1) NOT NULL DEFAULT '0',
  `poster_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_commission` tinyint(1) DEFAULT '0',
  `first_level_rate` int(11) DEFAULT NULL,
  `first_level_pay` decimal(14,2) DEFAULT NULL,
  `second_level_rate` int(11) DEFAULT NULL,
  `second_level_pay` decimal(14,2) DEFAULT NULL,
  `third_level_rate` int(11) DEFAULT NULL,
  `third_level_pay` decimal(14,2) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_good_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_discount
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_discount').";

CREATE TABLE ". tablename('yz_goods_discount')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `level_discount_type` tinyint(1) NOT NULL,
  `discount_method` tinyint(1) NOT NULL,
  `level_id` int(11) NOT NULL,
  `discount_value` decimal(14,2) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_goodid` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_discount_detail
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_discount_detail').";

CREATE TABLE ". tablename('yz_goods_discount_detail')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_id` int(11) NOT NULL,
  `level_id` int(11) DEFAULT NULL,
  `discount` decimal(3,2) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_discountid` (`discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品折扣与商品折扣明细关联表';



# Dump of table ims_yz_goods_dispatch
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_dispatch').";

CREATE TABLE ". tablename('yz_goods_dispatch')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `dispatch_type` tinyint(1) NOT NULL DEFAULT '1',
  `dispatch_price` int(11) DEFAULT '0',
  `dispatch_id` int(11) DEFAULT NULL,
  `is_cod` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_good_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_diyform
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_diyform').";

CREATE TABLE ". tablename('yz_goods_diyform')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `good_id` int(11) NOT NULL,
  `diyform_id` int(11) DEFAULT NULL,
  `diyform_enable` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_goodid` (`good_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品与自定义表单关联表';



# Dump of table ims_yz_goods_level_returns
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_level_returns').";

CREATE TABLE ". tablename('yz_goods_level_returns')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `good_return_id` int(11) NOT NULL,
  `level_type` tinyint(3) NOT NULL DEFAULT '1',
  `level_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_good_return_id` (`good_return_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品等级返现规则表';



# Dump of table ims_yz_goods_notices
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_notices').";

CREATE TABLE ". tablename('yz_goods_notices')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_good_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_option
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_option').";

CREATE TABLE ". tablename('yz_goods_option')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `goods_id` int(10) DEFAULT '0',
  `title` varchar(50) DEFAULT NULL,
  `thumb` varchar(60) DEFAULT NULL,
  `product_price` decimal(10,2) DEFAULT '0.00',
  `market_price` decimal(10,2) DEFAULT '0.00',
  `cost_price` decimal(10,2) DEFAULT '0.00',
  `stock` int(11) DEFAULT '0',
  `weight` decimal(10,2) DEFAULT '0.00',
  `display_order` int(11) DEFAULT '0',
  `specs` text,
  `skuId` varchar(255) DEFAULT '',
  `goods_sn` varchar(255) DEFAULT '',
  `product_sn` varchar(255) DEFAULT '',
  `virtual` int(11) DEFAULT '0',
  `red_price` varchar(50) DEFAULT '',
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

DROP TABLE IF EXISTS ". tablename('yz_goods_param').";

CREATE TABLE ". tablename('yz_goods_param')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `goods_id` int(10) DEFAULT '0',
  `title` varchar(50) DEFAULT NULL,
  `value` text,
  `displayorder` int(11) DEFAULT '0',
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

DROP TABLE IF EXISTS ". tablename('yz_goods_privilege').";

CREATE TABLE ". tablename('yz_goods_privilege')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `show_levels` text COLLATE utf8mb4_unicode_ci,
  `show_groups` text COLLATE utf8mb4_unicode_ci,
  `buy_levels` text COLLATE utf8mb4_unicode_ci,
  `buy_groups` text COLLATE utf8mb4_unicode_ci,
  `once_buy_limit` int(11) DEFAULT '0',
  `total_buy_limit` int(11) DEFAULT '0',
  `time_begin_limit` int(11) DEFAULT NULL,
  `time_end_limit` int(11) DEFAULT NULL,
  `enable_time_limit` tinyint(1) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_goodid` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_return
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_return').";

CREATE TABLE ". tablename('yz_goods_return')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `good_id` int(11) NOT NULL,
  `is_level_return` tinyint(3) NOT NULL DEFAULT '0',
  `level_return_type` tinyint(3) NOT NULL DEFAULT '1',
  `is_order_return` tinyint(3) NOT NULL DEFAULT '0',
  `is_queue_return` tinyint(3) NOT NULL DEFAULT '0',
  `add_pool_amount` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_good_id` (`good_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品与全返关联表';



# Dump of table ims_yz_goods_sale
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_sale').";

CREATE TABLE ". tablename('yz_goods_sale')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `max_point_deduct` int(11) DEFAULT '0',
  `max_balance_deduct` int(11) DEFAULT '0',
  `is_sendfree` int(11) DEFAULT '0',
  `ed_num` int(11) DEFAULT '0',
  `ed_money` int(11) DEFAULT '0',
  `ed_areas` text COLLATE utf8mb4_unicode_ci,
  `point` int(11) DEFAULT '0',
  `bonus` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_good_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_share
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_share').";

CREATE TABLE ". tablename('yz_goods_share')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `need_follow` tinyint(1) DEFAULT NULL,
  `no_follow_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `follow_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `share_title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `share_thumb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `share_desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_goodid` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_goods_spec
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_goods_spec').";

CREATE TABLE ". tablename('yz_goods_spec')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `goods_id` int(11) DEFAULT '0',
  `title` varchar(50) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `display_type` tinyint(3) DEFAULT '0',
  `content` text,
  `display_order` int(11) DEFAULT '0',
  `propId` varchar(255) DEFAULT NULL,
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

DROP TABLE IF EXISTS ". tablename('yz_goods_spec_item').";

CREATE TABLE ". tablename('yz_goods_spec_item')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `specid` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `show` int(11) DEFAULT '0',
  `display_order` int(11) DEFAULT '0',
  `valueId` varchar(255) DEFAULT NULL,
  `virtual` int(11) DEFAULT '0',
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

DROP TABLE IF EXISTS ". tablename('yz_member').";

CREATE TABLE ". tablename('yz_member')." (
  `member_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `level_id` int(11) NOT NULL DEFAULT '0',
  `inviter` int(11) DEFAULT '0',
  `is_black` tinyint(1) NOT NULL DEFAULT '0',
  `province_name` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `city_name` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `area_name` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `province` int(11) DEFAULT '0',
  `city` int(11) DEFAULT '0',
  `area` int(11) DEFAULT '0',
  `address` text COLLATE utf8mb4_unicode_ci,
  `referralsn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `is_agent` tinyint(1) DEFAULT '0',
  `alipayname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alipay` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `status` int(11) DEFAULT '0',
  `child_time` int(11) DEFAULT '0',
  `agent_time` int(11) DEFAULT '0',
  `apply_time` int(11) DEFAULT '0',
  `relaton` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  KEY `idx_member_id` (`member_id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_app_wechat
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_app_wechat').";

CREATE TABLE ". tablename('yz_member_app_wechat')." (
  `app_wechat_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `openid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`app_wechat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_cart
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_cart').";

CREATE TABLE ". tablename('yz_member_cart')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_coupon
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_coupon').";

CREATE TABLE ". tablename('yz_member_coupon')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `uid` varchar(255) DEFAULT '',
  `coupon_id` int(11) DEFAULT '0',
  `get_type` tinyint(3) DEFAULT '0',
  `used` int(11) DEFAULT '0',
  `use_time` int(11) DEFAULT '0',
  `get_time` int(11) DEFAULT '0',
  `send_uid` int(11) DEFAULT '0',
  `order_sn` varchar(255) DEFAULT '',
  `back` tinyint(3) DEFAULT '0',
  `back_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_couponid` (`coupon_id`),
  KEY `idx_gettype` (`get_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_member_favorite
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_favorite').";

CREATE TABLE ". tablename('yz_member_favorite')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_group').";

CREATE TABLE ". tablename('yz_member_group')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `group_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `is_default` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_history').";

CREATE TABLE ". tablename('yz_member_history')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_income
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_income').";

CREATE TABLE ". tablename('yz_member_income')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `incometable_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `incometable_id` int(11) DEFAULT NULL,
  `type_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `pay_status` tinyint(3) NOT NULL DEFAULT '0',
  `detail` text COLLATE utf8mb4_unicode_ci,
  `create_month` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_level
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_level').";

CREATE TABLE ". tablename('yz_member_level')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `level_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_money` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_count` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `discount` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  `is_default` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_mini_app
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_mini_app').";

CREATE TABLE ". tablename('yz_member_mini_app')." (
  `mini_app_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `openid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`mini_app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_qq
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_qq').";

CREATE TABLE ". tablename('yz_member_qq')." (
  `qq_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `nickname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `figureurl` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `figureurl_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `figureurl_2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `figureurl_qq_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `figureurl_qq_2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` tinyint(1) NOT NULL DEFAULT '0',
  `is_yellow_year_vip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `vip` int(11) NOT NULL DEFAULT '0',
  `yellow_vip_level` tinyint(1) NOT NULL DEFAULT '0',
  `level` tinyint(1) NOT NULL DEFAULT '0',
  `is_yellow_vip` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`qq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_relation
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_relation').";

CREATE TABLE ". tablename('yz_member_relation')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `become` tinyint(1) NOT NULL DEFAULT '0',
  `become_order` tinyint(1) NOT NULL DEFAULT '0',
  `become_child` tinyint(1) NOT NULL DEFAULT '0',
  `become_ordercount` int(11) DEFAULT '0',
  `become_moneycount` decimal(5,2) DEFAULT '0.00',
  `become_goods_id` int(11) DEFAULT '0',
  `become_info` tinyint(1) NOT NULL DEFAULT '1',
  `become_check` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_unique
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_unique').";

CREATE TABLE ". tablename('yz_member_unique')." (
  `unique_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `unionid` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_id` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`unique_id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_unionid` (`unionid`),
  KEY `idx_member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_member_wechat
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_member_wechat').";

CREATE TABLE ". tablename('yz_member_wechat')." (
  `wechat_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `openid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` tinyint(1) NOT NULL DEFAULT '0',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `province` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`wechat_id`),
  KEY `idx_member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_menu').";

CREATE TABLE ". tablename('yz_menu')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url_params` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `permit` tinyint(1) NOT NULL DEFAULT '0',
  `menu` tinyint(1) NOT NULL DEFAULT '0',
  `icon` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_options
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_options').";

CREATE TABLE ". tablename('yz_options')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `option_value` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_order').";

CREATE TABLE ". tablename('yz_order')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL DEFAULT '0',
  `order_sn` varchar(23) NOT NULL DEFAULT '',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_total` int(11) NOT NULL DEFAULT '1',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(3) NOT NULL DEFAULT '0',
  `is_member_deleted` tinyint(3) NOT NULL DEFAULT '0',
  `finish_time` int(11) NOT NULL DEFAULT '0',
  `pay_time` int(11) NOT NULL DEFAULT '0',
  `send_time` int(11) NOT NULL DEFAULT '0',
  `cancel_time` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `cancel_pay_time` int(11) NOT NULL DEFAULT '0',
  `cancel_send_time` int(11) NOT NULL DEFAULT '0',
  `dispatch_type_id` tinyint(3) NOT NULL DEFAULT '0',
  `dispatch_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pay_type_id` tinyint(3) NOT NULL DEFAULT '0',
  `order_goods_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deduction_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `refund_id` int(11) NOT NULL DEFAULT '0',
  `is_plugin` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_address
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_order_address').";

CREATE TABLE ". tablename('yz_order_address')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL DEFAULT '0',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `realname` varchar(50) NOT NULL DEFAULT '',
  `updated_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_change_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_order_change_log').";

CREATE TABLE ". tablename('yz_order_change_log')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL DEFAULT '',
  `old_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `new_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_express
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_order_express').";

CREATE TABLE ". tablename('yz_order_express')." (
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

DROP TABLE IF EXISTS ". tablename('yz_order_goods').";

CREATE TABLE ". tablename('yz_order_goods')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `goods_id` int(11) NOT NULL DEFAULT '0',
  `total` int(11) NOT NULL DEFAULT '1',
  `create_at` int(11) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_sn` varchar(50) NOT NULL DEFAULT '',
  `uid` int(10) NOT NULL DEFAULT '0',
  `thumb` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(50) NOT NULL DEFAULT '',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_option_id` int(11) NOT NULL DEFAULT '0',
  `goods_option_title` varchar(50) NOT NULL DEFAULT '',
  `product_sn` varchar(23) NOT NULL DEFAULT '',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `discount_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `comment_status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_ORDER_ID_GOODS_ID` (`order_id`,`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_goods_change_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_order_goods_change_log').";

CREATE TABLE ". tablename('yz_order_goods_change_log')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_change_log_id` int(11) DEFAULT NULL,
  `order_goods_id` int(11) NOT NULL,
  `old_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `new_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `change_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_mapping
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_order_mapping').";

CREATE TABLE ". tablename('yz_order_mapping')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_order_id` int(11) NOT NULL,
  `new_order_id` int(11) NOT NULL,
  `old_openid` char(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_member_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_order_operation_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_order_operation_log').";

CREATE TABLE ". tablename('yz_order_operation_log')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `before_operation_status` tinyint(1) DEFAULT '0',
  `after_operation_status` tinyint(1) DEFAULT '0',
  `operator` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `operation_time` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT NULL,
  `type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_order_refund
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_order_refund').";

CREATE TABLE ". tablename('yz_order_refund')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `refund_sn` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `images` text NOT NULL,
  `content` text NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '0',
  `reply` text,
  `refund_way_type` tinyint(3) DEFAULT '0',
  `apply_price` decimal(10,2) DEFAULT '0.00',
  `order_price` decimal(10,2) DEFAULT '0.00',
  `refund_type` tinyint(1) DEFAULT '0',
  `refund_proof_imgs` text,
  `refund_time` int(11) DEFAULT '0',
  `refund_address` text,
  `remark` text,
  `operate_time` int(11) DEFAULT '0',
  `send_time` int(11) DEFAULT '0',
  `return_time` int(11) DEFAULT '0',
  `refund_express_company_code` varchar(100) NOT NULL DEFAULT '',
  `refund_express_company_name` varchar(100) NOT NULL DEFAULT '',
  `refund_express_sn` varchar(100) NOT NULL DEFAULT '',
  `refund_address_id` int(11) NOT NULL DEFAULT '0',
  `end_time` int(11) DEFAULT '0',
  `alipay_batch_sn` varchar(255) DEFAULT '',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_create_time` (`create_time`),
  KEY `idx_shop_id` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_order_remark
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_order_remark').";

CREATE TABLE ". tablename('yz_order_remark')." (
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

DROP TABLE IF EXISTS ". tablename('yz_pay_access_log').";

CREATE TABLE ". tablename('yz_pay_access_log')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` char(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(135) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_pay_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_pay_log').";

CREATE TABLE ". tablename('yz_pay_log')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `third_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `price` decimal(14,2) NOT NULL,
  `operation` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(135) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(13) NOT NULL DEFAULT '0',
  `updated_at` int(13) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_pay_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_pay_order').";

CREATE TABLE ". tablename('yz_pay_order')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `int_order_no` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `out_order_no` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `trade_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(14,2) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `third_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_member_id` (`member_id`),
  KEY `idx_order_no` (`out_order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_pay_request_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_pay_request_data').";

CREATE TABLE ". tablename('yz_pay_request_data')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `out_order_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `third_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_pay_response_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_pay_response_data').";

CREATE TABLE ". tablename('yz_pay_response_data')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `out_order_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `third_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_pay_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_pay_type').";

CREATE TABLE ". tablename('yz_pay_type')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `plugin_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_permission
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_permission').";

CREATE TABLE ". tablename('yz_permission')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  `item_id` int(11) NOT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_plugin_article
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_plugin_article').";

CREATE TABLE ". tablename('yz_plugin_article')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `desc` text,
  `thumb` text,
  `content` longtext NOT NULL,
  `virtual_created_at` int(11) DEFAULT NULL,
  `author` varchar(20) NOT NULL DEFAULT '',
  `virtual_read_num` int(11) DEFAULT NULL,
  `read_num` int(11) NOT NULL DEFAULT '0',
  `virtual_like_num` int(11) DEFAULT NULL,
  `like_num` int(11) NOT NULL DEFAULT '0',
  `link` varchar(255) DEFAULT NULL,
  `per_person_per_day` int(11) DEFAULT NULL,
  `total_per_person` int(11) DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `credit` int(11) DEFAULT NULL,
  `bonus_total` int(11) DEFAULT NULL,
  `bonus_total_now` int(11) DEFAULT NULL,
  `no_copy_url` tinyint(1) DEFAULT NULL,
  `no_share` tinyint(1) DEFAULT NULL,
  `no_share_to_friend` tinyint(1) DEFAULT NULL,
  `keyword` varchar(255) NOT NULL DEFAULT '',
  `report_enabled` tinyint(1) DEFAULT NULL,
  `advs_type` tinyint(1) DEFAULT NULL,
  `advs_title` varchar(255) DEFAULT NULL,
  `advs_title_footer` varchar(255) DEFAULT NULL,
  `advs_link` varchar(255) DEFAULT NULL,
  `advs` text,
  `state` tinyint(1) DEFAULT NULL,
  `state_wechat` tinyint(1) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_plugin_article_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_plugin_article_category').";

CREATE TABLE ". tablename('yz_plugin_article_category')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `member_level_limit` int(11) DEFAULT NULL,
  `commission_level_limit` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_plugin_article_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_plugin_article_log').";

CREATE TABLE ". tablename('yz_plugin_article_log')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL,
  `read_num` int(11) DEFAULT NULL,
  `like_num` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `uniacid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_plugin_article_report
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_plugin_article_report').";

CREATE TABLE ". tablename('yz_plugin_article_report')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_plugin_article_share
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_plugin_article_share').";

CREATE TABLE ". tablename('yz_plugin_article_share')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `share_uid` int(11) DEFAULT NULL,
  `click_uid` int(11) DEFAULT NULL,
  `click_time` int(11) DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `credit` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_plugin_goods_assistant
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_plugin_goods_assistant').";

CREATE TABLE ". tablename('yz_plugin_goods_assistant')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `itemid` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_point_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_point_log').";

CREATE TABLE ". tablename('yz_point_log')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `point` decimal(10,2) NOT NULL DEFAULT '0.00',
  `point_income_type` tinyint(2) NOT NULL DEFAULT '0',
  `point_mode` tinyint(5) NOT NULL DEFAULT '0',
  `before_point` decimal(10,2) NOT NULL DEFAULT '0.00',
  `after_point` decimal(10,2) NOT NULL DEFAULT '0.00',
  `remark` varchar(255) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_poster
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_poster').";

CREATE TABLE ". tablename('yz_poster')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `keyword` varchar(30) NOT NULL,
  `time_start` int(10) unsigned DEFAULT '0',
  `time_end` int(10) unsigned DEFAULT '0',
  `background` varchar(255) DEFAULT '',
  `style_data` text NOT NULL,
  `response_title` varchar(50) DEFAULT '',
  `response_thumb` varchar(255) DEFAULT '',
  `response_desc` varchar(255) DEFAULT '',
  `response_url` varchar(255) DEFAULT '',
  `is_open` tinyint(4) DEFAULT '0',
  `auto_sub` tinyint(4) DEFAULT '1',
  `status` tinyint(4) DEFAULT '1',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  `deleted_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_poster_award
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_poster_award').";

CREATE TABLE ". tablename('yz_poster_award')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `poster_id` int(10) unsigned NOT NULL,
  `subscriber_memberid` int(10) unsigned NOT NULL,
  `recommender_memberid` int(10) unsigned NOT NULL,
  `recommender_credit` int(10) unsigned DEFAULT '0',
  `recommender_bonus` int(10) unsigned DEFAULT '0',
  `recommender_coupon_id` int(10) unsigned DEFAULT '0',
  `recommender_coupon_num` int(10) unsigned DEFAULT '0',
  `subscriber_credit` int(10) unsigned DEFAULT '0',
  `subscriber_bonus` int(10) unsigned DEFAULT '0',
  `subscriber_coupon_id` int(10) unsigned DEFAULT '0',
  `subscriber_coupon_num` int(10) unsigned DEFAULT '0',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  `deleted_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_subscriber_memberid` (`subscriber_memberid`),
  KEY `idx_recommender_memberid` (`recommender_memberid`),
  KEY `poster_id` (`poster_id`),
  CONSTRAINT ". tablename('yz_poster_award_ibfk_1')." FOREIGN KEY (`poster_id`) REFERENCES ". tablename('yz_poster')." (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_poster_qrcode
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_poster_qrcode').";

CREATE TABLE ". tablename('yz_poster_qrcode')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `poster_id` int(10) unsigned NOT NULL,
  `qrcode_id` int(10) unsigned NOT NULL,
  `memberid` int(11) unsigned NOT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  `deleted_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_posterid` (`poster_id`),
  KEY `idx_qrcodeid` (`qrcode_id`),
  CONSTRAINT ". tablename('yz_poster_qrcode_ibfk_1')." FOREIGN KEY (`poster_id`) REFERENCES ". tablename('yz_poster')." (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_poster_scan
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_poster_scan').";

CREATE TABLE ". tablename('yz_poster_scan')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `poster_id` int(10) unsigned NOT NULL,
  `subscriber_memberid` int(10) unsigned NOT NULL,
  `recommender_memberid` int(10) unsigned NOT NULL,
  `event_type` tinyint(4) NOT NULL,
  `sign_up_this_time` tinyint(4) NOT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  `deleted_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_posterid` (`poster_id`),
  KEY `idx_subscriber_memberid` (`subscriber_memberid`),
  KEY `idx_recommender_memberid` (`recommender_memberid`),
  CONSTRAINT ". tablename('yz_poster_scan_ibfk_1')." FOREIGN KEY (`poster_id`) REFERENCES ". tablename('yz_poster')." (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_poster_supplement
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_poster_supplement').";

CREATE TABLE ". tablename('yz_poster_supplement')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poster_id` int(10) unsigned NOT NULL,
  `not_start_reminder` varchar(140) DEFAULT '',
  `finish_reminder` varchar(140) DEFAULT '',
  `wait_reminder` varchar(140) DEFAULT '',
  `not_open_reminder` varchar(140) DEFAULT '',
  `not_open_reminder_url` varchar(255) DEFAULT '',
  `recommender_credit` int(10) unsigned DEFAULT '0',
  `recommender_bonus` decimal(14,2) unsigned DEFAULT '0.00',
  `recommender_coupon_id` int(10) unsigned DEFAULT '0',
  `recommender_coupon_name` varchar(15) DEFAULT '',
  `recommender_coupon_num` int(10) unsigned DEFAULT '0',
  `subscriber_credit` int(10) unsigned DEFAULT '0',
  `subscriber_bonus` decimal(14,2) unsigned DEFAULT '0.00',
  `subscriber_coupon_id` int(10) unsigned DEFAULT '0',
  `subscriber_coupon_name` varchar(15) DEFAULT '',
  `subscriber_coupon_num` int(10) unsigned DEFAULT '0',
  `bonus_method` tinyint(4) DEFAULT '1',
  `recommender_award_notice` varchar(140) DEFAULT '',
  `subscriber_award_notice` varchar(140) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_poster_id` (`poster_id`),
  CONSTRAINT ". tablename('yz_poster_supplement_ibfk_1')." FOREIGN KEY (`poster_id`) REFERENCES ". tablename('yz_poster')." (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_qq_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_qq_config').";

CREATE TABLE ". tablename('yz_qq_config')." (
  `config_id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `app_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `app_secret` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_refund_express
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_refund_express').";

CREATE TABLE ". tablename('yz_refund_express')." (
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

DROP TABLE IF EXISTS ". tablename('yz_role').";

CREATE TABLE ". tablename('yz_role')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_setting
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_setting').";

CREATE TABLE ". tablename('yz_setting')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'shop',
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_slide
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_slide').";

CREATE TABLE ". tablename('yz_slide')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `slide_name` varchar(100) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  `enabled` tinyint(3) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ims_yz_sms_send_limit
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_sms_send_limit').";

CREATE TABLE ". tablename('yz_sms_send_limit')." (
  `sms_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `mobile` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` tinyint(1) NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`sms_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_street
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_street').";

CREATE TABLE ". tablename('yz_street')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `areaname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parentid` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_supplier
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_supplier').";

CREATE TABLE ". tablename('yz_supplier')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `realname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `mobile` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `apply_time` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `salt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_supplier_dispatch
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_supplier_dispatch').";

CREATE TABLE ". tablename('yz_supplier_dispatch')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dispatch_id` int(11) NOT NULL DEFAULT '0',
  `supplier_id` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='供应商运费模板表';



# Dump of table ims_yz_supplier_goods
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_supplier_goods').";

CREATE TABLE ". tablename('yz_supplier_goods')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL DEFAULT '0',
  `supplier_id` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `uniacid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_supplier_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_supplier_order').";

CREATE TABLE ". tablename('yz_supplier_order')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `supplier_id` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `apply_status` tinyint(1) NOT NULL DEFAULT '0',
  `supplier_profit` decimal(14,2) NOT NULL DEFAULT '0.00',
  `order_goods_information` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `uniacid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_supplier_withdraw
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_supplier_withdraw').";

CREATE TABLE ". tablename('yz_supplier_withdraw')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `supplier_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `money` decimal(14,2) NOT NULL DEFAULT '0.00',
  `order_ids` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `uniacid` int(11) DEFAULT '0',
  `apply_sn` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  `pay_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_template_message
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_template_message').";

CREATE TABLE ". tablename('yz_template_message')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'system',
  `item` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_item` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_id_short` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_id` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `example` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_template_message_record
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_template_message_record').";

CREATE TABLE ". tablename('yz_template_message_record')." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `member_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `openid` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `template_id` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `top_color` char(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `send_time` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `msgid` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `result` tinyint(1) NOT NULL DEFAULT '0',
  `wechat_send_at` int(11) NOT NULL DEFAULT '0',
  `sended_count` tinyint(1) NOT NULL DEFAULT '1',
  `extend_data` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_user_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_user_role').";

CREATE TABLE ". tablename('yz_user_role')." (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ims_yz_withdraw
# ------------------------------------------------------------

DROP TABLE IF EXISTS ". tablename('yz_withdraw').";

CREATE TABLE ". tablename('yz_withdraw')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `withdraw_sn` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uniacid` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `type` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_id` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amounts` decimal(14,2) DEFAULT NULL,
  `poundage` decimal(14,2) DEFAULT NULL,
  `poundage_rate` int(11) DEFAULT NULL,
  `pay_way` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
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

DROP TABLE IF EXISTS ". tablename('yz_withdraw_relation_order').";

CREATE TABLE ". tablename('yz_withdraw_relation_order')." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `withdraw_id` int(11) DEFAULT '0',
  `order_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  `updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ". tablename('yz_menu')." (`id`, `name`, `item`, `url`, `url_params`, `permit`, `menu`, `icon`, `parent_id`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`)
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

