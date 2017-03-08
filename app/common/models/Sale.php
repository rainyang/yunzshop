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
    public $love_money = '0';
    public $max_point_deduct = '0';
    public $max_balance_deduct = '0';
    public $is_sendfree = '0';
    public $ed_num = '0';
    public $ed_money = '0';
    public $point = '0';
    public $bonus = '0';

    protected $guarded = [''];

    protected $fillable = [''];
}