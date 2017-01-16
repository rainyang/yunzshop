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
define('DOWNLOAD', 'http://cloud.yunzshop.com');
global $_GPC;

if ($_GPC['operation'] == 'synchronous') {
    $apkinfo = $_GPC['apkinfo'];

    $num = $_GPC['num'];
    $time = $_GPC['time'];
    $parms = array(
        'operation' => 'validation',
        'id' => $_GPC['id'],
        'token' => $_GPC['token'],
        'signature' =>'sz_cloud_register'
        );
    $resp = tokenValidation($parms);    //验证token

    if ($resp['status'] == 'OK' && $resp['apkname']) {
        $apkinfo['apkname'] = $resp['apkname'];
        $apkinfo['createtime'] = TIMESTAMP;

        $url = DOWNLOAD . "/apk/" . $num . "/" . $time . "/" .$resp['apkname'];
        //$url = "http://lbj.yunzshop.com/apk/" . $num . "/" . $time . "/" . $resp['apkname'];        //测试使用

        $path = dirname(__FILE__)."/../../../apk/".$apkinfo['createtime'];
        $files = getFile($url, $path, $apkinfo['apkname'], $apkinfo['apktype']);
        if ($files && $files['size'] == $apkinfo['apksize']) {
            $apkinfo['apkpath'] = $path;
            $apkinfo['clientdownload'] = "http://" . $_SERVER['SERVER_NAME']. "/addons/sz_yi/apk/" . $apkinfo['createtime'] . "/" . $apkinfo['apkname'];

            pdo_insert('sz_yi_appinfo', $apkinfo);
            $ret = array('status' => 1, 'message' => "同步操作成功！");
            echo json_encode($ret);
            exit;
        } else {
            $ret = array('status' => 0, 'message' => "同步失败！未能正确下载！！");
            echo json_encode($ret);
            exit;
        }
    }
    //file_put_contents(IA_ROOT."/yitian_file.txt",print_r($resp, true), FILE_APPEND);
    $ret = array('status' => -1, 'message' => "云端验证失败！！");
    echo json_encode($ret);
    exit;
}
if ($_GPC['operation'] == 'update_remark' && $_GPC['apkremark']) {
    $data['apkremark'] = $_GPC['apkremark'];
    $id = pdo_fetch('select id from ' . tablename('sz_yi_appinfo') . 'where version_code = (select max(version_code) from ' . tablename('sz_yi_appinfo') . ')');
    pdo_update('sz_yi_appinfo', $data, array('id' => $id));
    echo json_encode($id);
    exit;
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
    return array('size' => $size, 'save_path' => $save_dir . $filename);
}
function tokenValidation($parms)
{
    $url = DOWNLOAD . "/web/index.php?c=account&a=apkupgrade";
    //$url = "http://lbj.yunzshop.com/web/index.php?c=account&a=apkupgrade";      //测试使用

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parms);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $return  = curl_exec($ch);
    $status = json_decode($return,true);
    curl_close($ch);

    return $status;
}
