<?php

//define('IN_IA', true);
// 设置用户定义的错误处理函数
function myErrorHandler($errno, $errstr, $errfile, $errline) {
    echo "<b>Custom error:</b> [$errno] $errstr<br>";
    echo " Error on line $errline in $errfile<br>";
}
    function getExitInfo()
    {

        if(!function_exists('shutdown_find_exit')) {
            function shutdown_find_exit()
            {
                dd($GLOBALS['dbg_stack']);
            }
        }
        register_shutdown_function('shutdown_find_exit');
        if(!function_exists('write_dbg_stack')) {

            function write_dbg_stack()
            {
                $GLOBALS['dbg_stack'] = debug_backtrace();
            }
        }
        register_tick_function('write_dbg_stack');
        declare(ticks = 1);
    }
if(isset($_GET['hard'])){
    set_error_handler("myErrorHandler");
    getExitInfo();
}
require '../../framework/bootstrap.inc.php';
//define('IA_ROOT', str_replace("\\", '/', dirname(dirname(dirname(__FILE__)))));

//require IA_ROOT . '/framework/class/loader.class.php';

include_once __DIR__ . '/app/laravel.php';

include_once __DIR__ . '/app/yunshop.php';