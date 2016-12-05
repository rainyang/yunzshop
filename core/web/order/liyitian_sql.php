<?php
if (!pdo_fieldexists('sz_yi_member', 'membermobile')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_member')." ADD `membermobile` VARCHAR(11) DEFAULT '' COMMENT '会员资料手机号';");

    $sql = "UPDATE ".tablename(sz_yi_member)." SET membermobile = mobile, mobile = '' WHERE mobile <> '' AND PWD IS NULL ;";
    pdo_query($sql);
}
//12.2增加app库APK信息数据表
$sql = "CREATE TABLE IF NOT EXISTS" . tablename(`sz_yi_appinfo`). "(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apkname` varchar(255) DEFAULT NULL COMMENT 'apk包名',
  `apktype` varchar(255) DEFAULT NULL COMMENT 'apk类型',
  `apksize` varchar(255) DEFAULT NULL COMMENT 'apk文件大小',
  `createtime` int(11) DEFAULT NULL COMMENT '创建apk时间',
  `appname` varchar(255) DEFAULT NULL COMMENT 'APP应用名称',
  `package` varchar(255) DEFAULT NULL COMMENT 'APP应用包名',
  `version_name` varchar(255) DEFAULT NULL COMMENT 'app版本',
  `version_code` varchar(255) DEFAULT NULL COMMENT '版本代码，用来判断版本',
  `app_icon` varchar(255) DEFAULT NULL COMMENT '图片路径',
  `apkpath` varchar(255) DEFAULT NULL COMMENT '文件储存路径',
  `apkremark` text COMMENT '更新日志',
  `downloadurl` varchar(255) NOT NULL COMMENT '采集地址',
  `clientdownload` varchar(255) NOT NULL COMMENT '客户下载地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='app升级包APK信息记录表【客户端】';
";
pdo_fetchall($sql);

//云端数据库增加APK信息表【只限云端使用，使用时去请粘贴出去独立运行】
/*$sql = "CREATE TABLE IF NOT EXISTS" . tablename(`sz_yi_client_app`). "(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clientid` int(11) DEFAULT NULL COMMENT '客户ID',
  `apkname` varchar(255) DEFAULT NULL COMMENT 'apk包名',
  `apktype` varchar(255) DEFAULT NULL COMMENT 'apk类型',
  `apksize` varchar(255) DEFAULT NULL COMMENT 'apk文件大小',
  `createtime` int(11) DEFAULT NULL COMMENT '创建apk时间',
  `appname` varchar(255) DEFAULT NULL COMMENT 'APP应用名称',
  `package` varchar(255) DEFAULT NULL COMMENT 'APP应用包名',
  `version_name` varchar(255) DEFAULT NULL COMMENT 'app版本',
  `version_code` varchar(255) DEFAULT NULL COMMENT '版本代码，用来判断版本',
  `app_icon` varchar(255) DEFAULT NULL COMMENT '图片路径',
  `apkpath` varchar(255) DEFAULT NULL COMMENT '文件储存路径',
  `appstatus` tinyint(1) DEFAULT '0' COMMENT 'app使用状态，1使用，0禁用',
  `apkstatus` tinyint(1) DEFAULT '0' COMMENT '是否开启app升级，1允许，0禁止',
  `apkremark` text COMMENT '更新日志',
  `synchronous` tinyint(1) DEFAULT '0' COMMENT '同步客户服务器状态，1已同步，2未同步',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='app升级包APK信息记录表[云端]';
";
pdo_query($sql);*/


echo  "运行成功";

/*
CREATE TABLE IF NOT EXISTS `ims_sz_yi_appinfo`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apkname` varchar(255) DEFAULT NULL COMMENT 'apk包名',
  `apktype` varchar(255) DEFAULT NULL COMMENT 'apk类型',
  `apksize` varchar(255) DEFAULT NULL COMMENT 'apk文件大小',
  `createtime` int(11) DEFAULT NULL COMMENT '创建apk时间',
  `appname` varchar(255) DEFAULT NULL COMMENT 'APP应用名称',
  `package` varchar(255) DEFAULT NULL COMMENT 'APP应用包名',
  `version_name` varchar(255) DEFAULT NULL COMMENT 'app版本',
  `version_code` varchar(255) DEFAULT NULL COMMENT '版本代码，用来判断版本',
  `app_icon` varchar(255) DEFAULT NULL COMMENT '图片路径',
  `apkpath` varchar(255) DEFAULT NULL COMMENT '文件储存路径',
  `apkremark` text COMMENT '更新日志',
  `downloadurl` varchar(255) NOT NULL COMMENT '采集地址',
  `clientdownload` varchar(255) NOT NULL COMMENT '客户下载地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='app升级包APK信息记录表【客户端】';*/
