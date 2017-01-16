<?php
if(!pdo_fieldexists('sz_yi_bonus_log', 'type')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_bonus_log')." ADD `type` tinyint(1) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_bonus', 'bonus_area')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_bonus')." ADD `bonus_area` tinyint(1) DEFAULT '0';");
}
//9.13添加
//充值记录表中添加进行充值中的记录
if (!pdo_fieldexists('sz_yi_member_log', 'underway')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_log')." ADD `underway` tinyint(1) DEFAULT '0';");
}                                          

//9.18添加
if (!pdo_fieldexists('sz_yi_goods', 'catch_id')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `catch_id` int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_goods', 'catch_source')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `catch_source` int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_goods', 'catch_url')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `catch_url` int(11) DEFAULT '0';");
}
if (!pdo_fieldexists('sz_yi_goods', 'minprice')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `minprice` decimal(10,2) DEFAULT '0.00';");
}
if (!pdo_fieldexists('sz_yi_goods', 'maxprice')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `maxprice` decimal(10,2) DEFAULT '0.00';");
}
//9.21添加
if(!pdo_fieldexists('sz_yi_bonus_log', 'goodids')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_bonus_log')." ADD `goodids` text DEFAULT '';");
}
if(!pdo_fieldexists('sz_yi_bonus', 'orderids')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_bonus')." ADD `orderids` text DEFAULT '';");
}
//9.30添加  yangyang
if(p('channel')){
pdo_fetchall("ALTER TABLE ".tablename('sz_yi_af_channel')." CHANGE `diychannelfields` `diychannelfields` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '自定义表单字段';");
pdo_fetchall("ALTER TABLE ".tablename('sz_yi_af_channel')." CHANGE `diychanneldata` `diychanneldata` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '自定义表单数据';");
pdo_fetchall("ALTER TABLE ".tablename('sz_yi_af_channel')." CHANGE `realname` `realname` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '姓名';");
pdo_fetchall("ALTER TABLE ".tablename('sz_yi_af_supplier')." CHANGE `diymemberfields` `diymemberfields` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '自定义表单字段';");
pdo_fetchall("ALTER TABLE ".tablename('sz_yi_af_supplier')." CHANGE `diymemberdata` `diymemberdata` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '自定义表单数据';");
}
//11.9 街道分红
if (!pdo_fieldexists('sz_yi_member', 'bonus_street')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member')." ADD `bonus_street` varchar(50) DEFAULT '' COMMENT '街道分红';");
}

//11.23众筹其它数据库必加字段
if(!pdo_fieldexists('sz_yi_goods', 'plugin')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `plugin` varchar(10) DEFAULT '' COMMENT '插件关联';");
}
if(!pdo_fieldexists('sz_yi_order_comment', 'plugin')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order_comment')." ADD `plugin` varchar(10) DEFAULT '' COMMENT '插件关联';");
}
if(!pdo_fieldexists('sz_yi_order', 'plugin')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `plugin` varchar(10) DEFAULT '' COMMENT '插件关联';");
}
//12.08会员足迹添加更新时间做为排序依据
if(!pdo_fieldexists('sz_yi_member_history', 'utime')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_member_history')." ADD `utime` int(11) DEFAULT '0' COMMENT '更新时间';");
}

//1.3 佣金抵扣所需字段
if(!pdo_fieldexists('sz_yi_goods', 'deductcommission')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `deductcommission` decimal(10, 2) DEFAULT '0.00' COMMENT '佣金抵扣';");
}

if(!pdo_fieldexists('sz_yi_order', 'deductcommission')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_order')." ADD `deductcommission` decimal(10, 2) DEFAULT '0.00' COMMENT '佣金抵扣';");
}

//1.16自动提现状态字段
if(!pdo_fieldexists('sz_yi_commission_apply', 'payauto')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_commission_apply')." ADD `payauto` tinyint(1) DEFAULT '0' COMMENT '自动提现 1为自动提现 0为审核提现';");
}
echo 1;