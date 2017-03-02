<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 02/03/2017
 * Time: 15:41
 */

namespace app\backend\models;


use app\common\models\BaseModel;

class BackendModel extends BaseModel
{

    //后台全局筛选统一账号scope
    public function scopeUniacid($query)
    {
        return $query->where('uniacid', \YunShop::app()->uniacid);
    }
}