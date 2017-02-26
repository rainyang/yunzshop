<?php
namespace  app\backend\controllers;

use app\common\components\BaseController;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 16:46
 */
class TestCacheController extends BaseController
{
    public function index()
    {
        echo __CLASS__;
    }

    public function testIndex()
    {
        echo __CLASS__;
        echo $this->action;
    }

}