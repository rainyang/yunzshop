<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/27
 * Time: 上午10:14
 */

namespace app\frontend\modules\goods\services;

use app\frontend\models\Goods;
use Illuminate\Container\Container;

class GoodsManager extends Container
{
    public function __construct()
    {
        $this->bind('Goods', function ($orderManager, $attributes = []) {
            if(\YunShop::isApi()){
                //前台
                return new Goods($attributes);

            }else{
                //后台
                return new \app\backend\modules\goods\models\Goods($attributes);
            }
        });
    }
}