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

$extend = '';
$boot_file = __DIR__ . '/../../framework/bootstrap.inc.php';

if (file_exists($boot_file)) {
    include_once $boot_file;
} else {
    $extend = '/../..';
}

include_once __DIR__ . $extend . '/app/laravel.php';

include_once __DIR__ . $extend . '/app/yunshop.php';