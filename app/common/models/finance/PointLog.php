<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/10
 * Time: 下午5:47
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;

class PointLog extends BaseModel
{
    public $table = 'yz_point_log';
    protected $guarded = [''];
    //搜索
    protected $search_fields = ['id'];
}