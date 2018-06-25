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
use app\common\modules\status\StatusObserverDispatcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 阶段
 * Class ModelBelongsStatus
 * @package app\common\models\Status
 * @property State state
 * @property string name
 * @property string code
 * @property int model_id
 * @property int state_id
 * @property Model model_type
 * @property string belongsToModel
 */
class Status extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_status';

    protected $guarded = ['id'];
    protected $hidden = ['model_type'];

    /**
     * 所属的实体
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongsToModel(){
        return $this->belongsTo($this->model_type,'model_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function getNameAttribute(){
        return $this->state->name;
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
        parent::observe(new StatusObserverDispatcher);

    }
}
