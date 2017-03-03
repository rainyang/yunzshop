<?php
/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/28
 * Time: 上午10:54
 */

namespace app\common\models\goods;


use app\common\models\BaseModel;

class Privilege extends BaseModel
{
    public $table = 'yz_goods_privilege';


    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];


    /**
     * 获取商品权限信息
     * @param int  $goodsId
     * @return array  $goodsPrivilegeInfo
     */
    public static function getGoodsPrivilegeInfo($goodsId)
    {
        $goodsPrivilegeInfo = self::where('goods_id', $goodsId)
            ->first();
        return $goodsPrivilegeInfo;
    }

    public static function validationMessages()
    {
        return [
            'required' => ' :attribute不能为空!',
            'min' => ' :attribute不能少于:min!',
            'max' => ' :attribute不能少于:max!',
            'image' => ':attribute必须是图片格式',
            'numeric' => ':attribute必须填写数字',
            'timezone' => ':attribute必须填写时间格式'


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
            'show_levels' => 'confirmed',
            'show_groups' => 'confirmed',
            'buy_levels' => 'confirmed',
            'buy_groups' => 'confirmed',
            'once_buy_limit' => 'numeric',
            'total_buy_limit' => 'numeric',
            'time_begin_limit' => 'timezone',
            'time_end_limit' => 'timezone',
        ], self::validationMessages());

        return $validator;
    }
}