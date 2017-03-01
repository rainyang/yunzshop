<?php
/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/28
 * Time: 下午7:16
 */

namespace app\common\models\goods;


use app\common\models\BaseModel;

class DiscountDetail extends BaseModel
{
    public $table = 'yz_goods_discount_detail';

    public static function getDiscountDetailList($discountId)
    {
        $discountDetailList = self::where('discount_id', $discountId)
            ->get();
        return $discountDetailList;
    }

    public static function validationMessages()
    {
        return [
            'required' => ' :attribute不能为空!',
            'integer' => ' :attribute必须填写数字!',



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
            'discount_id' => 'required|integer',
            'level_id' => 'integer',
            'discount' => 'confirmed',
            'amount' => 'integer',
        ], self::validationMessages());

        return $validator;
    }
}