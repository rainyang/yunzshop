<?php
namespace app\common\models;

use app\common\models\BaseModel;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 上午9:11
 */

class Brand extends BaseModel
{
    public $table = 'yz_brand';

    protected $guarded = [''];

    //protected $fillable = ['name'];
}