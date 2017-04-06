<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 下午5:52
 */

namespace app\frontend\modules\shop\services;


use app\common\exceptions\AppException;
use app\frontend\modules\shop\services\models\ShopModel;

class ShopService
{
    private static $_current_shop;
    //todo 待实现
    public static function getCurrentShopModel(){
        $result = new ShopModel();
        if(!isset($result)){
            throw new AppException('读取商城信息出错');
        }
        return $result;
    }
}