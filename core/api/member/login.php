<?php
require __API_ROOT__.'/base.class.php';
//dump($_W);
//dump($_GPC);
$api = new Api\Base();
$para = $api->getPara();
$api->validate('username','password');
$setting = $_W['setting'];

$para['username'] = trim($para['username']);
$record = user_single($para);
dump($record);
$api->returnSuccess($record);