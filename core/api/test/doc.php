<?php
namespace Api;
define("IS_API_DOC", true);
function get_test_para()
{
    require_once __DIR__ . '/../inc/aes.php';
    $test_para = array(
        'member/login' => array(
            'username' => 'admin',
            'password' => 'admin'
        ), 'account/display' => array(
            'uid' => '1',
        ),'goods/display' => array(
            'uid' => '1',
            'uniacid' => '2',

        )
    );
    $aes = new \Common\Org\Aes();
    $api_name = $_GET['api'];
    return $aes->siyuan_aes_encode(json_encode($_POST, JSON_UNESCAPED_UNICODE));
}

;
$_POST['para'] = get_test_para();
require __DIR__ . "/index.php";