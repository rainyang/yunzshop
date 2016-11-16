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
//订单表添加购物积分
if (!pdo_fieldexists('sz_yi_order', 'credit1')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `credit1` INT(11) NOT NULL COMMENT '购物积分' AFTER `goodsprice`;");
}


