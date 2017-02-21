<?php
namespace app\backend\modules\order\controllers;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController
{
    public function index()
    {
            echo __CLASS__;
        echo "<pre>";
        print_r(\YunShop::request());
        echo "</pre>";
        echo "<pre>";
        print_r(\YunShop::config()->db);
        echo "</pre>";
    }
}