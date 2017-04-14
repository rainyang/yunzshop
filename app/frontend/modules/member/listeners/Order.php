<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/1
 * Time: 下午4:49
 */

namespace app\frontend\modules\member\listeners;


use app\frontend\modules\member\services\MemberCartService;

class Order
{
    public function handle($event){
        $cart_ids = \YunShop::request()->get('cart_ids');
        @$cart_ids = json_decode($cart_ids);
        MemberCartService::clearCartByIds($cart_ids);
    }
}