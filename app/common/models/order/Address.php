<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/8
 * Time: 下午2:24
 */

namespace app\common\models\order;



use app\common\models\BaseModel;

class Address extends BaseModel
{
    public $table = 'yz_order_address';
    protected $hidden = ['id', 'order_id'];
}