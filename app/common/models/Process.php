<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;

use app\common\traits\BelongsStatusTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 进程
 * Class ModelHasFlow
 * @package app\common\models\flow
 * @property Flow flow
 * @property Collection status
 * @property string model_type
 */
class Process extends BaseModel
{
    use BelongsStatusTrait, SoftDeletes;
    public $table = 'yz_process';

    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at'];

    /**
     * 进程的主体
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function model()
    {
        return $this->belongsTo($this->model_type, 'model_id');
    }

    /**
     * 所属流程类型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }
    public function getNextStatus(){}
    /**
     * 进入下一个状态
     */
    public function toNextState()
    {
        // 进程进入下一个状态
        $this->currentStatus()->getNextState();
        $nextState = $this->flow->getNextState($this->currentStatus()->state);
        $this->status()->save($nextState);
    }
}
