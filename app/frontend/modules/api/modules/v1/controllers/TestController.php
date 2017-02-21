<?php

namespace app\backend\modules\api\modules\v1\controllers;

use app\common\models\Member;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 11:50
 */
class TestController
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
        $member->nickname = "janpan";
        $member->save();
        print_r($members);

        $id = \YunShop::request()->id;


    }

    public function view()
    {
    }
}