<?php

namespace app\common\models\goods;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\lValidator;

/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/22
 * Time: 下午5:54
 */
class Dispatch extends BaseModel
{
    public $table = 'yz_dispatch';


    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];


    /**
     * 自定义显示错误信息
     * @return array
     */
    public static function getDispatchList()
    {
        $dispatchList = self::get();
        return $dispatchList;
    }

    /**
     * @param int $goodsId
     * @return array $goodsShareInfo
     */
    public static function validationMessages()
    {
        return [
            'required' => ' :attribute不能为空!',
            'max' => ' :attribute不能大于:max!',
            'integer' => ':attribute请填写正确格式'
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
            'uniacid' => 'required',
            'dispatch_name' => 'accepted|max:50',
            'display_order' => 'accepted',
            'is_default' => 'integer',
            'enabled' => 'integer',
            'calculate_type' => 'integer',
            'first_piece' => 'integer',
            'another_piece' => 'integer',
            'first_piece_price' => 'integer',
            'another_piece_price' => 'integer',
            'first_weight' => 'integer',
            'another_weight' => 'integer',
            'first_weight_price' => 'integer',
            'another_weight_price' => 'integer',
            'areas' => 'accepted',
            'carriers' => 'accepted',
        ], self::validationMessages());

        return $validator;
    }


}