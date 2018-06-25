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
        return $this->hasMany(Status::class, 'model_id', 'id')->where('model_type', $this->getMorphClass());
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

    protected function statusAttribute($state)
    {
        return ['state_id' => $state->id,
            'model_id' => $this->id,
            'model_type' => $this->getMorphClass(),
            'code' => $this->code.'.'.$state->code];
    }

    /**
     * @return Status
     */
    public function currentStatus()
    {
        // 为了与数据库中状态同步,需要重新取
        return $this->status()->first();
    }

    /**
     * 根据实体和state创建status
     * @param $state
     */
    protected function createStatus(State $state)
    {
        $status = new Status($this->statusAttribute($state));
        $status->save();
    }

}