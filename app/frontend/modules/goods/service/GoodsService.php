<?php
namespace app\frontend\modules\goods\service;
use app\frontend\modules\goods\model\factory\GoodsGroupModelFactory;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/21
 * Time: 下午4:01
 */
class GoodsService
{
    public static function getGoodsGroupModel(){
        return GoodsGroupModelFactory::getGoodsGroupModel();
    }
}