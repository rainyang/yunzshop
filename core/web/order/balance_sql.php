<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/11/28
 * Time: 上午9:52
 */

global $_W;

$setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $_W['uniacid']
));

$set     = unserialize($setdata['sets']);

$set['trade']['withdrawnocheck'] = 0;

$data = array('sets' => iserializer($set));

pdo_update('sz_yi_sysset', $data, array(
    'uniacid' => $_W['uniacid']
));
