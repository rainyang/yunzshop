<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/15
 * Time: 下午4:29
 */

namespace app\frontend\modules\discount\services\models;

use app\frontend\modules\discount\services\models\Discount;

class OrderDiscount extends Discount
{

    // todo 获取商品可选的优惠
    public function getDiscountTypes()
    {
        $data[] = [
            'id' => 1,
            'name' => '积分抵扣',
            'max_value' => 20,
            'max_price' => 20,
            'plugin' => 0
        ];
        return $data;
    }

}