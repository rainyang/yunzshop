<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 09/03/2017
 * Time: 11:00
 */

namespace app\backend\controllers;


use app\backend\models\Menu;
use app\common\components\BaseController;
use app\common\facades\Option;

class MenuController extends BaseController
{
    public function index()
    {

        return $this->render('index',[]);
    }

    public function add()
    {
        $model = new Menu();


        return $this->render('form',compact('model'));
    }

    public function edit()
    {
        return $this->render('form',[]);
    }

    public function delete()
    {

    }
}