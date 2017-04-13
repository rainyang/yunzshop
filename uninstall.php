<?php
global $_W;
$sql = "
drop table if exists " . tablename('yz_menu') . " ;
drop table if exists " . tablename('yz_member_relation') . " ;
drop table if exists " . tablename('yz_pay_order') . " ;
drop table if exists " . tablename('yz_pay_order') . " ;
drop table if exists " . tablename('yz_pay_request_data') . " ;

";
pdo_query($sql);
