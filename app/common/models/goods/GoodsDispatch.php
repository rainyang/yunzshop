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
class GoodsDispatch extends BaseModel
{
    public $table = 'yz_goods_dispatch';
    const UNIFY_TYPE = 1;
    const TEMPLATE_TYPE = 2;
    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];


    /**
     * 自定义显示错误信息
     * @return array
     */
    public static function getDispatchInfo($goodsId)
    {
        $dispatchInfo = self::where('goods_id', $goodsId)
            ->first();
        return $dispatchInfo;
    }

    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public  function atributeNames()
    {
        return [
            'dispatch_type' => '配送方式',
            'dispatch_price' => '统一配送价格',
            'dispatch_id' => '配送模板',
            'is_cod' => '是否支持货到付款',
        ];
    }


    public  function rules()
    {
        return [
            'dispatch_type' => 'required|integer|min:0|max:1',
            'dispatch_price' => 'numeric|min:0',
            'dispatch_id' => 'integer',
            'is_cod' => 'required|integer|min:0|max:1',
        ];
    }



}