<?php
global $_W;

$sql = "
CREATE TABLE IF NOT EXISTS " .  tablename('yz_menu') . " (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8;

CREATE TABLE IF NOT EXISTS " .  tablename( 'yz_member_relation') . " (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS " .  tablename( 'yz_pay_order') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uniacid` int(11) NOT NULL COMMENT '统一公众号',
  `member_id` int(11) NOT NULL COMMENT '用户ID',
  `int_order_no` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '支付单号',
  `out_order_no` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '订单号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态 0-未支付；1-待支付；2-已支付',
  `price` int(11) NOT NULL COMMENT '支付金额',
  `type` tinyint(1) NOT NULL COMMENT '支付类型(1支付、2充值)',
  `third_type` tinyint(1) NOT NULL COMMENT '第三方支付类型',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更像时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_member_id` (`member_id`),
  KEY `idx_order_no` (`out_order_no`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS " .  tablename(  'yz_pay_order') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uniacid` int(11) NOT NULL COMMENT '统一公众号',
  `member_id` int(11) NOT NULL COMMENT '用户ID',
  `int_order_no` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '支付单号',
  `out_order_no` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '订单号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态 0-未支付；1-待支付；2-已支付',
  `price` int(11) NOT NULL COMMENT '支付金额',
  `type` tinyint(1) NOT NULL COMMENT '支付类型(1支付、2充值)',
  `third_type` tinyint(1) NOT NULL COMMENT '第三方支付类型',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更像时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_member_id` (`member_id`),
  KEY `idx_order_no` (`out_order_no`)
) ENGINE=MyISAM AUTO_INCREMENT=606 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS " .  tablename(   'yz_pay_request_data') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uniacid` int(11) NOT NULL COMMENT '统一公众号',
  `order_id` int(11) NOT NULL COMMENT '支付单ID/提现单ID/退款单ID',
  `type` tinyint(1) NOT NULL COMMENT '支付种类 1-订单支付 2-充值  ',
  `third_type` tinyint(1) DEFAULT NULL COMMENT '支付类型 1-微信；2-支付宝；3-余额',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '请求数据',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=471 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";


pdo_fetchall($sql);

