<?php
/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/28
 * Time: 上午10:54
 */

namespace app\frontend\modules\goods\models\goods;

class Privilege extends \app\common\models\goods\Privilege
{
    protected $casts = [
        'time_begin_limit'=>'datetime',
        'time_end_limit'=>'datetime',
    ];
    public function validate()
    {

    }
    public function enableTimeLimit()
    {
        if($this->enable_time_limit){
            //$this->time_begin_limit
        }
    }
}