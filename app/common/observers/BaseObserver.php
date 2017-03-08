<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 24/02/2017
 * Time: 01:01
 */

namespace app\common\observers;


use Illuminate\Database\Eloquent\Model;

class BaseObserver {

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