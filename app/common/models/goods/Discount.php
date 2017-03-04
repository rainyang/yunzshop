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

    public static function validationMessages()
    {
        return [

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
            'level_discount_type' => 'confirmed',
            'discount_method' => 'confirmed',
        ], self::validationMessages());

        return $validator;
    }
}