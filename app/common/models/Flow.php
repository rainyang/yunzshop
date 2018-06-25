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
use Illuminate\Database\Eloquent\Collection;

/**
 * 流程类型
 * Class Flow
 * @package app\common\models\statusFlow
 * @property Collection flowStates
 * @property Collection process
 * @property int id
 * @property string name
 * @property string code
 * @property Collection states
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

    /**
     * 流程状态关联记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function flowStates()
    {
        return $this->hasMany(FlowState::class, 'flow_id');
    }

    /**
     * 此状态的下一个状态
     * @param State $state
     * @return mixed
     */
    public function getNextState(State $state)
    {
        $flowState = $this->flowStates->where('state_id',$state->id)->first();
        return $this->flowStates->where('order', '>', $flowState->order)->first()->state;
    }

    public function getCloseState()
    {
        return $this->flowStates->where('order', FlowState::ORDER_CLOSE)->first()->state;

    }

    public function getCancelState()
    {
        return $this->flowStates->where('order', FlowState::ORDER_CANCEL)->first()->state;

    }

    /**
     * 获取最终状态
     * @return mixed
     */
    public function getFinalState()
    {
        return $this->flowStates->sortByDesc('order')->first()->state;
    }

    /**
     * 添加一组状态
     * @param $states
     */

    public function pushStates($states)
    {
        $result = [];
        foreach ($states as $state) {
            $result[State::create($state)->id] = [
                'order' => $state['order']
            ];
        }

        $this->states()->attach($result);
    }

    public function setStates($states)
    {
        $this->states()->attach($states);

    }

    public function process()
    {
        return $this->hasMany(Process::class, 'flow_id');
    }
}
