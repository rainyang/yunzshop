<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/27
 * Time: 下午6:35
 */

namespace app\frontend\modules\member\models;



class MemberFavorite extends \app\common\models\MemberFavorite
{
    /*
     * 通过主键ID查找
     *
     * @params int $favoriteId
     *
     * @return object*/
    public static function getFavoriteById($favoriteId)
    {
        return static::uniacid()->where('id', $favoriteId)->first();
    }

    /*
     * 通过商品ID、会员ID查找
     *
     * @params int $goodsId
     * @params int $memberId
     *
     * @return object*/
    public static function getFavoriteByGoodsId($goodsId, $memberId)
    {
        return static::uniacid()->where('goods_id', $goodsId)->where('member_id', $memberId)->first();
    }

    public static function getFavoriteList($memberId)
    {
        return static::select('id', 'goods_id')->uniacid()->where('member_id', $memberId)->get()->toArray();
    }
    /**
     * remove collection
     *
     * @param array $data
     *
     * @return 1 or 0
     * */
    public static function destroyFavorite($favoriteId)
    {
        return static::uniacid()->where('id', $favoriteId)->delete();
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'goods_id'  => '商品ID不能为空',
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'goods_id'  => 'required|integer',
        ];
    }
}
