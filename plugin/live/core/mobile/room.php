<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/12/16
 * Time: 上午9:14
 */

load()->func('communication');

//获取apk的下载地址
$remoteUrl = "http://lbj.yunzshop.com"; //todo 临时地址,李宝佳更新该地址
$url = $remoteUrl . '/web/index.php?c=account&a=apkupgrade&operation=livedown&signature=sz_cloud_register';
$result = ihttp_get($url);
$result_array = json_decode($result['content'], true);
$filename = $result_array['parms']['filename'];
$path = $result_array['parms']['path'];

$apkUrl = $remoteUrl . '/apk/live/' . $path . '/' . $filename;


include $this->template('room');