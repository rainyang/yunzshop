<?php
/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/28
 * Time: 下午7:16
 */

namespace app\common\models\goods;


use app\common\models\BaseModel;

class Discount extends BaseModel
{
    public $table = 'yz_goods_discount';

    public static function getGoodsDiscountList($goodsId)
    {
        $goodsDiscountInfo = self::where('goods_id', $goodsId)
            ->get();
        return $goodsDiscountInfo;
    }


    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public static function atributeNames()
    {
        return [
            'level_discount_type' => '等级方式',
            'discount_method' => '折扣方式',
            'level_id' => '会员等级id',
            'discount_value' => '折扣或金额数值'
        ];
    }


    public static function rules()
    {
        return [
            'level_discount_type' => '',
            'discount_method' => '',
            'level_id' => '',
            'discount_value' => ''
        ];
    }
}