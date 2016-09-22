<?php
global $_W;

$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'indiana'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`,`category`) VALUES(". $displayorder .",'indiana','一元夺宝','1.0','官方','1','sale');";
  pdo_fetchall($sql);
}

$sql = " CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_indiana_goods') . " (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL COMMENT '公众号ID',
  `title` varchar(100) NOT NULL COMMENT '夺宝标题',
  `good_id` int(11) NOT NULL COMMENT '商品ID',
  `init_money` tinyint(10) NOT NULL COMMENT '几元专区',
  `max_num` tinyint(10) NOT NULL COMMENT '最大购买数',
  `sort` tinyint(10) NOT NULL COMMENT '排序',
  `price` decimal(10,2) NOT NULL COMMENT '夺宝价格',
  `periods` smallint(6) NOT NULL COMMENT '夺宝期数',
  `max_periods` smallint(5) NOT NULL COMMENT '最大期数',
  `participants_num` int(10) NOT NULL COMMENT '已参与总人次',
  `status` tinyint(3) NOT NULL COMMENT '0:删除1：下架2：上架',
  `create_time` varchar(255) NOT NULL COMMENT '创建时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `ims_sz_yi_indiana_goods` ADD PRIMARY KEY (`id`);

ALTER TABLE `ims_sz_yi_indiana_goods` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
";
pdo_query($sql);


message('一元夺宝插件安装成功', $this->createPluginWebUrl('indiana/set'), 'success');


