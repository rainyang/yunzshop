<?php

global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'hotel'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`,`category`) VALUES(". $displayorder .",'hotel','酒店插件','1.0','官方','1','biz');";
  pdo_query($sql);
}
$sql = "CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_hotel_room') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goodsid` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `oprice` decimal(10) DEFAULT '2',
  `cprice` decimal(10) DEFAULT '2',
  `deposit` decimal(10) DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='酒店房间表' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_hotel_room_price') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roomid` int(11) DEFAULT '0',
  `roomdate` int(11) DEFAULT '0',
  `thisdate` varchar(255) DEFAULT '',
  `oprice` decimal(10) DEFAULT '2',
  `cprice` decimal(10) DEFAULT '2',
  `mprice` decimal(10) DEFAULT '2',
  `num` varchar(255) DEFAULT '',
  `status` int(11) DEFAULT '0',

  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='酒店房间价格表' AUTO_INCREMENT=1 ;

";
 pdo_query($sql);
// if(!pdo_fieldexists('sz_yi_member', 'bonuslevel')) {
//   pdo_query("ALTER TABLE ".tablename('sz_yi_member')." ADD `bonuslevel` INT DEFAULT '0' AFTER `agentlevel`, ADD `bonus_status` TINYINT(1) DEFAULT '0' AFTER `bonuslevel`;");
// }

// if(!pdo_fieldexists('sz_yi_member', 'bonus_area')) {
//   pdo_query("ALTER TABLE ".tablename('sz_yi_member')." ADD `bonus_area` TINYINT(1) DEFAULT '0' AFTER `bonuslevel`, ADD `bonus_province` varchar(50) DEFAULT '' AFTER `bonus_area`, ADD `bonus_city` varchar(50) DEFAULT '' AFTER `bonus_province`, ADD `bonus_district` varchar(50) DEFAULT '' AFTER `bonus_city`, ADD `bonus_area_commission` decimal(10,2) DEFAULT '0.00' AFTER `bonus_district`;");
// }

// if(!pdo_fieldexists('sz_yi_goods', 'bonusmoney')) {
//   pdo_query("ALTER TABLE ".tablename('sz_yi_goods')." ADD `bonusmoney` DECIMAL(10,2) AFTER `costprice`;");
// }

// if(!pdo_fieldexists('sz_yi_bonus_goods', 'bonus_area')) {
//   pdo_query("ALTER TABLE ".tablename('sz_yi_bonus_goods')." ADD `bonus_area` TINYINT(1) DEFAULT '0' AFTER `levelid`;");
// }

message('酒店插件安装成功', $this->createPluginWebUrl('hotel/room_status'), 'success');