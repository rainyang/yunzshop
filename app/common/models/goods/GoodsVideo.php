<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12
 * Time: 9:21
 */

namespace app\common\models\goods;

use app\common\models\BaseModel;

class GoodsVideo extends BaseModel
{
    public $table = 'yz_goods_video';

    public function scopeOfGoodsId($query, $goodsId)
    {
        return $query->where('goods_id', $goodsId);
    }

    /**
     * 初始化方法
     */
    public static function boot()
    {
        parent::boot();
        // 添加了公众号id的全局条件.
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}