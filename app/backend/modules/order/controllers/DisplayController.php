<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 21:25
 */

namespace app\backend\modules\order\controllers;


use app\common\components\BaseController;

class DisplayController extends BaseController
{
    public function index()
    {
        $list = [];
        //或者模板路径可写全  $this->render('order/display/index',['list'=>$list]);
        //以下为简写
        $this->render('index', [
            'list' => $list
        ]);
    }
}