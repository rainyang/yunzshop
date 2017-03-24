<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 24/03/2017
 * Time: 01:07
 */

namespace app\payment\controllers;


use app\payment\PaymentController;

class AlipayController extends PaymentController
{
    public function notifyUrl()
    {
        file_put_contents('../../../../addons/sz_yi/data/p3.log', print_r($_POST,1));
        echo \YunShop::app()->uniacid;
                echo "index ";
    }

    public function returnUrl()
    {
        echo 'success';
    }
}