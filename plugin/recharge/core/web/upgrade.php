<?php
global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
ca('recharge.upgrade');
$result = pdo_fetchcolumn('SELECT id FROM ' . tablename('sz_yi_plugin') .
    ' WHERE identity = :identity ',
    array(
        ':identity' => 'recharge'
    )
);
if (empty($result)) {
    $displayorder_max = pdo_fetchcolumn('SELECT max(displayorder) FROM ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') .
        " (`displayorder`, `identity`, `name`, `version`, `author`, `status`, `category`, `desc`) 
        VALUES (" . $displayorder . ", 'recharge', '手机充值', '1.0', '官方', '1', 'biz', '手机业务快速充值');";
  pdo_fetchall($sql);
}
$sql = " CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_recharge_adv') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `advname` varchar(50) NOT NULL,
  `link` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `displayorder` int(11) NOT NULL,
  `isshow` int(1) NOT NULL DEFAULT '1',
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1 ;";
pdo_query($sql);
if(!pdo_fieldexists('sz_yi_goods', 'province')) {
    pdo_query("ALTER TABLE " . tablename('sz_yi_goods') .
        " ADD `province` varchar(255) NOT NULL COMMENT '流量商品限制省份';");
}
if(!pdo_fieldexists('sz_yi_goods', 'operator')) {
    pdo_query("ALTER TABLE " . tablename('sz_yi_goods') .
        " ADD `operator` int(1) NOT NULL COMMENT '流量商品限制运营商';");
}
if(!pdo_fieldexists('sz_yi_goods', 'isprovince')) {
    pdo_query("ALTER TABLE " . tablename('sz_yi_goods') .
        " ADD `isprovince` int(1) NOT NULL COMMENT '流量商品限制流量类型 1：省内，2：国内';");
}

message('手机充值插件安装成功', $this->createPluginWebUrl('recharge/index'), 'success');
