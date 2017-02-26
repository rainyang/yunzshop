<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 下午5:52
 */

namespace app\frontend\modules\shop\service;


class ShopService
{
    private static $_current_shop;

    public static function getCurrentShopModel(){

        return self::$_current_shop;
    }
}