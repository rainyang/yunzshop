<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 14/06/2018
 * Time: 11:44
 */

namespace app\common\traits;


use app\common\models\State;
use app\common\models\Status;
use app\common\models\Process;
use app\common\modules\status\StatusContainer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Trait HasStatusTrait
 * @package app\common\traits
 * @property Collection status
 */
trait BelongsStatusTrait
{
    /**
     * @return HasMany
     */
    public function status()
    {
        return $this->hasMany(Status::class, 'model_id', 'id')->where('model_type', $this->getTable());
    }

    /**
     * 所有的状态类型
     * @return MorphToMany
     */
    public function states()
    {
        return $this->morphToMany(
            State::class,
            'model',
            (new Status())->getTable(),
            'model_id',
            'state_id'
        )->withTimestamps();
    }

    abstract protected function statusAttribute($nextState);

    /**
     * @return Status
     */
    public function currentStatus()
    {
        // todo 判断存在 不存在删掉递归
        return $this->status->where()->first();
    }

    /**
     * 根据实体和state创建status
     * @param $state
     */
    protected function createStatus(State $state)
    {
        $this->states()->save($state, $this->statusAttribute($state));
//        dd($this->currentStatus()->getEventDispatcher()->getListeners('eloquent.created: '.get_class($this->currentStatus())));
//        exit;

        $this->currentStatus()->fireModelEvent('created');
    }

}