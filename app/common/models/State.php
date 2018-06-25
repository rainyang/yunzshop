<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;


/**
 * 状态
 * Class State
 * @package app\common\models\statusFlow
 * @property int id
 * @property int order
 * @property string code
 * @property string name
 */
class State extends BaseModel
{
    public $table = 'yz_state';

    protected $guarded = ['id'];
    protected $fillable = ['name','code','plugin_id'];
    /**
     * 包含此状态的流程
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function flows()
    {
        return $this->belongsToMany(Flow::class, self::getTable(), 'state_id', 'flow_id');
    }
}
