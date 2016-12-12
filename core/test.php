<?php
define("IS_TEST", true);

if (!function_exists("dump")) {

    function dump($var, $echo = true, $label = null, $strict = true)
    {
        if (!defined('IS_TEST')) {
            return;
        }
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        } else
            return $output;
    }
}
function get_test_para()
{
    require_once __DIR__ . '/../inc/aes.php';
    $api_db = file_get_contents(__DIR__ . '/test/para.json');
    //var_dump($api_db);
    $api_db = json_decode($api_db, true);
    //var_dump($api_db);exit;
    $aes = new \Aes('hrbin-yunzs-2016', '');
    $api_name = explode('/', $_GET['api']);
    $group_name = array_shift($api_name);
    $method_name = array_shift($api_name);

    $para = $api_db[$group_name]['method'][$method_name]['para'];
    //var_dump($para);
    return $aes->siyuan_aes_encode(json_encode($para));

}

$_POST['para'] = get_test_para();
//var_dump($_POST['para']);

require __DIR__ . "/index.php";