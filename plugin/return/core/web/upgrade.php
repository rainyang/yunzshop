<?php

global $_W;

$info = pdo_fetch('select * from ' . tablename('sz_yi_plugin') . ' where identity= "supplier"  order by id desc limit 1');
if(!$info){
    $sql = "INSERT INTO `yunzshop-pc`.`ims_sz_yi_plugin` (`id`, `displayorder`, `identity`, `name`, `version`, `author`, `status`, `category`) VALUES (NULL, '999', 'return', '全返系统', '1.0', '官方', '1', 'biz')";
    pdo_query($sql);
}

$sql = "
CREATE TABLE IF NOT EXISTS `ims_sz_yi_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `return_money` decimal(10,2)  NOT NULL,
  `create_time` varchar(60) NOT NULL,
  `status` tinyint(2)  NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_sz_yi_return_money` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL COMMENT '用户id',
  `uniacid` int(11) NOT NULL,
  `money` decimal(10,2) NOT NULL COMMENT '累计订单金额',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
pdo_query($sql);

if(!pdo_fieldexists('sz_yi_goods', 'isreturn')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_goods')." ADD `isreturn` int(1) NOT NULL COMMENT '全返开关';");
}


message('全返系统插件安装成功', $this->createPluginWebUrl('return/set'), 'success');
