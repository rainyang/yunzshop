<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/6/22
 * Time: 下午4:52
 */

global $_W;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'app'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`) VALUES(". $displayorder .",'app','APP客户端','1.0','官方','1');";
    pdo_query($sql);
}

message('芸众APP客户端插件安装成功', $this->createPluginWebUrl('app/index'), 'success');