<?php
/*=============================================================================
#     FileName: apkupgrade.php
#         Desc: apk升级接口
#   Created by: Sublime Text3.  
#       Author: yitian - http://www.yunzshop.com
#        Email: livsyitian@163.com
#     HomePage: http://shop.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-11-23 11:28:10
#      History:
#         Q Q : 751818588
#         test: ++/.app.index.php?c=entry&p=apkuprade&op=upgrade&do=shop&m=sz_yi
=============================================================================*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';


if ($operation == 'upgrade') {
    $version = grtVersion();
    if ($version['apkstatus'] == 1) {
        echo json_encode($version);
        exit;
    }
    echo json_encode($version);
    exit;

}
function detection()
{
    define('CLOUD_URL', 'http://cloud.yunzshop.com/web/index.php?c=account&a=appupgrade');
    //define('CLOUD_URL', 'http://lyt.yunzshop.com/web/index.php?c=account&a=appupgrade');        //测试链接
    
    $parms = array(
        'signature' =>'sz_cloud_register',
        'domain' => $_SERVER['HTTP_HOST']
        );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, CLOUD_URL);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parms);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $resp  = curl_exec($ch);
    $content = @json_decode($resp, true);

    curl_close($ch);
    if ($resp) {
        return array(
            'status'    => $content['status'],
            'apkstatus' => $content['apkstatus']
        );
    }
    return array('status' => 0, 'msg' => "云端请求失败");
}
function grtVersion()
{
    $version = pdo_fetch('select * from ' . tablename('sz_yi_appinfo') . 'where version_code = (select max(version_code) from ' . tablename('sz_yi_appinfo') . ')');
    $ret = detection();
    if ($ret['status'] == 1 && $ret['apkstatus'] == 1) {
        $version['apkstatus'] = 1;
        return $version;
    }
    // status = 0 : 云端请求失败， status = 1 ：云端注册用户/允许升级，status = 2 ：云端注册用户/禁止升级， status = 3 ：非法用户。
    return array(
        'msg' => $ret['msg'],
        'apkstatus' => 0
    );
}
