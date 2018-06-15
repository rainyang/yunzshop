<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;

use app\common\models\BaseModel;
use app\common\traits\BelongsStatusTrait;
use Illuminate\Database\Eloquent\Collection;

/**
 * 进程
 * Class ModelHasFlow
 * @package app\common\models\flow
 * @property Flow flow
 * @property Collection status
 */
class Process extends BaseModel
{
    use BelongsStatusTrait;
    public $table = 'yz_process';

    protected $guarded = ['id'];

    /**
     * 进程的主体
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function model(){
        return $this->belongsTo($this->model_type,'model_id');
    }

    /**
     * 所属流程类型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flow(){
        return $this->belongsTo(Flow::class);
    }
}
