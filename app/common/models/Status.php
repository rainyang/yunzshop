<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;

use app\common\models\BaseModel;
use app\common\modules\status\StatusContainer;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 阶段
 * Class ModelBelongsStatus
 * @package app\common\models\statusFlow
 * @property State state
 * @property string code
 */
class Status extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_status';

    protected $guarded = ['id'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * @return array
     */
    public function getButtons()
    {
        return [];
    }
    protected static function boot()
    {
        parent::boot();
        // TODO: 来不及解决
        parent::observe();
        self::created(function($event,Status $status){
            if(isset($status->code)){
                app('StatusContainer')->make($status->code)->onCreated();
            }
        });
    }
}
