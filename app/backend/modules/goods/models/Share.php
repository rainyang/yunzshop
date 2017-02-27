<?php
/**
 * 商品分享权限关联表数据操作
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/24
 * Time: 下午2:31
 */

namespace app\backend\modules\goods\models;


class Share extends \app\common\models\goods\Share
{
    public $timestamps = false;
    /**
     * 获取商品分享权限数据
     * @param int $goodsId
     * @return array
     */
    public static function getInfo($goodsId)
    {
        return self::getGoodsShareInfo($goodsId);
    }

    /**
     * 商品分享权限数据添加
     * @param array $shareInfo
     * @return bool
     */
    public static function createdShare($shareInfo)
    {
        return self::insert($shareInfo);
    }

    /**
     * 商品分享权限数据更新
     * @param array $shareInfo
     * @return mixed
     */
    public static function updatedShare($goodsId, $shareInfo)
    {
        return self::where('goods_id',  $goodsId)->update($shareInfo);
    }

    /**
     * 商品分享权限数据删除
     * @param int $goodsId
     * @return mixed
     */
    public static function deletedShare($goodsId)
    {
        return self::where('goods_id', $goodsId)->delete();
    }

}