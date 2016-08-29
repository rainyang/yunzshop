<?php
namespace Api;
define("IS_TEST", true);
function get_test_para()
{
    require_once __DIR__ . '/../inc/aes.php';
    $api_db = file_get_contents(__DIR__ . '/test/para.json');
    $api_db = json_decode($api_db, true);
//dump($api_db);exit;
    $aes = new \Aes('hrbin-yunzs-2016','');
    $api_name = explode('/',$_GET['api']);
    $group_name = array_shift($api_name);
    $method_name = array_shift($api_name);

    $para = $api_db[$group_name]['method'][$method_name]['para'];
    //var_dump($para);
    return $aes->siyuan_aes_encode(json_encode($para, JSON_UNESCAPED_UNICODE));

}
$_POST['para'] = get_test_para();
//var_dump($_POST['para']);

require __DIR__ . "/index.php";