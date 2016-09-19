<?php
error_reporting(0);
require '../../framework/bootstrap.inc.php';
require '../../addons/sz_yi/defines.php';
require '../../addons/sz_yi/core/inc/functions.php';
require '../../addons/sz_yi/core/inc/plugin/plugin_model.php';
global $_W, $_GPC;
ignore_user_abort();
set_time_limit(0);
$sets = pdo_fetchall('select uniacid from ' . tablename('sz_yi_sysset'));
foreach ($sets as $set) {
    m('order')->autoexec($set['uniacid']);
    $pbonus = p('bonus');
    if(!empty($pbonus)){
        $pbonus->autoexec($set['uniacid']);
    }
}
echo "ok……";