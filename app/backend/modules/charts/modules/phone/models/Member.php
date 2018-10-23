<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 15:59
 */

namespace app\backend\modules\charts\modules\phone\models;


use app\common\models\BaseModel;

class Member extends BaseModel
{
    protected $table = 'yz_member_phone';
    protected $guarded = [''];
    protected $fillable = [''];

    public $timestamps = true;
}