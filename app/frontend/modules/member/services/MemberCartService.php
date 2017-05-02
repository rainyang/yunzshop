<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/1
 * Time: 下午4:37
 */

namespace app\frontend\modules\member\services;


use app\common\exceptions\AppException;
use app\frontend\modules\member\models\MemberCart;
use Illuminate\Support\Collection;

class MemberCartService
{
    public static function clearCartByIds($ids)
    {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        if (!is_array($ids)) {
            throw new AppException('购物车ID格式不正确');
        }


        return MemberCart::destroyMemberCart($ids);
    }

    public static function newMemberCart($params)
    {

        $cart = new MemberCart($params);
        $cart->validate();
        return $cart;
    }

    /**
     * @param Collection $memberCarts
     * @return Collection
     */
    public static function filterShopMemberCart(Collection $memberCarts)
    {
        return $memberCarts->filter(function ($memberCart) {
            /**
             * @var $memberCart MemberCart
             */
            if (empty($memberCart->goods->is_plugin)) {
                return true;
            }
            return false;
        });
    }

    public static function filterPluginMemberCart(Collection $memberCarts)
    {
        return $memberCarts->filter(function ($memberCart) {
            /**
             * @var $memberCart MemberCart
             */
            if (!empty($memberCart->goods->is_plugin)) {
                return true;
            }
            return false;
        });
    }
}