<?php

namespace app\backend\modules\api\modules\v1\controllers;


use app\common\models\Member;
use app\common\helpers\View;
use app\common\components\BaseController;


/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 11:50
 */
class TestController extends BaseController
{
    public function index()
    {
        echo __CLASS__;
        echo "<br/>";
        echo "<br/>";
        echo "<br/>";
        $arr = ['a'];
        $arr2 = array_add($arr,'1','b');
        print_r($arr2);
        echo "<br/>";
        echo "<br/>";
        $member = Member::first();

        print_r($member);

        $id = \YunShop::request()->id;


    }

    public function view()
    {
        $this->render('shop/index', ['a' => '']);
    }


}