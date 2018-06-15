<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;

use app\common\models\BaseModel;

/**
 * 状态
 * Class State
 * @package app\common\models\statusFlow
 */
class State extends BaseModel
{
    public $table = 'yz_state';

    protected $guarded = ['id'];

    /**
     * 包含此状态的流程
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function flows()
    {
        return $this->belongsToMany(Flow::class, self::getTable(), 'state_id', 'flow_id');
    }
}
