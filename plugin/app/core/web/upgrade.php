<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/6/22
 * Time: 下午4:52
 */

global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'app'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`, `category`) VALUES(". $displayorder .",'app','APP客户端','1.0','官方','1', 'biz');";
    pdo_query($sql);
}

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_banner'). " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0',
  `thumb_pc` varchar(500) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
";
pdo_query($sql);

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_message'). " (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `openid` varchar(255) NOT NULL COMMENT '用户openid',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `contents` text NOT NULL COMMENT '内容',
  `status` set('0','1') NOT NULL DEFAULT '0' COMMENT '0-未读；1-已读',
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
";
pdo_query($sql);

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_push'). " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(50) DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `content` text,
  `time` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
";
pdo_query($sql);

$sql = "
ALTER TABLE " . tablename('sz_yi_member'). " ADD `bindapp` tinyint(4) NOT NULL DEFAULT '0';
";
if(!pdo_fieldexists('sz_yi_member', 'bindapp')) {
    pdo_query($sql);
}

message('芸众APP客户端插件安装成功', $this->createPluginWebUrl('app/index'), 'success');