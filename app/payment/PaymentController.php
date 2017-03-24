<?php

namespace  app\payment;

use app\common\components\BaseController;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 24/03/2017
 * Time: 09:06
 */
class PaymentController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        file_put_contents('../../../../addons/sz_yi/data/p1.log', print_r($_POST,1));
        file_put_contents('../../../../addons/sz_yi/data/p2.log', print_r(\YunShop::request(),1));
        $body = $_POST['body'];
        $strs = explode(':', $body);

        \YunShop::app()->uniacid = intval($strs[0]);
    }
}