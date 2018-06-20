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
use app\common\modules\status\StatusObserver;
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
        parent::observe(new StatusObserver);

    }
}
