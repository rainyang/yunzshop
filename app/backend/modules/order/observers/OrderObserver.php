<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/14
 * Time: 下午3:16
 */
namespace app\backend\modules\order\observers;

use app\common\observers\BaseObserver;
use app\frontend\modules\order\services\OrderService;
use Illuminate\Database\Eloquent\Model;

class OrderObserver extends BaseObserver
{
    public function saving(Model $model)
    {

    }
    public function saved(Model $model)
    {
        //$this->pluginObserver('observer.order',$model,'saved', 1);
    }
}