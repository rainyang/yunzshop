<?php
global $_W;

$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'indiana'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`,`category`) VALUES(". $displayorder .",'indiana','一元夺宝','1.0','官方','1','sale');";
  pdo_fetchall($sql);
}

pdo_fetchall(" CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_indiana_goods') . " (
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

ALTER TABLE " . tablename('sz_yi_indiana_goods') . " ADD PRIMARY KEY (`id`);
ALTER TABLE " . tablename('sz_yi_indiana_goods') . " MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");

pdo_fetchall("CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_indiana_period') . " (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL COMMENT '公众号ID',
  `ig_id` int(11) NOT NULL COMMENT '夺宝商品表ID',
  `goodsid` int(11) NOT NULL COMMENT '本期商品ID',
  `period` int(11) NOT NULL COMMENT '该商品第几期',
  `init_money` TINYINT(10) NOT NULL COMMENT '几元专区',
  `mid` int(11) NOT NULL COMMENT '获奖人ID',
  `openid` varchar(145) NOT NULL COMMENT '获奖人openid',
  `nickname` varchar(60) NOT NULL COMMENT '获奖人昵称',
  `avatar` varchar(255) NOT NULL COMMENT '获奖人头像',
  `partakes` int(11) NOT NULL COMMENT '获奖人参与次数',
  `code` varchar(45) NOT NULL COMMENT '获奖码',
  `endtime` varchar(145) NOT NULL COMMENT '本期结束时间',
  `jiexiao_time` VARCHAR(145) NOT NULL COMMENT '揭晓时间',
  `ordersn` varchar(20) NOT NULL COMMENT '订单ID',
  `codes` longtext NOT NULL COMMENT '本期剩余夺宝码',
  `recordid` INT NOT NULL ,
  `shengyu_codes` int(11) NOT NULL COMMENT '剩余夺宝码个数',
  `zong_codes` int(11) NOT NULL COMMENT '总夺宝码个数',
  `allcodes` longtext NOT NULL COMMENT '备份总夺宝码',
  `period_num` varchar(145) NOT NULL COMMENT '期号',
  `canyurenshu` int(11) NOT NULL COMMENT '参与人次数',
  `status` tinyint(4) NOT NULL COMMENT '1进行中2待揭晓3已揭晓4已完成',
  `create_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE " . tablename('sz_yi_indiana_period') . " ADD PRIMARY KEY (`id`);
ALTER TABLE " . tablename('sz_yi_indiana_period') . " MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");

pdo_fetchall("CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_indiana_record') . " (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `openid` varchar(145) NOT NULL,
  `ordersn` varchar(50) NOT NULL COMMENT '订单ID',
  `period_id` int(11) NOT NULL COMMENT '期号ID',
  `period_num` varchar(145) NOT NULL COMMENT '期号',
  `count` int(10) NOT NULL,
  `codes` longtext NOT NULL COMMENT 'code',
  `create_time` varchar(145) NOT NULL,
  `microtime` SMALLINT(3) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE " . tablename('sz_yi_indiana_record') . "  ADD PRIMARY KEY (`id`);
ALTER TABLE " . tablename('sz_yi_indiana_record') . " MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");

pdo_fetchall("CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_indiana_comcode') . " (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `numa` varchar(20) NOT NULL,
  `numb` varchar(11) NOT NULL,
  `periods` varchar(50) NOT NULL,
  `pid` int(11) NOT NULL,
  `wincode` int(11) NOT NULL,
  `arecord` longtext NOT NULL,
  `createtime` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE " . tablename('sz_yi_indiana_comcode') . " ADD PRIMARY KEY (`id`);

ALTER TABLE " . tablename('sz_yi_indiana_comcode') . " MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");

pdo_fetchall("CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_indiana_consumerecord') . " (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `openid` varchar(145) NOT NULL,
  `num` int(11) NOT NULL COMMENT '夺宝数量',
  `ordersn` varchar(20) NOT NULL,
  `codes` LONGTEXT NOT NULL ,
  `period_num` varchar(145) NOT NULL COMMENT '期号',
  `create_time` varchar(145) NOT NULL COMMENT '消费时间',
  `microtime` SMALLINT(3) NOT NULL ,
  `ip` varchar(45) NOT NULL
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE " . tablename('sz_yi_indiana_consumerecord') . " ADD PRIMARY KEY (`id`);

ALTER TABLE " . tablename('sz_yi_indiana_consumerecord') . " MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");


message('一元夺宝插件安装成功', $this->createPluginWebUrl('indiana/set'), 'success');





