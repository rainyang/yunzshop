<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/21
 * Time: 9:19
 */

namespace app\backend\modules\from\services;


use app\backend\modules\from\models\CategoryDiscount;
use app\common\models\Goods;

class DiscountService
{
    protected $goods_id = [];

    public function index()
    {

        $discount_data = CategoryDiscount::get()->toArray();
//        dd($discount_data);
        // foreach ($discount_data as $k => $value) {
        //     Goods::select('id')->whereHas('')
        // }
    }
}