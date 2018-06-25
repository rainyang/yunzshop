<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;


/**
 * 流程类型与状态类型的关系表
 * Class FlowTypeHasStatusType
 * @package app\common\models\statusFlow
 */
class FlowState extends BaseModel
{
    public $table = 'yz_flow_state';

    protected $guarded = ['id'];
    const ORDER_CLOSE = -2;
    const ORDER_CANCEL = -1;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

}
