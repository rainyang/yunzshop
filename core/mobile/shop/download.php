<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/4/29
 * Time: 上午8:33
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}

$wx_client = 1;
$m_client  = 2;

//获取USER AGENT
$agent = strtolower($_SERVER['HTTP_USER_AGENT']);

$is_iphone = (strpos($agent, 'iphone')) ? true : false;
$is_ipad = (strpos($agent, 'ipad')) ? true : false;

$setdata = m("cache")->get("sysset");
$set     = unserialize($setdata['sets']);
$app = $set['app']['base'];

$data = array();

$flag = 0;
//微信浏览器判断
if  (strpos($agent, 'micromessenger')) {
    $flag = $wx_client;
    $url  = '';
} else {
    $flag = $m_client;

    if ($is_iphone || $is_ipad) {
        $url = $app['ios_url'];
    } else {
        $url = $app['android_url'];
    }
}

$this->setHeader();
include $this->template('shop/download');


