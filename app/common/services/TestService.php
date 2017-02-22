<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 22/02/2017
 * Time: 01:39
 */

namespace app\common\services;


use app\common\TestContract;

class TestService implements TestContract
{
    public function callMe($controller)
    {
        dd('Call Me From TestServiceProvider In '.$controller);
    }
}