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

        $script_info = pathinfo($_SERVER['SCRIPT_NAME']);

        if (!empty($script_info)) {
            switch ($script_info['filename']) {
                case 'notifyUrl':
                case 'retrunUrl':
                    $body = !empty($_REQUEST['body']) ? $_REQUEST['body'] : '';
                    $splits = explode(':', $body);
                    break;
                case 'refundNotifyUrl':
                    $detail_data = !empty($_REQUEST['detail_data']) ? $_REQUEST['detail_data'] : '';
                    $data = explode('^', $detail_data);

                    if (!empty($data[2])) {
                        $splits = explode('-', $data[2]);
                    }
                    break;
                case 'withdrawNotifyUrl':
                    $detail_data = !empty($_REQUEST['detail_data']) ? $_REQUEST['detail_data'] : '';
                    $data = explode('^', $detail_data);

                    if (!empty($data[4])) {
                        $splits = explode('-', $data[4]);
                    }
                    break;
                default:
                    break;
            }
            file_put_contents('../../../../addons/sz_yi/data/p2.log', print_r($splits,1));
            if (!empty($splits[1])) {
                \YunShop::app()->uniacid = intval($splits[1]);
            } else {
                \YunShop::app()->uniacid = 0;
            }

        }
    }
}