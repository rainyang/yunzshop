<?php
namespace app\backend\modules\user\observers;

use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;


/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/17
 * Time: 上午10:56
 */
class UserObserver extends BaseObserver
{
    public function saving(Model $model)
    {

        //检测1
       // if(){
        //    return false;
      // }
        //检测2
    }

    public function saved(Model $model) {

    }

    public function updating(Model $model) {}

    public function updated(Model $model) {}

    public function creating(Model $model) {}

    public function created(Model $model) {}

    public function deleting(Model $model) {}

    public function deleted(Model $model) {}

    public function restoring(Model $model) {}

    public function restored(Model $model) {}
}