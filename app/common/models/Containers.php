<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午11:17
 */

namespace app\common\models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Containers extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_containers';
    protected $guarded = ['id'];
    protected $fillable = ['name', 'key', 'class', 'is_shared'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function binds()
    {
        return $this->hasMany(ContainerBinds::class);
    }
}