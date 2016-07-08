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
message('渠道商插件安装成功', $this->createPluginWebUrl('channel/index'), 'success');