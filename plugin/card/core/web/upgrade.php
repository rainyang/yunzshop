<?php
global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'card'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`,`category`) VALUES(". $displayorder .",'card','代金卡','1.0','官方','1','sale');";
  pdo_query($sql);
}
$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_gift_card') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `total` int(11) NOT NULL DEFAULT '0' COMMENT '发放数量',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '面额',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '发放时间',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='发放代金卡数据' AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_card_data') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `gift_id` int(11) NOT NULL DEFAULT '0' COMMENT '发放id',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '面额',
  `isday` tinyint(3) NOT NULL DEFAULT '0' COMMENT '1固定天数，2时间区间',
  `timestart` int(11) NOT NULL DEFAULT '0' COMMENT '时间区间开始',
  `timeend` int(11) NOT NULL DEFAULT '0' COMMENT '时间区间结束',
  `validity_period` int(11) NOT NULL DEFAULT '0' COMMENT '有效时间',
  `cdkey` varchar(50) NOT NULL DEFAULT '0' COMMENT 'cdkey',
  `isbind` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0未绑定，1已绑定',
  `openid` varchar(50) NOT NULL DEFAULT '0' COMMENT '绑定者openid',
  `bindtime` int(11) NOT NULL DEFAULT '0' COMMENT '绑定时间',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `isoverdue` tinyint(3) NOT NULL DEFAULT '0' COMMENT '1已过期',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_openid`(`openid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='代金卡数据' AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_card_log') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `orderid` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `spending` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '花销',
  `card_id` int(11) NOT NULL DEFAULT '0' COMMENT '代金卡id',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='代金卡消费记录' AUTO_INCREMENT=1 ;
";
pdo_query($sql);

if(!pdo_fieldexists('sz_yi_card_data', 'cdkey')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_card_data')." ADD UNIQUE(`cdkey`);");
}

if(!pdo_fieldexists('sz_yi_order', 'cardid')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `cardid` int(11) DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_order', 'cardprice')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `cardprice` decimal(10,2) DEFAULT '0';");
}

message('芸众代金卡插件安装成功', $this->createPluginWebUrl('card/index'), 'success');
