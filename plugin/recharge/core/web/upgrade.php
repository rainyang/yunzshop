<?php
global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
ca('recharge.upgrade');
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'recharge'));

if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`,`category`,`desc`) VALUES(". $displayorder .",'recharge','手机充值','1.0','官方','1','biz','手机业务快速充值');";
  pdo_fetchall($sql);
}
if(!pdo_fieldexists('sz_yi_goods', 'province')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_goods')." ADD `province` varchar(255) NOT NULL COMMENT '流量商品限制省份';");
}
if(!pdo_fieldexists('sz_yi_goods', 'operator')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_goods')." ADD `operator` int(1) NOT NULL COMMENT '流量商品限制运营商';");
}
if(!pdo_fieldexists('sz_yi_goods', 'isrecharge')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_goods')." ADD `isrecharge` int(1) NOT NULL COMMENT '是否是充值商品';");
}

message('手机充值插件安装成功', $this->createPluginWebUrl('recharge/index'), 'success');
