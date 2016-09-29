<?php
/*=============================================================================
#     FileName: 1.4.2.php
#         Desc:
#       Author: RainYang - https://github.com/rainyang
#        Email: rainyang2012@qq.com
#     HomePage: http://rainyang.github.io
#      Version: 0.0.1
#   LastChange: 2016-03-29 19:28:39
#      History:
=============================================================================*/

$sql = "
CREATE TABLE `ims_sz_yi_api_log` (
  `api_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `api` varchar(255) NOT NULL,
  `para` text,
  `date_added` datetime NOT NULL,
  `client_ip` varchar(255) NOT NULL DEFAULT '0.0.0.0',
  `error_info` text NOT NULL,
  `is_error` tinyint(1) NOT NULL DEFAULT '0',
  `is_fix` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`api_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=137175 DEFAULT CHARSET=utf8 COMMENT='接口访问日志';";
pdo_fetchall($sql);
echo 1;
