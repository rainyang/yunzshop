<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/29
 * Time: 下午9:57
 */
namespace app\common\observers\dispatchObserver;
use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;

class DispatchObserver extends BaseObserver
{
    public function saved(Model $model)
    {
        $this->pluginObserver('observer.dispatch', $model, 'saved');
    }

    public function deleted(Model $model)
    {
        $this->pluginObserver('observer.dispatch', $model, 'deleted');
    }
}