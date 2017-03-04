<?php
namespace app\backend\modules\goods\services;

/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/27
 * Time: 上午9:18
 */
class DiscountService
{
    /**
     * 数组转换成字符串
     * @param array $array
     * @return string
     */
    public function resetArray($array = array())
    {
        if (empty($array)) {
            return;
        }
        /*$array = [
            'goods_id' => 1,
            'level_discount_type' => 1,
            'discount_method' => 1,
            'discount_value' => [
                '1' => 50,
                '2' => 30,
            ],
        ];*/
        $discount = [];
        foreach ($array['discount_value'] as $key => $value) {
            $discount = [
                'level_discount_type' => $array['level_discount_type'],
                'discount_method' =>  $array['discount_method'],
                'level_id' => $key,
                'discount_value' => $value
            ];
        }
        return $discount;

    }
}