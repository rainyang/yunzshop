<?php
//余额提现 手续费 2016-08-23
if(!pdo_fieldexists('sz_yi_member_log', 'poundage')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_log')."ADD `poundage` DECIMAL(10,2) NOT NULL AFTER `money`;");
}
//余额提现 提现金额 2016-08-23
if(!pdo_fieldexists('sz_yi_member_log', 'withdrawal_money')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_log')."ADD `withdrawal_money` DECIMAL(10,2) NOT NULL AFTER `poundage`;");
}

//2016-09-02
//购物间接获得虚拟币
if(!pdo_fieldexists('sz_yi_member', 'virtual_temporary')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')."ADD `virtual_temporary` DECIMAL(10,2) NOT NULL AFTER `virtual_currency`;");
}
//购物间接获得虚拟币总数
if(!pdo_fieldexists('sz_yi_member', 'virtual_temporary_total')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')."ADD `virtual_temporary_total` DECIMAL(10,2) NOT NULL AFTER `virtual_temporary`;");
}
//购物间接获得虚拟币 上级获得
if(!pdo_fieldexists('sz_yi_goods', 'yunbi_commission')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')."ADD `yunbi_commission` DECIMAL(6,3) NOT NULL AFTER `yunbi_consumption`;");
}
//修改 虚拟币返现比例
if(pdo_fieldexists('sz_yi_goods', 'yunbi_consumption')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')."CHANGE `yunbi_consumption` `yunbi_consumption` DECIMAL(6,3) NOT NULL;");
}
//2016-09-07 购买人ID
if(!pdo_fieldexists('sz_yi_yunbi_log', 'buy_mid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_yunbi_log')."ADD `buy_mid` INT NOT NULL AFTER `status`;");
}
//2016-09-08 是否开启报单
if (!pdo_fieldexists('sz_yi_goods', 'isdeclaration')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `isdeclaration` TINYINT(1) NOT NULL DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_goods', 'virtual_declaration')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `virtual_declaration` DECIMAL(10,2) NOT NULL AFTER `isdeclaration`;");
}
if (!pdo_fieldexists('sz_yi_diyform_temp', 'declaration_mid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_diyform_temp')." ADD `declaration_mid` INT NOT NULL AFTER `diyformdata`;");
}
if (!pdo_fieldexists('sz_yi_order_goods', 'declaration_mid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD `declaration_mid` INT NOT NULL AFTER `ischannelpay`;");
}


if (!pdo_fieldexists('sz_yi_goods', 'return_appoint_amount')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `return_appoint_amount` DECIMAL(10,2) NOT NULL COMMENT '全返分红金额' AFTER `plugin`;");
}


//会员升级指定商品
if (!pdo_fieldexists('sz_yi_member_level', 'goodsid')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_level')." ADD `goodsid` INT NOT NULL COMMENT '购买指定商品成为指定会员等级' AFTER `discount`;");
}
//会员升级时间
if (!pdo_fieldexists('sz_yi_member', 'upgradeleveltime')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `upgradeleveltime` VARCHAR(255) NOT NULL AFTER `level`;");
}

//订单表添加购物积分
if (!pdo_fieldexists('sz_yi_order', 'credit1')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `credit1` INT(11) NOT NULL COMMENT '购物积分' AFTER `goodsprice`;");
}

$sql = "CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_return_tpm')." (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `money` decimal(10,2) NOT NULL COMMENT '队列金额',
  `returnrule` tinyint(1) NOT NULL COMMENT '队列类型',
  `status` tinyint(1) NOT NULL COMMENT '状态',
  `create_time` varchar(60) NOT NULL COMMENT '创建时间',
  `update_time` varchar(60) NOT NULL COMMENT '更新时间',
  `queue` int(11) NOT NULL COMMENT '队列ID'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='全返队列临时表';

ALTER TABLE ".tablename('sz_yi_return_tpm')." ADD PRIMARY KEY (`id`);

ALTER TABLE ".tablename('sz_yi_return_tpm')." MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
pdo_fetchall($sql);



