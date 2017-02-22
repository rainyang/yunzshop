<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 22/02/2017
 * Time: 14:39
 */

namespace app\common\models;


class TestMemberValidator
{
    public static function rule()
    {
        return [
            'username'=>'required|max:255',
            'email'=>'required|email|max:25',
            'password'=>'required|min:6|confirmed',
        ];
    }
}