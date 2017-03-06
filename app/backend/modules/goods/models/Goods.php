<?php
/**
 * Created by PhpStorm.
 * User: RainYang
 * Date: 2017/2/22
 * Time: 下午18:16
 */
namespace app\backend\modules\goods\models;


use app\backend\modules\goods\observers\GoodsObserver;

class Goods extends \app\common\models\Goods
{

    public $widgets = [];
}