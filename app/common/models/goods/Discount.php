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

    public static function getGoodsDiscountInfo($goodsId)
    {
        $goodsDiscountInfo = self::where('goods_id', $goodsId)
            ->first();
        return $goodsDiscountInfo;
    }

    public static function validationMessages()
    {
        return [
            'required' => ' :attribute不能为空!',



        ];
    }

    /**
     * 校验表单数据
     *
     * @param $data
     * @return \Illuminate\Validation\Validator
     */
    public static function validator($data)
    {
        $validator = Validator::make($data, [
            'goods_id' => 'required',
            'level_discount_type' => 'integer',
            'discount_method' => 'integer',
        ], self::validationMessages());

        return $validator;
    }
}