<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/11
 * Time: 上午10:52
 */

namespace app\frontend\modules\order\controllers;

use app\common\exceptions\AppException;
use app\frontend\modules\member\models\MemberCart;
use Illuminate\Support\Collection;

class CartBuyController extends PreGeneratedController
{
    public function index()
    {
        if (!isset($_GET['cart_ids'])) {
            return $this->errorJson('请选择要结算的商品');
        }

        parent::index();
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
            $memberCarts = MemberCart::getCartsByIds($cartIds);
        }
        if ($memberCarts->isEmpty()) {
            throw new AppException('未找到购物车信息');
        }

        if ($memberCarts->isEmpty()) {

            throw new AppException('请选择下单商品');
        }
        return $memberCarts;
    }
}