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
