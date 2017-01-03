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
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'wxapp'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`, `category`) VALUES(". $displayorder .",'wxapp','小程序','1.0','官方','1', 'biz');";
    pdo_query($sql);
}

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_wxapp'). " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `switch` tinyint(1) DEFAULT '',
  `appid` varchar(255) DEFAULT '',
  `secret` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
";
pdo_query($sql);

message('芸众小程序端插件安装成功', $this->createPluginWebUrl('wxapp/index'), 'success');