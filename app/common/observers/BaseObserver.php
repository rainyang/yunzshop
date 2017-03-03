<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 24/02/2017
 * Time: 01:01
 */

namespace app\common\observers;


use Eloquent;

class BaseObserver {

    public function saving(Eloquent $model) {}

    public function saved(Eloquent $model) {}

    public function updating(Eloquent $model) {}

    public function updated(Eloquent $model) {}

    public function creating(Eloquent $model) {}

    public function created(Eloquent $model) {}

    public function deleting(Eloquent $model) {}

    public function deleted(Eloquent $model) {}

    public function restoring(Eloquent $model) {}

    public function restored(Eloquent $model) {}
}