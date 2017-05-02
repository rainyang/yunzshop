<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/1
 * Time: 下午4:49
 */

namespace app\frontend\modules\member\listeners;


use app\common\requests\Request;
use app\frontend\modules\member\services\MemberCartService;

class Order
{
    public function handle($event){
        return ;
        dd(Request::input('cart_ids'));
        exit;
        MemberCartService::clearCartByIds($cart_ids);
    }
}