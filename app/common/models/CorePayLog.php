<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/9
 * Time: 上午11:19
 */

namespace app\common\models;


class CorePayLog extends BaseModel
{
    public $table = 'yz_core_paylog';
    public $timestamps = false;

    public $guarded = [''];
}