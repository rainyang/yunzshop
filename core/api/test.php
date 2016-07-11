<?php
namespace Api;
define("IS_TEST",true);
function get_test_para(){
    require_once __DIR__.'/../inc/aes.php';
    $test_para = array(
        'member/login'=>array(
            'username'=>'admin',
            'password'=>'admin'
        ),
    );
    $aes = new \Common\Org\Aes();
    $api_name = $_GET['api'];
    return $aes->siyuan_aes_encode(json_encode($test_para[$api_name], JSON_UNESCAPED_UNICODE));
};
$_POST['para'] = get_test_para();
require __DIR__."/index.php";