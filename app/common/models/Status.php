<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;

use app\common\models\BaseModel;

/**
 * 阶段
 * Class ModelBelongsStatus
 * @package app\common\models\statusFlow
 * @property State state
 */
class Status extends BaseModel
{
    public $table = 'yz_status';

    protected $guarded = ['id'];

    public function state(){
        return $this->belongsTo(State::class);
    }

    /**
     *
     * @return array
     */
    public function getButtons(){
        return [];
    }
}
