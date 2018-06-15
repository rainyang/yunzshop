<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models\process;

use app\common\models\Process;
use app\common\models\Status;
use Illuminate\Database\Eloquent\Builder;

/**
 * 阶段
 * Class ModelBelongsStatus
 * @package app\common\models\statusFlow
 * @property Status status
 */
class ProcessStatus extends Status
{
    public function process()
    {
        return $this->belongsTo($this->model_type, 'model_id');
    }


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->where('model_type', Process::class);
        });
    }
}
