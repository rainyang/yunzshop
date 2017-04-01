<?php
defined('IN_IA') or exit('Access Denied');
global $_W;
$master_db_config = $_W['config']['db']['master'];
return array(
    "DB_TYPE" =>"mysqli",
     "DB_HOST" => $master_db_config['host'],
    "DB_NAME" => $master_db_config['database'],
    "DB_USER" => $master_db_config['username'],
    "DB_PWD" => $master_db_config['password'],
    "DB_PREFIX" => $master_db_config['tablepre'],
    "DB_FIELDS_CACHE" => false,
);

/** ["host"] => string(9) "127.0.0.1"
  ["username"] => string(4) "root"
  ["password"] => string(6) "123456"
  ["port"] => string(4) "3306"
  ["database"] => string(8) "yunzhong"
  ["charset"] => string(4) "utf8"
  ["pconnect"] => int(0)
  ["tablepre"] => string(4) "ims_"
{"DB_TYPE" : "mysqli",
"DB_HOST" : "101.201.42.13",
"DB_NAME" : "xike",
"DB_USER" : "xike",
"DB_PWD" : "123456",
"DB_PREFIX" : "xk_",
"DB_FIELDS_CACHE":false}
$config['db']['master']['tablepre'] = 'ims_';*/
