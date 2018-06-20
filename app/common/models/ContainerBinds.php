<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午11:17
 */

namespace app\common\models;


use Illuminate\Database\Eloquent\SoftDeletes;

class ContainerBinds extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_container_binds';
    protected $guarded = ['id'];
}