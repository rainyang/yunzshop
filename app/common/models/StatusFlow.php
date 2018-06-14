<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;

class StatusFlow extends BaseModel
{
    public $table = 'yz_status_flow';

    protected $guarded = ['id'];

    public function status()
    {
        return $this->belongsToMany(Status::class, self::getTable(), 'status_flow_id', 'status_id');
    }
}
