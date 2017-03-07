<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/6
 * Time: 上午11:42
 */

namespace app\common\models;

use app\backend\models\BackendModel;

class Sale extends BackendModel
{
    public $table = 'yz_goods_sale';

    protected $guarded = [''];

    protected $fillable = [''];
}