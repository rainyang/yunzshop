<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/10/29
 * Time: 下午12:33
 */

global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'discuz'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`, `category`, `desc`) VALUES(". $displayorder .",'discuz','Discuz数据整合','1.0','官方','1', 'biz', 'Discuz&商城会员信息、积分同步');";
    pdo_query($sql);
}

if(!pdo_fieldexists('sz_yi_member_group', 'groupid')) {
    $sql = "ALTER TABLE " . tablename('sz_yi_member_group'). " ADD `groupid` INT NOT NULL DEFAULT '0' COMMENT '论坛用户组id';
";

    pdo_query($sql);
}

if(!pdo_fieldexists('sz_yi_member_group', 'status')) {
    $sql = "ALTER TABLE " . tablename('sz_yi_member_group'). " ADD `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-未同步；1-同步';
";

    pdo_query($sql);
}

if(!pdo_fieldexists('uni_settings', 'wx')) {
    $sql = "
ALTER TABLE " . tablename('uni_settings'). " ADD `wx` varchar(500) NULL COMMENT '微信开放平台appid appsecret';

ALTER TABLE " . tablename('uni_settings') . " MODIFY `uc` varchar(600);";

    pdo_query($sql);
}

/*$sql = "ALTER TABLE " . tablename('uni_settings') . " MODIFY `uc` varchar(600);";
pdo_fetch($sql);*/



message('芸众Discuz会员数据同步插件安装成功', $this->createPluginWebUrl('discuz/index'), 'success');