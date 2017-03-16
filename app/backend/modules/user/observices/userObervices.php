<?php

/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/16
 * Time: 下午1:41
 */
class UserObservices extends \app\common\observers\BaseObserver
{
    public function saving(Model $model) {}

    public function saved(Model $model) {}

    public function updating(Model $model) {}

    public function updated(Model $model) {}

    public function creating(Model $model) {}

    public function created(Model $model) {}

    public function deleting(Model $model) {}

    public function deleted(Model $model) {}

    public function restoring(Model $model) {}

    public function restored(Model $model) {}
}