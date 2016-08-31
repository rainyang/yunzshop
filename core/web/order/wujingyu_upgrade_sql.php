<?php
//收银台添加会员中心是否可以编辑的字段
if(!pdo_fieldexists('sz_yi_cashier_store', 'centercan')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_cashier_store')." ADD    `centercan` tinyint(1) DEFAULT '1';");
}
