<?php

define('IN_IA', true);

include_once __DIR__ . '/app/laravel.php';

if (env('APP_Framework') == 'platform') {
    include_once __DIR__ . '/app/yz_yunshop.php';
} else {
    include_once __DIR__ . '/app/yunshop.php';
}