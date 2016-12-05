<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/12/3
 * Time: 下午3:37
 */


$url = "http://shop.shanxinhui.com.cn/app/index.php?i=3&c=entry&p=pay_ping&do=order&m=sz_yi&t=11testdingran";
/*
$channel = '';
$amount = '';
$ordersn = '';
$token = '';
*/

//$url = "http://test.yunzshop.com/app/index.php?i=2&c=entry&p=pay_ping&do=order&m=sz_yi";

//$url = "http://shop.shanxinhui.com.cn/t.php?i=3";

$channel = 'wx';
$amount = '998877';
$ordersn = 'SH20161123201047484664';
$token = 'o5yWLwRfzOKam0inShK4svBZiUb9';

load()->func('communication');

$resp    = ihttp_post($url, array(
    'channel' => $channel,
    'amount' => $amount,
    'ordersn' => $ordersn,
    'token' => $token
));
file_put_contents(IA_ROOT . '/addons/sz_yi/data/result.log', print_r($resp, 1));
echo '<pre>';print_r($resp);exit;