<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/22
 * Time: 17:37
 */

namespace app\backend\modules\charts\models;


use app\common\models\Withdraw;

class Income extends \app\common\models\Income
{

    public function scopeSearch($query,$search)
    {

    }

    public function hasOneWithdraw()
    {
        return $this->hasOne(Withdraw::class, 'member_id', 'member_id');
    }

}