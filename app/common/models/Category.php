<?php

namespace app\common\models;

use app\common\models\BaseModel;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午5:54
 */
class Category extends BaseModel
{
    public $table = 'yz_category';

    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];

    public static function getCategoryList($uniacid, $parent_id)
    {
        return self::where('uniacid', $uniacid)
            ->where('parent_id', $parent_id)
            ->orderBy('id', 'asc');
    }
    /**
     * @param $uniacid
     * @param $pindex
     * @param $psize
     * @param $parent_id
     * @return mixed
     */
    public static function getCategorys($uniacid, $pindex, $psize, $parent_id)
    {
        $data = self::where('uniacid', $uniacid)
            ->where('parent_id', $parent_id)
            ->orderBy('id', 'asc')
            ->skip(($pindex - 1) * $psize)
            ->take($psize)
            ->get()
            ->toArray();
        return $data;
    }

    /**
     * @param $uniacid
     * @param $parent_id
     * @return mixed
     */
    public static function getCategoryTotal($uniacid,  $parent_id)
    {
        return self::where('uniacid', $uniacid)
            ->where('parent_id', $parent_id)
            ->count();
    }

}