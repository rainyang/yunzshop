<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\dispatch\services\models;

class GoodsDispatch extends Dispatch
{


    /**
     * 获取商品配送方式
     * @return int
     */
    //  todo 从商品中获取
    public function getDispatchType()
    {
        return 1;
    }


}