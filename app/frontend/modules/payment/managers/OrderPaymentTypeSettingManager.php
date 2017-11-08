<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/8
 * Time: 下午2:01
 */

namespace app\frontend\modules\payment\managers;

use app\frontend\modules\payment\paymentSettings\OrderPaymentSettingCollection;
use Illuminate\Container\Container;

class OrderPaymentTypeSettingManager extends Container
{
    public function getOrderPaymentSettingCollection($code,$order){

        $settings = $this->make($code,$order);

        $settings = collect($settings)->map(function($setting) use ($order){
            return call_user_func($setting,$order);
        });
        return new OrderPaymentSettingCollection($settings);
    }
}