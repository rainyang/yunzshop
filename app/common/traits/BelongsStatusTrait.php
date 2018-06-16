<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 14/06/2018
 * Time: 11:44
 */

namespace app\common\traits;


use app\common\models\Status;
use app\common\models\Process;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait HasStatusTrait
 * @package app\common\traits
 * @property Collection status
 */
trait BelongsStatusTrait
{
//    public function status(){
//        return $this->morphToMany(
//            Status::class,
//            'model_type',
//            (new ModelBelongsStatus)->getTable(),
//            'model_id',
//            'status_id'
//        );
//    }
    /**
     * @return HasMany
     */
    public function status()
    {
        return $this->hasMany(Process::class, 'model_id', 'id')->where('model_type', self::class);
    }

    /**
     * @return Status
     */
    public function currentStatus()
    {
        return $this->status->where('state', 'processing')->first();
    }
}