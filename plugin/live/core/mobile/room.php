<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/12/16
 * Time: 上午9:14
 */
define("REMOTEURL","http://cloud.yunzshop.com");

load()->func('communication');

//获取apk的下载地址
$url = REMOTEURL . '/web/index.php?c=account&a=apkupgrade';
$params = array(
        'operation'=>'livedown',
        'signature'=>'sz_cloud_register'
    );
$result = ihttp_require($url, $params);
$result_array = json_decode($result['content'], true);
$filename = $result_array['parms']['filename'];
$path = $result_array['parms']['path'];

$apkUrl = REMOTEURL . '/apk/live/' . $path . '/' . $filename;


include $this->template('room');