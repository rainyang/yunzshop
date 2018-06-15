<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models\order;

use app\common\models\Flow;
use app\common\models\Order;
use app\common\models\Process;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * 订单的流程
 * Class ModelHasFlow
 * @package app\common\models\statusFlow
 * @property Flow flow
 * @property Collection status
 */
class OrderProcess extends Process
{
    public function order()
    {
        return $this->belongsTo($this->model_type, 'model_id');
    }


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->where('model_type', Order::class);
        });
    }
}
