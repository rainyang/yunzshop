<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/23
 * Time: 上午10:32
 */

namespace app\common\frame;


use app\common\models\BaseModel;

class RuleKeyword extends BaseModel
{
    public $table = 'rule_keyword';

    public $timestamps = false;

    protected $guarded = [''];



}
