<?php

namespace app\common\models\goods;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\Validator;

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
        $dispatchList = self::uniacid()
            ->get()->toArray();
        return $dispatchList;
    }

    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public static function atributeNames()
    {
        return [
            'uniacid' => '公众号id',
            'dispatch_name' => '配送方式名称',
            'display_order' => '排序',
            'is_default' => '是否默认',
            'enabled' => '是否显示',
            'calculate_type' => '计算方式',
            'first_piece' => '首件个数',
            'another_piece' => '续件个数',
            'first_piece_price' => '首件价格',
            'another_piece_price' => '续件价格',
            'first_weight' => '首重克数',
            'another_weight' => 'c续重克数',
            'first_weight_price' => '首重价格',
            'another_weight_price' => '续重价格',
            'areas' => '配送区域',
            'carriers' => '配送详情',
        ];
    }


    public static function rules()
    {
        return [
            'uniacid' => 'required',
            'dispatch_name' => 'required|max:50',
            'display_order' => '',
            'is_default' => 'digits_between:0,1',
            'enabled' => 'integer',
            'calculate_type' => 'digits_between:0,1',
            'first_piece' => 'numeric',
            'another_piece' => 'numeric',
            'first_piece_price' => 'numeric',
            'another_piece_price' => 'numeric',
            'first_weight' => 'numeric',
            'another_weight' => 'numeric',
            'first_weight_price' => 'numeric',
            'another_weight_price' => 'numeric',
            'areas' => '',
            'carriers' => '',
        ];
    }



}