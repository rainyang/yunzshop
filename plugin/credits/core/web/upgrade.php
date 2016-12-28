<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/12/27
 * Time: 下午3:31
 */

global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'credits'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`, `category`) VALUES(". $displayorder .",'credits','积分兑换','1.0','官方','1', 'biz');";
    pdo_query($sql);
}

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_eblock_log'). " (
  `id` INT NOT NULL AUTO_INCREMENT , 
  `uniacid` INT NOT NULL , 
  `uid` INT NOT NULL , 
  `openid` VARCHAR(50)  NOT NULL , 
  `accountnum` FLOAT NOT NULL COMMENT '兑换VC' , 
  `beforebalance` FLOAT NOT NULL COMMENT ' 兑换前VC总额' , 
  `afterbalance` FLOAT NOT NULL COMMENT '兑换后VC总额' , 
  `createtime` DATETIME NOT NULL COMMENT '兑换时间' , 
  `remark` VARCHAR(255) NOT NULL COMMENT '备注' , 
  `status` TINYINT NOT NULL COMMENT '状态' , 
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT = 'eblock积分兑换日志表';
";
pdo_query($sql);

message('积分兑换插件安装成功', $this->createPluginWebUrl('credits/index'), 'success');
