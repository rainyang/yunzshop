<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 22/02/2017
 * Time: 13:52
 */

namespace app\backend\modules\member\controllers;


use app\common\components\BaseController;

class TestMemberController extends  BaseController
{
    public function testLogin()
    {
        $this->render('test',['a'=>'123456']);
    }
}