<?php

namespace app\common\models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 该表用于记录禁止编辑商品的信息
 * 当要禁止某商品编辑时，就往该变=表插入一条数据，当可以编辑后，请删除该记录
 * 例如，拼团活动使用了商品id:635,这时候需要禁止商品635编辑功能，否则可能出现下单出错(规格问题)
 *  于是拼团活动往该表插入一条数据，活动结束后将该数据删除
 * 当拼团活动在进行中而编辑该商品时，会查询该表是否有该商品记录，有则不允许编辑
 * Class GoodsEditDisable
 * @package app\common\models
 */
class GoodsEditDisable extends \app\common\models\BaseModel
{
    public $table = 'yz_goods_edit_disable';

    use SoftDeletes;
    public $timestamps = true;
    public $attributes = [];

    public function rules()
    {
        return [
            'uniacid' => 'required|integer',
            'goods_id' => 'required|integer',
            'message' => 'required',
        ];
    }

    public function atributeNames()
    {
        return [
            'uniacid' => '公众号ID',
            'goods_id' => '商品id',
            'message' => '提示信息',
        ];
    }

}