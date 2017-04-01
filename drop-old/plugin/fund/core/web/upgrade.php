<?php
global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
ca('fund.upgrade');
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'fund'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`,`category`) VALUES(". $displayorder .",'fund','芸众众筹','1.0','官方','1','biz');";
  pdo_fetchall($sql);
}
$sql = "CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_fund_goods') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `goodsid` int(11) DEFAULT '0',
  `allprice` int(11) DEFAULT '0',
  `desc` varchar(255) DEFAULT '',
  `allrefund` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='众筹商品表' AUTO_INCREMENT=1 ;
";
pdo_fetchall($sql);
if(!pdo_fieldexists('sz_yi_goods', 'plugin')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `plugin` varchar(10) DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_order_comment', 'plugin')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_comment')." ADD `plugin` varchar(10) DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_order', 'plugin')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `plugin` varchar(10) DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_fund_goods', 'allrefund')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_fund_goods')." ADD `allrefund` TINYINT(1) DEFAULT '0';");
}
message('芸众众筹插件安装成功', $this->createWebUrl('shop/goods', array('plugin' => 'fund')), 'success');