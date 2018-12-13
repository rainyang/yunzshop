<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/12
 * Time: 9:10
 */

namespace app\payment;

use app\common\components\BaseController;

class YopController extends BaseController
{
    protected $set;

    protected  $parameters;

    public function __construct()
    {
        parent::__construct();
        if (!app('plugins')->isEnabled('yop-pay')) {
            echo 'Not turned on yop pay';
            exit();
        }
        $this->init();
    }

    private function init()
    {
        $this->set = \Setting::get('plugin.yop_pay');

        if ($_REQUEST['response']) {
            $response = \Yunshop\YopPay\common\Util\YopSignUtils::decrypt($_REQUEST['response'], $this->set['private_key'], $this->set['public_key']);
            $this->parameters = json_decode($response, true);
        };

    }
}