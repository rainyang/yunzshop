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
     * @var Process
     */
    protected $currentProcess;

    /**
     * 所有的流程类型
     * @return mixed
     */
    public function flows()
    {
        return $this->morphToMany(
            Flow::class,
            'model',
            (new Process())->getTable(),
            'model_id',
            'flow_id'
        )->withTimestamps();
    }

    /**
     * @param Flow $flow
     * @return Process
     */
    public function addProcess(Flow $flow)
    {

        $this->flows()->save($flow);
        $this->currentProcess()->initStatus();

        return $this->currentProcess();
    }

    /**
     * @return HasMany
     */
    public function process()
    {
        return $this->hasMany(Process::class, 'model_id')->where('model_type', $this->getTable());
    }

    /**
     * 当前的流程
     * @return Process
     */
    public function currentProcess()
    {
        if (!isset($this->currentProcess)) {
            $this->currentProcess = $this->process->where('state', 'processing')->first();
        }
        return $this->currentProcess;
    }
}