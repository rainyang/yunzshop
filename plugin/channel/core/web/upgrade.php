<?php
global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
ca('channel.upgrade');
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'channel'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`,`category`) VALUES(". $displayorder .",'channel','渠道商','1.0','官方','1','biz');";
  pdo_fetchall($sql);
}

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_channel_merchant') . " (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uniacid` INT(11) NOT NULL,
  `openid` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL COMMENT '我的openid',
  `lower_openid` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL COMMENT '下级openid',
  `commission` DECIMAL(10,2) NULL COMMENT '推荐员分红比例',
  PRIMARY KEY (`id`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = '渠道商推荐员';



CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_channel_level') . " (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uniacid` INT(11) NOT NULL,
  `level_name` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL COMMENT '等级名称',
  `level_num` INT(1) NULL COMMENT '等级权重',
  `purchase_discount` VARCHAR(45) NULL COMMENT '进货折扣 %',
  `min_price` DECIMAL(10,2) NULL COMMENT '最小进货量',
  `profit_sharing` VARCHAR(45) NULL COMMENT '利润分成\n%',
  `become` INT(11) NULL COMMENT '升级条件',
  `team_count` INT(11) NULL COMMENT '团队人数',
  `goods_id` INT(11) NULL COMMENT '指定商品id',
  `order_money` INT(11) NULL COMMENT '订单累计金额',
  `order_count` INT(11) NULL COMMENT '订单累计次数',
  `createtime` INT(11) NULL COMMENT '创建时间',
  `updatetime` INT(11) NULL COMMENT '更新时间',
  PRIMARY KEY (`id`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = '渠道商等级';




CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_af_channel') . " (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `mid` INT(11) NOT NULL,
  `uniacid` INT(11) NOT NULL,
  `openid` VARCHAR(50) NULL,
  `realname` VARCHAR(45) NOT NULL COMMENT '真实姓名',
  `mobile` VARCHAR(11) NULL COMMENT '电话号',
  `diychannelid` INT(11) NULL,
  `diychanneldataid` INT(11) NULL,
  `diychannelfields` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `diychanneldata` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = MyISAM
COMMENT = '会员申请渠道商';



CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_channel_apply') . " (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uniacid` INT(11) NOT NULL,
  `mid` INT(11) NOT NULL,
  `openid` VARCHAR(50) NULL,
  `applyno` VARCHAR(255) NULL,
  `apply_money` DECIMAL(10,2) NULL COMMENT '申请金额',
  `apply_time` INT(11) NULL COMMENT '申请时间',
  `type` TINYINT(2) NULL COMMENT '提现类型',
  `status` TINYINT(2) NULL COMMENT '申请状态',
  `finish_time` INT(11) NULL COMMENT '完成时间',
  PRIMARY KEY (`id`))
ENGINE = MyISAM
COMMENT = '渠道商申请提现';



CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_channel_stock') . " (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uniacid` INT(11) NOT NULL,
  `openid` VARCHAR(50) NULL,
  `goodsid` INT(11) NOT NULL COMMENT '商品ID',
  `stock_total` INT(11) NOT NULL COMMENT '库存总数',
  PRIMARY KEY (`id`))
ENGINE = MyISAM
COMMENT = '渠道商库存';

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_channel_order_goods_profit') . " (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uniacid` INT(11) NOT NULL,
  `order_goods_id` INT(11) NOT NULL COMMENT '商品ID',
  `goods_price` DECIMAL(10,2) NULL COMMENT '商品总额',
  `discount` DECIMAL(10,2) NULL COMMENT '折扣',
  `profit_ratio` DECIMAL(10,2) NULL COMMENT '利润比例',
  `profit` DECIMAL(10,2) NULL COMMENT '利润',
  PRIMARY KEY (`id`))
