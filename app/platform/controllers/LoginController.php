<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/15
 * Time: 下午6:56
 */

namespace app\platform\controllers;


use app\common\components\BaseController;

class LoginController extends BaseController
{

    public function index()
    {
        echo 'index';
    }

    public function login()
    {
        echo 'login';
    }

    public function showLoginForm()
    {
        echo 'showLoginForm';exit;
    }
}