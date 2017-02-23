<?php

namespace app\common\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午5:54
 */
class Category extends Model
{
    public $table = 'yz_category';

    public static function getCategorys($uniacid)
    {
        $data = self::where('uniacid', $uniacid)
            ->orderBy('display_order', 'desc')
            ->get();
        return $data;
    }

}