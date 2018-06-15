<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 14/06/2018
 * Time: 11:44
 */

namespace app\common\traits;


use app\common\models\flow\Status;
use app\common\models\flow\Process;
use EasyWeChat\Support\Collection;

/**
 * Trait HasStatusTrait
 * @package app\common\traits
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
     * @return Collection
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