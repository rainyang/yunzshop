<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/11
 * Time: 上午10:52
 */

namespace app\frontend\modules\order\controllers;

use app\common\exceptions\AppException;
use \app\frontend\models\MemberCart;
use app\frontend\modules\memberCart\MemberCartCollection;
use Illuminate\Support\Collection;

class CartBuyController extends PreOrderController
{
    public function index()
    {
        $this->validateParam();

        parent::index();
    }
    protected function validateParam(){
        $this->validate([
            'cart_ids' => 'required',
        ]);
    }

    /**
     * 从url中获取购物车记录并验证
     * @return Collection
     * @throws AppException
     */
    protected function getMemberCarts()
    {
        static $memberCarts;
        $cartIds = [];
        if (!is_array($_GET['cart_ids'])) {
            $cartIds = explode(',', $_GET['cart_ids']);
        }

        if (!count($cartIds)) {
            throw new AppException('参数格式有误');
        }
        if(!isset($memberCarts)){
            $memberCarts = app('OrderManager')->make('MemberCart')->whereIn('id', $cartIds)->get();
            $memberCarts = new MemberCartCollection($memberCarts);
            $memberCarts->loadRelations();
        }

        $memberCarts->validate();
        if ($memberCarts->isEmpty()) {
            throw new AppException('未找到购物车信息');
        }

        if ($memberCarts->isEmpty()) {

            throw new AppException('请选择下单商品');
        }
        return $memberCarts;
    }
}