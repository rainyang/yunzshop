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
        $pay_type = config('app.pay_type');

        $script_info = pathinfo($_SERVER['SCRIPT_NAME']);

        if (!empty($script_info)) {
            switch ($script_info['filename']) {
                case 'notifyUrl':
                    \YunShop::app()->uniacid = $this->getUniacid();
                    break;
                case 'refundNotifyUrl':
                case 'withdrawNotifyUrl':
                    $batch_no = !empty($_REQUEST['batch_no']) ? $_REQUEST['batch_no'] : '';

                    \YunShop::app()->uniacid = substr($batch_no, 17, 5);

                    break;
                default:
                    \YunShop::app()->uniacid = $this->getUniacid();

                    break;
            }
        }

       \Setting::$uniqueAccountId = \YunShop::app()->uniacid;

    }

    private function getUniacid()
    {
        $body = !empty($_REQUEST['body']) ? $_REQUEST['body'] : '';
        $splits = explode(':', $body);

        if (!empty($splits[1])) {
            return intval($splits[1]);
        } else {
            return 0;
        }
    }
}