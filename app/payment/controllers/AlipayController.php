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
        echo \YunShop::app()->uniacid;
                echo "index ";
    }
}