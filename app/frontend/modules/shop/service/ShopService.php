<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 下午5:52
 */

namespace app\frontend\modules\shop\service;


use app\frontend\modules\shop\model\ShopModel;

class ShopService
{
    private static $_current_shop;
    //todo 待实现
    public static function getCurrentShopModel(){

        return new ShopModel();
    }
}