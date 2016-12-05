<?php
/*=============================================================================
#     FileName: apkupload.php
#         Desc: apk同步上传操作
#   Created by: Sublime Text3.  
#       Author: yitian - http://www.yunzshop.com
#        Email: livsyitian@163.com
#     HomePage: http://shop.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-11-23 11:28:10
#      History:
#         Q Q : 751818588
=============================================================================*/

global $_GPC;

if ($_GPC['operation'] == 'synchronous' && $_GPC['encrypt'] == md5('yitian_make')) {
    //同步apk文件、数据
    $apkinfo = array();
    $apkinfo['apksize']         = $_GPC['apksize'];
    $apkinfo['createtime']      = TIMESTAMP;        //独立创建时间
    $apkinfo['appname']         = $_GPC['appname'];
    $apkinfo['package']         = $_GPC['package'];
    $apkinfo['version_name']    = $_GPC['version_name'];
    $apkinfo['version_code']    = $_GPC['version_code'];
    $apkinfo['apkremark']       = $_GPC['apkremark'];

    $url        = $apkinfo['downloadurl']   = $_GPC['link_url'];
    $filename   = $apkinfo['apkname']       = $_GPC['apkname'];
    $type       = $apkinfo['apktype']       = $_GPC['apktype'];
    $path = dirname(__FILE__)."/../../../apk/".$apkinfo['createtime'];

    $file = getFile($url, $path, $filename, $type);
    if ($file) {
        $apkinfo['apkpath'] = $file['save_path'];
        $apkinfo['clientdownload'] = $_SERVER['SERVER_NAME']. "/addons/sz_yi/apk/" . $apkinfo['createtime'] . "/" . $apkinfo['apkname'];

        pdo_insert('sz_yi_client_app', $apkinfo);
        //$ret = "同步操作成功！";
        $ret = array('status' => 1, 'message' => "同步操作成功！");
        echo json_encode($ret);
        exit;
    } else {
        $ret = array('status' => 0, 'message' => "同步失败！未能正确下载！！");
        echo json_encode($ret);
        exit;
    }
}
message('错误访问.');

//下载APK文件
function getFile($url, $save_dir = '', $filename = '', $type = 0){
    if(trim($url) == '') {
        return false;
    }
    if(trim($save_dir) == '') {
        $save_dir='./';
    }
    if(0 !== strrpos($save_dir,'/')) {
        $save_dir.='/';
    }
    //创建保存目录
    if(!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
        return false;
    }
    //获取远程文件所采用的方法
    if($type){
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $content = curl_exec($ch);
        curl_close($ch);
    }else{
        ob_start();
        readfile($url);
        $content = ob_get_contents();
        ob_end_clean();
    }
    $size = strlen($content);
    //文件大小
    $fp2 = @fopen($save_dir . $filename,'a');
    fwrite($fp2, $content);
    fclose($fp2);
    unset($content, $url);
    return array('file_name' => $filename, 'save_path' => $save_dir . $filename);
}
