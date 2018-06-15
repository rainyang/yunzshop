<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 14/06/2018
 * Time: 11:44
 */

namespace app\common\traits;

use app\common\models\flow\Flow;
use app\common\models\flow\Process;
use Illuminate\Database\Eloquent\Collection;

/**
 * Trait HasFlowTrait
 * @package app\common\traits
 * @property Collection process
 * @property Collection flows
 * @property Process flow
 */
trait HasFlowTrait
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
            'status_flow_id'
        );
    }
    /**
     * 所有的流程
     * @return Collection
     */
    public function process()
    {
        return $this->hasMany(Process::class, 'model_id', 'id')->where('model_type', self::class);
    }

    /**
     * 当前的流程
     * @return ModelHasFlow
     */
    public function currentProcess()
    {
        return $this->process->where('state', 'processing')->first();
    }
}