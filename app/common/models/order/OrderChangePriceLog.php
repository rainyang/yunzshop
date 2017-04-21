<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/19
 * Time: 下午5:00
 */

namespace app\common\models\order;


use app\common\models\BaseModel;

class OrderChangePriceLog extends BaseModel
{
    public $table = 'yz_order_change_log';
    protected $fillable = [];
    protected $guarded = ['id'];
}