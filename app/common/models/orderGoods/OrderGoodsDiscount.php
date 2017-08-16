<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\common\models\orderGoods;

use app\common\models\BaseModel;

class OrderGoodsDiscount extends BaseModel
{
    public $table = 'yz_order_goods_discount';
    protected $fillable = [];
    protected $guarded = ['id'];
}