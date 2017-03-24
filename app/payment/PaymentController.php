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

       // file_put_contents('../../../../addons/sz_yi/data/p2.log', print_r($_SERVER['SCRIPT_NAME'],1));

        $script_name = $_SERVER['SCRIPT_NAME'];
echo $script_name;exit;

        $body = !empty($_REQUEST['body']) ? $_REQUEST['body'] : '';
        $splits = explode(':', $body);

        if (!empty($splits[1])) {
            \YunShop::app()->uniacid = intval($splits[1]);
        } else {
            \YunShop::app()->uniacid = 0;
        }
    }
}