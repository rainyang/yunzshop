<?php
function dump($var, $echo=true, $label=null, $strict=true) {
    if(!defined('IS_TEST')){
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
    }else
        return $output;
}
function is_test(){
    return defined('IS_TEST');
}
function array_part($key,$array){
    if(is_string($key)){
        $key = explode(',',$key);
    }
    return array_intersect_key($array, array_fill_keys($key, 0));
}