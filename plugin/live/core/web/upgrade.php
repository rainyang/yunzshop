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
$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'live'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`, `category`, `desc`) VALUES(". $displayorder .",'live','视频直播','1.0','官方','1', 'biz', '视频直播');";
    pdo_query($sql);
}
$uniacid = $_W['uniacid'];

//基础设置表
$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_live_base'). " (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uniacid` INT(11) NOT NULL DEFAULT '0',
  `conditions` TINYINT(4) NOT NULL COMMENT '主播条件',
  `is_check` SET('0','1') NOT NULL  DEFAULT '0' COMMENT '主播是否审核',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 COMMENT= '基础设置' DEFAULT CHARSET=utf8;
";
pdo_query($sql);
//写入基础设置表的默认值
$sql = 
"INSERT INTO " . tablename('sz_yi_live_base') . " (uniacid, conditions, is_check) VALUES (" . $uniacid . ", 1, 0 )"
;
pdo_query($sql);

//幻灯片
$sql = "
CREATE TABLE IF NOT EXISTS " .  tablename('sz_yi_live_banner') . " (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uniacid` INT(11) NOT NULL DEFAULT '0',
  `advname` VARCHAR(50) NOT NULL DEFAULT '',
  `link` VARCHAR(255) NOT NULL DEFAULT '',
  `thumb` VARCHAR(255) NOT NULL DEFAULT '',
  `displayorder` INT(11) NOT NULL DEFAULT '0',
  `enabled` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 COMMENT= '幻灯片表' DEFAULT CHARSET=utf8;
";
pdo_query($sql);

//主播表
$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_live_anchor') . " ( 
    `id` INT NOT NULL AUTO_INCREMENT ,   
    `uniacid` INT NOT NULL DEFAULT '0' , 
    `uid` INT NOT NULL DEFAULT '0' COMMENT '会员id' , 
    `openid` VARCHAR(50) NOT NULL DEFAULT '0' COMMENT 'openid' ,
    `cloud_anchor_id` INT NOT NULL DEFAULT '0' COMMENT '云端主播id' ,
    `cloud_room_id` INT NOT NULL DEFAULT '0' COMMENT '云端房间id' , 
    `status` SET('0','1','2','3')  NOT NULL DEFAULT '0' COMMENT '是否审核通过,0表示待审核,1表示审核通过,2表示审核被拒绝,3表示被禁播',
    `createtime` DATETIME NOT NULL DEFAULT '0000-00-00' COMMENT '创建日期' ,  
    PRIMARY KEY (`id`)
) ENGINE = MyISAM COMMENT = '主播表';";
pdo_query($sql);

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_live_reminder') . " ( 
`id` INT NOT NULL AUTO_INCREMENT , 
`aid` INT NOT NULL COMMENT '主播编号' , 
`mobile` VARCHAR(11) NOT NULL COMMENT '手机号' , 
`auth_img0` VARCHAR(255) NOT NULL COMMENT '身份证正面' , 
`auth_img1` VARCHAR(255) NOT NULL COMMENT '身份证反面' , 
PRIMARY KEY (`id`)
) ENGINE = MyISAM COMMENT = '主播申请资料表';";
pdo_query($sql);

//订单表增加fromanchor字段 (用于标识是哪个主播引流的订单)
$sql = "ALTER table " . tablename('sz_yi_order') . " ADD COLUMN fromanchor smallint DEFAULT 0 COMMENT '(值是云端的直播ID)用于标识直播引流, 值为0时表示非直播引流订单' AFTER deductcommission";
pdo_query($sql);


message('芸众视频直播插件安装成功', $this->createPluginWebUrl('live/index'), 'success');