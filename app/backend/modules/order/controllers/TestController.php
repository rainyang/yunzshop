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
        //$_GPC
        print_r(\YunShop::request()->route);
        echo "</pre>";
        echo "<pre>";
        //$_W;

        dfasdfa
        print_r(\YunShop::app()->config['db']);
        echo "</pre>";
    }
}