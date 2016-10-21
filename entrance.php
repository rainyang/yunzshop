<?php
/*=============================================================================
#     FileName: entrance.php
#         Desc: 
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 1.8.6
#   LastChange: 2016-09-19 10:00:00
#      History:
=============================================================================*/

error_reporting(0);
require '../../framework/bootstrap.inc.php';
require '../../addons/sz_yi/defines.php';
require '../../addons/sz_yi/core/inc/functions.php';
require '../../addons/sz_yi/core/inc/plugin/plugin_model.php';
global $_W, $_GPC;
set_time_limit(0);

$sql = "SELECT * FROM ". tablename('uni_account'). " as a LEFT JOIN". tablename('account'). " as b ON a.default_acid = b.acid WHERE a.default_acid <> 0 ORDER BY a.`uniacid` DESC ";
$sets = pdo_fetchall($sql);
foreach ($sets as $k => $set) {
    m('order')->autoexec($set['uniacid']);
    $pbonus = p('bonus');
    if(!empty($pbonus)){
        $pbonus->autoexec($set['uniacid']);
    }

    $preturn = p('return');
    if(!empty($preturn)){
        $preturn->autoexec($set['uniacid']);
    }
    $pyunbi = p('yunbi');
    if(!empty($pyunbi)){
        $pyunbi->autoexec($set['uniacid']);
    }
}
echo "<pre>";print_r("ok...");exit;

