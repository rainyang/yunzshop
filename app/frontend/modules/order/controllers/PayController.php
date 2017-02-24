<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/24
 * Time: 下午3:42
 */

namespace app\frontend\modules\order\controllers;

use app\common\helpers\Url;
class PayController
{
    private $set;
    private $open_id;
    private $order_id;

    public function __construct()
    {
        //$this->set      = m('common')->getSysset('shop');
        //$this->open_id  = m('user')->getOpenid();
        $this->order_id = intval(\YunShop::request()->order_id);
    }

    private function getOpenId()
    {
        if (!$this->open_id) {
            $this->open_id = \YunShop::request()->open_id;
        }
    }

    private function getOrder()
    {

    }

    public function display()
    {
        echo '<pre>';print_r($this->order_id);exit;
    }
}