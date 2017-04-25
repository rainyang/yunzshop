<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 19/03/2017
 * Time: 00:48
 */

namespace app\backend\controllers;

use app\common\components\BaseController;

class IndexController extends BaseController
{
    public function index()
    {

        return view('index',[])->render();
    }
}