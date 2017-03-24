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

        $body = $_REQUEST['body'];
        $strs = explode(':', $body);

        \YunShop::app()->uniacid = intval($strs[1]);
    }
}