<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/1
 * Time: 下午4:49
 */

namespace app\frontend\modules\member\listeners;


use app\frontend\modules\member\services\MemberCartService;

class Order
{
    public function handle($event){
        $cart_ids =\Request::input('cart_ids');
        @$cart_ids = json_decode($cart_ids);

        MemberCartService::clearCartByIds($cart_ids);
    }
}