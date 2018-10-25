<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/24
 * Time: 15:34
 */

namespace app\common\models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OperationLog extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_operation_log';

    protected $guarded = ['id'];

    //protected $appends = [''];

    protected $attributes = [
        'plugin_id' => 0,
        'is_virtual' => 0,
    ];

}