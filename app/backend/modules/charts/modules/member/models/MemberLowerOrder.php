<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 11:15
 */

namespace app\backend\modules\charts\modules\member\models;


use app\common\models\BaseModel;

class MemberLowerOrder extends BaseModel
{
    public $table = 'yz_member_lower_order';
    public $timestamps = true;

    protected $fillable = [];
    protected $guarded = [''];

}