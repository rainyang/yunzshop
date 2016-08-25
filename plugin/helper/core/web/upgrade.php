<?php
global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'helper'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`, `category`) VALUES(". $displayorder .",'helper','帮助中心','1.0','官方','1', 'biz');";
  pdo_query($sql);
}

if(!pdo_fieldexists('sz_yi_article', 'is_helper')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article')." ADD `is_helper` int(11) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('sz_yi_article_category', 'is_helper')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_article_category')." ADD `is_helper` int(11) NOT NULL DEFAULT '0';");
}

message('芸众帮助中心安装成功', $this->createPluginWebUrl('helper/index'), 'success');
