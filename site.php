<?php
/**
 * 芸众商城模块微站定义
 *
 * @url http://bbs.yunzshop.com/
 */

defined('IN_IA') or exit('Access Denied');

include_once __DIR__ . '/app/laravel.php';

if (env('APP_Framework') == 'platform') {
    include_once __DIR__ . '/app/yz_yunshop.php';
} else {
    include_once __DIR__ . '/app/yunshop.php';
}

exit;
