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

global $_GPC; $_W;
load()->func('communication');

$data = $_GPC;

if ($data['encrypt'] == md5('yitian_make')) {
    if ($data['operation'] == 'synchronous') {
        //同步apk文件、数据
        $apkinfo = array();
        $apkinfo['apksize']         = $data['apksize'];
        $apkinfo['createtime']      = TIMESTAMP;        //独立创建时间
        $apkinfo['appname']         = $data['appname'];
        $apkinfo['package']         = $data['package'];
        $apkinfo['version_name']    = $data['version_name'];
        $apkinfo['version_code']    = $data['version_code'];
        $apkinfo['apkstatus']       = $data['apkstatus'];
        $apkinfo['apkremark']       = $data['apkremark'];

        $url        = $apkinfo['downloadurl']   = $data['link_url'];
        $filename   = $apkinfo['apkname']       = $data['apkname'];
        $type       = $apkinfo['apktype']       = $data['apktype'];
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

    //$ret = "无效操作！！";
    $ret = array('status' => 0, 'message' => "无效操作！！");
    echo json_encode($ret);
    exit;

} else {
    //echo json_encode( array('status' => 1, 'msg' => 'error'));
    message('错误访问.');
}
//下载APK文件
function getFile($url,$save_dir='',$filename='',$type=0){
    if(trim($url)==''){
        return false;
    }
    if(trim($save_dir)==''){
        $save_dir='./';
    }
    if(0!==strrpos($save_dir,'/')){
        $save_dir.='/';
    }
    //创建保存目录
    if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
        return false;
    }
    //获取远程文件所采用的方法
    if($type){
        $ch=curl_init();
        $timeout=5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $content=curl_exec($ch);
        curl_close($ch);
    }else{
        ob_start();
        readfile($url);
        $content=ob_get_contents();
        ob_end_clean();
    }
    $size=strlen($content);
    //文件大小
    $fp2=@fopen($save_dir.$filename,'a');
    fwrite($fp2,$content);
    fclose($fp2);
    unset($content,$url);
    return array('file_name'=>$filename,'save_path'=>$save_dir.$filename);
}
