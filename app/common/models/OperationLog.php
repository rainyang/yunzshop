<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/24
 * Time: 15:34
 */

namespace app\common\models;

use app\framework\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperationLog extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_operation_log';

    protected $guarded = ['id'];

    //protected $appends = [''];

    protected $attributes = [
    ];

    public function scopeSearch(Builder $query, $search)
    {
        $model = $query->uniacid();

        if ($search['user_name']) {
            $model->where('user_name', 'like', '%' . $search['user_name'] . '%');
        }

        if ($search['is_time']) {
                if ($search['time']['start'] != '请选择' && $search['time']['end'] != '请选择') {
                    $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                    $model->whereBetween('created_at', $range);
                }
        }


        return $model;

    }
}