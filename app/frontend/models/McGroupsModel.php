<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/3/7
 * Time: 下午2:14
 */

namespace app\frontend\models;

use app\backend\models\BackendModel;

class McGroupsModel extends BackendModel
{
    public $table = 'mc_groups';

    public static function getDefaultGroupId()
    {
        return self::select('groupid')
            ->uniacid()
            ->where('isdefault', 1)
            ->first();
    }
}