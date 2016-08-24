<?php
//余额提现 手续费 2016-08-23
if(!pdo_fieldexists('sz_yi_member_log', 'poundage')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_log')."ADD `poundage` DECIMAL(10,2) NOT NULL AFTER `money`;");
}
//余额提现 提现金额 2016-08-23
if(!pdo_fieldexists('sz_yi_member_log', 'withdrawal_money')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_log')."ADD `withdrawal_money` DECIMAL(10,2) NOT NULL AFTER `poundage`;");
}


