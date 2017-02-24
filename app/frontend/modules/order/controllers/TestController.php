<?php
namespace app\frontend\modules\order\controllers;

use app\common\helpers\Url;
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

        print_r(\YunShop::app()->config['db']);
        echo "</pre>";

        echo '<a href="'. Url::web('api.v1.test.index',['id'=>1]) .'" >api</a>';

    }
}