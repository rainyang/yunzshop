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
class Share extends BaseModel
{
    public $table = 'yz_goods_share';


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
    public static function getGoodsShareInfo($goodsId)
    {
        $goodsShareInfo = self::where('goods_id', $goodsId)
            ->first();
        return $goodsShareInfo;
    }

    /**
     * @param int $goodsId
     * @return array $goodsShareInfo
     */
    public static function validationMessages()
    {
        return [
            'required' => ' :attribute不能为空!',
            'min' => ' :attribute不能少于:min!',
            'max' => ' :attribute不能少于:max!',
            'image' => ':attribute必须是图片格式',


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
            'need_follow' => 'integer',
            'no_follow_message' => 'accepted|max:255',
            'follow_message' => 'accepted|max:255',
            'share_title' => 'accepted|max:50',
            'share_thumb' => 'image',
            'share_desc' => 'accepted',
        ], self::validationMessages());

        return $validator;
    }


}