<?php
global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
ca('love.upgrade');
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'love'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`,`category`) VALUES(". $displayorder .",'love','爱心基金','1.0','官方','1','sale');";
  pdo_fetchall($sql);
}
$sql = "CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_love_log') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `mid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `money` decimal(10,2) DEFAULT '0.00',
  `paymonth` TINYINT(1) DEFAULT '0' COMMENT '1为积分 2为余额',
  `type` TINYINT(1) DEFAULT '0' COMMENT '1为购物 2为捐赠 3为文章',
  `goodsid` int(11) DEFAULT '0' COMMENT,
  `status` TINYINT(1) DEFAULT '0' COMMENT '0为未使用 1为使用',
  `createtime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='爱心基金记录' AUTO_INCREMENT=1 ;";
pdo_fetchall($sql);

if(!pdo_fieldexists('sz_yi_goods', 'love_money')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `love_money` decimal(10,2) DEFAULT '0.00';");
}

/*if(!pdo_fieldexists('sz_yi_member', 'bonus_area')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `bonus_area` TINYINT(1) DEFAULT '0' AFTER `bonuslevel`, ADD `bonus_province` varchar(50) DEFAULT '' AFTER `bonus_area`, ADD `bonus_city` varchar(50) DEFAULT '' AFTER `bonus_province`, ADD `bonus_district` varchar(50) DEFAULT '' AFTER `bonus_city`, ADD `bonus_area_commission` decimal(10,2) DEFAULT '0.00' AFTER `bonus_district`;");
}*/

message('爱心分红插件安装成功', $this->createPluginWebUrl('love/log'), 'success');