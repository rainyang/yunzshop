<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/23
 * Time: 上午10:41
 */

namespace app\common\frame;


use app\common\models\BaseModel;

class Rule extends BaseModel
{
    public $table = 'rule';

    public $timestamps = false;

    protected $guarded = [''];


}