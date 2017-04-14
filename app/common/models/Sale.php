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
    
    public $attributes = [
        'max_point_deduct' => 0,
        'max_balance_deduct' => 0,
        'is_sendfree' => 0,
        'ed_num' => 0,
        'ed_money' => 0,
        'point' => 0,
        'bonus' => 0
    ];

    protected $guarded = [''];

    protected $fillable = [''];
}