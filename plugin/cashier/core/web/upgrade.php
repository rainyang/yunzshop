<?php
global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'cashier'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`) VALUES(". $displayorder .",'cashier','收银台','1.0','官方','1');";
  pdo_query($sql);
}
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
  `condition` decimal(10,2) DEFAULT '0.00' COMMENT '使用优惠券条件',
  `iscontact` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否填写联系人信息',
  `isreturn` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否加入全返',
  `centercan` tinyint(1) NOT NULL DEFAULT '1' COMMENT '会员中心是够可以编辑',
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
pdo_query($sql);
if(!pdo_fieldexists('sz_yi_order', 'cashier')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `cashier` tinyint(1) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'realprice')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `realprice` decimal(10) DEFAULT '0';");
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

if (!pdo_fieldexists('sz_yi_cashier_store', 'bonus')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_cashier_store')." ADD `bonus` decimal(10,2) DEFAULT NULL;");
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


message('芸众收银台插件安装成功', $this->createPluginWebUrl('cashier/index'), 'success');
