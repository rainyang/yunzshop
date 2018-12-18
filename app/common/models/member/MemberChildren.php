<?php
/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/11/20
 * Time: 22:57
 */

namespace app\common\models\member;


use app\common\models\BaseModel;

class MemberChildren extends BaseModel
{
    public $table = 'yz_member_children';

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }

}