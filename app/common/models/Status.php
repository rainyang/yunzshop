<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;

class Status extends BaseModel
{
    public $table = 'yz_status';

    protected $guarded = ['id'];

    public function statusFlows()
    {
        return $this->belongsToMany(StatusFlow::class, self::getTable(), 'status_id', 'status_flow_id');
    }
}
