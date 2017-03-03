<?php
namespace  app\backend\controllers;

use app\common\components\BaseController;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 16:46
 */
class TestController extends BaseController
{
    public function index()
    {
       $this->render('index');
    }

    public function form()
    {
        $this->render('form');
    }

}