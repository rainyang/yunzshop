<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/11
 * Time: 上午10:52
 */

namespace app\frontend\modules\order\controllers;


use app\frontend\modules\order\services\OrderService;

class CartBuyController extends PreGeneratedController
{
    public function index(){
        if (!isset($_GET['cart_ids'])) {
            return $this->errorJson('请选择要结算的商品');
        }

        parent::index();
    }
    protected function getMemberCarts(){
        return OrderService::getShopMemberCarts();
    }
}