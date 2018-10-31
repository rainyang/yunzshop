<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 11:16
 */

namespace app\backend\modules\charts\modules\member\models;


use app\common\models\BaseModel;

class MemberLowerCount extends BaseModel
{
    public $table = 'ims_yz_member_lower_count';
    public $timestamps = true;

    protected $fillable = [];
    protected  $guarded = [''];

}