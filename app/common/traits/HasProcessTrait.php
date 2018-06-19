<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 14/06/2018
 * Time: 11:44
 */

namespace app\common\traits;

use app\common\models\Flow;
use app\common\models\Process;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait HasFlowTrait
 * @package app\common\traits
 * @property Collection process
 * @property Collection flows
 * @property Process flow
 */
trait HasProcessTrait
{
    /**
     * 所有的流程类型
     * @return mixed
     */
    public function flows(){
        return $this->morphToMany(
            Flow::class,
            'model',
            (new Process())->getTable(),
            'model_id',
            'flow_id'
        );
    }
    /**
     * @param Flow $flow
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addProcess(Flow $flow)
    {
        return $this->flows()->save($flow);
    }

    /**
     * @return HasMany
     */
    public function process()
    {
        return $this->hasMany(Process::class,'model_id')->where('model_type',self::class);
    }

    /**
     * 当前的流程
     * @return mixed
     */
    public function currentProcess()
    {
        return $this->process->where('state', 'processing')->first();
    }
}