ENGINE = MyISAM
COMMENT = '渠道商商品利润';

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_channel_stock_log') . " (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uniacid` INT(11) NOT NULL,
  `openid` VARCHAR(50) NULL,
  `goodsid` INT(11) NULL COMMENT '商品ID',
  `every_turn` INT(11) NULL COMMENT '每次进货量',
  `every_turn_price` DECIMAL(10,2) NULL COMMENT '每次进货单价',
  `every_turn_discount` DECIMAL(10,2) NULL COMMENT '每次进货当前折扣',
  `goods_price` DECIMAL(10,2) NULL COMMENT '进货时商品单价',
  PRIMARY KEY (`id`))
ENGINE = MyISAM
COMMENT = '渠道商进货记录';

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_channel_merchant_order') . " (
`id` INT(11) NOT NULL AUTO_INCREMENT,
  `uniacid` INT(11) NOT NULL,
  `openid` VARCHAR(50) NULL,
  `orderid` INT(11) NULL COMMENT '订单ID',
  `commission` DECIMAL(10,2) NULL COMMENT '当前推荐员分红比例',
  `money` DECIMAL(10,2) NULL COMMENT '订单分红',
  PRIMARY KEY (`id`))
ENGINE = MyISAM
COMMENT = '渠道商推荐员订单'";

pdo_fetchall($sql);

if(!pdo_fieldexists('sz_yi_af_channel', 'address')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_af_channel')." ADD `address` varchar(255) DEFAULT NULL;");
}

if(!pdo_fieldexists('sz_yi_af_channel', 'url')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_af_channel')." ADD `url` varchar(100) DEFAULT NULL;");
}

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

if(!pdo_fieldexists('sz_yi_af_channel', 'status')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_af_channel')." ADD `status` tinyint(1) NOT NULL COMMENT '0为申请1为通过';");
}

if(!pdo_fieldexists('sz_yi_chooseagent', 'isopenchannel')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_chooseagent')." ADD `isopenchannel` tinyint(1) NOT NULL COMMENT '0关闭1开启';");
}

if(!pdo_fieldexists('sz_yi_goods', 'isopenchannel')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_goods')." ADD `isopenchannel` tinyint(1) NOT NULL COMMENT '0关闭1开启';");
}

if(!pdo_fieldexists('sz_yi_order_goods', 'ischannelpay')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD `ischannelpay` tinyint(1) NOT NULL COMMENT '0不是1渠道商采购订单';");
}

if (!pdo_fieldexists('sz_yi_channel_apply', 'apply_ordergoods_ids')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_channel_apply')." ADD  `apply_ordergoods_ids` text;");
}

if (!pdo_fieldexists('sz_yi_channel_apply', 'apply_cmaorders_ids')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_channel_apply')." ADD  `apply_cmaorders_ids` text;");
}

if (!pdo_fieldexists('sz_yi_channel_apply', 'apply_selforders_ids')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_channel_apply')." ADD  `apply_selforders_ids` text;");
}

if (!pdo_fieldexists('sz_yi_channel_stock_log', 'paytime')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_channel_stock_log')." ADD  `paytime` INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_channel_stock_log', 'optionid')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_channel_stock_log')." ADD `optionid` INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_channel_stock', 'optionid')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_channel_stock')." ADD `optionid` INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'iscmas')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `iscmas` INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_channel_stock_log', 'type')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_channel_stock_log')." ADD `type` INT(11) DEFAULT '0' COMMENT '1.采购2.下级采购3.零售4.自提';");
}

if(!pdo_fieldexists('sz_yi_channel_stock_log', 'order_goodsid')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_channel_stock_log')." ADD `order_goodsid` INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_channel_stock_log', 'surplus_stock')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_channel_stock_log')." ADD `surplus_stock` INT(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_channel_stock_log', 'mid')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_channel_stock_log')." ADD `mid` INT(11) DEFAULT '0';");
}
message('渠道商插件安装成功', $this->createPluginWebUrl('channel/index'), 'success');