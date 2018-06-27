<?php
if (!function_exists("getExitInfo")) {

    function getExitInfo()
    {
        function shutdown_find_exit()
        {
            print_r(debug_backtrace(0,2));
        }

        register_shutdown_function('shutdown_find_exit');
        function write_dbg_stack()
        {
            $GLOBALS['dbg_stack'] = debug_backtrace(0,2);
        }

        register_tick_function('write_dbg_stack');
        declare(ticks = 1);
    }
}
//getExitInfo();
//define('IN_IA', true);

require '../../framework/bootstrap.inc.php';
//define('IA_ROOT', str_replace("\\", '/', dirname(dirname(dirname(__FILE__)))));

//require IA_ROOT . '/framework/class/loader.class.php';

include_once __DIR__ . '/app/laravel.php';

include_once __DIR__ . '/app/yunshop.php';