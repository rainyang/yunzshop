<?php
/**
 * Created by PhpStorm.
 * User: RainYang
 * Date: 2017/2/22
 * Time: 19:35
 */

namespace app\common\models;

use app\common\models\BaseModel;
use HaoLi\LaravelAmount\Traits\AmountTrait;


class GoodsOption extends BaseModel
{
    use AmountTrait;

    protected $amountFields = ['product_price', 'market_price', 'cost_price'];

    public $table = 'yz_goods_option';

    public $guarded = [];
    
}