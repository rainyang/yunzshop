<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;

use app\common\models\BaseModel;
use app\common\models\FlowState;

/**
 * 流程类型
 * Class Flow
 * @package app\common\models\statusFlow
 */
class Flow extends BaseModel
{

    public $table = 'yz_flow';

    protected $guarded = ['id'];

    /**
     * 包含的状态类型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function states()
    {
        return $this->belongsToMany(State::class, (new FlowState)->getTable(), 'flow_id', 'state_id');
    }
}
