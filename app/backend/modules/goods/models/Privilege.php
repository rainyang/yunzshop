<?php
/**
 * 商品权限关联表数据操作
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/28
 * Time: 上午11:01
 */

namespace app\backend\modules\goods\models;


class Privilege extends \app\common\models\goods\Privilege
{
    public $timestamps = false;

    /**
     * 获取商品权限数据
     * @param int $goodsId
     * @return array
     */
    public static function getInfo($goodsId)
    {
        return self::getGoodsPrivilegeInfo($goodsId);
    }

    /**
     * 商品分享权限数据添加
     * @param array $privilegeInfo
     * @return bool
     */
    public static function createdPrivilege($privilegeInfo)
    {
        return self::insert($privilegeInfo);
    }

    /**
     * 商品分享权限数据更新
     * @param array $privilegeInfo
     * @return mixed
     */
    public static function updatedPrivilege($goodsId, $privilegeInfo)
    {
        return self::where('goods_id', $goodsId)->update($privilegeInfo);
    }

    /**
     * 商品分享权限数据删除
     * @param int $goodsId
     * @return mixed
     */
    public static function deletedPrivilege($goodsId)
    {
        return self::where('goods_id', $goodsId)->delete();
    }
}