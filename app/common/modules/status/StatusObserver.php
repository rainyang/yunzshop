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

class StatusObserver extends BaseObserver
{
    public function created(Model $status)
    {
        dd($status);
        exit;

        /**
         * @var Status $status
         */
        if((new StatusContainer())->bound($status->code)){
            (new StatusContainer())->make($status->code)->onCreated();
        }

    }
}