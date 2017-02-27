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
    
    public static function getCategoryTotal($uniacid,  $parent_id)
    {
        return self::where('uniacid', $uniacid)
            ->where('parent_id', $parent_id)
            ->count();
    }

}