<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午9:58
 */

namespace app\common\modules\status;

use app\common\models\Status;
use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;

class StatusObserverDispatcher
{
    public function created(Model $status)
    {

        /**
         * @var Status $status
         */
        if(app('StatusContainer')->bound($status->code)){
            app('StatusContainer')->make($status->code,$status)->onCreated();
        }

    }
}