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
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'gaohuitong'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`, `category`) VALUES(". $displayorder .",'gaohuitong','高汇通支付','1.0','官方','1', 'biz');";
    pdo_query($sql);
}

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_gaohuitong'). " (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '编号' , 
  `uniacid` INT NOT NULL COMMENT '平台' , 
  `switch` TINYINT(1) NOT NULL COMMENT '显示开关' , 
  `merchant_no` VARCHAR(255) NOT NULL COMMENT '商户号' , 
  `terminal_no` VARCHAR(255) NOT NULL COMMENT '终端号' , 
  `merchant_key` VARCHAR(255) NOT NULL COMMENT '密钥' ,
  `server` VARCHAR(255) NOT NULL COMMENT '服务器' ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT = '高汇通支付';
";
pdo_query($sql);

message('芸众高汇通插件安装成功', $this->createPluginWebUrl('gaohuitong/index'), 'success');