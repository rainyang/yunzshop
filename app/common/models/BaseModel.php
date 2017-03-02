<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 24/02/2017
 * Time: 16:36
 */

namespace app\common\models;


use app\common\traits\ValidatorTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use ValidatorTrait;

    public static function getUniacid()
    {
        return \YunShop::app()->uniacid;
    }

    //后台全局筛选统一账号scope
    public function scopeUniacid($query)
    {
        return $query->where('uniacid', \YunShop::app()->uniacid);
    }

}