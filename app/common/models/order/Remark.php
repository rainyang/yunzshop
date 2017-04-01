<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/6
 * Time: 下午7:54
 */
namespace app\common\models\order;


use app\common\models\BaseModel;

class Remark extends BaseModel
{
    public $table = 'yz_order_remark';
    protected $guarded = [''];
}