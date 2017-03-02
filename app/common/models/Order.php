<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/28
 * Time: 上午11:32
 */

namespace app\common\models;

use app\common\models\BaseModel;

class Order extends BaseModel
{
    public $table = 'yz_order';
    
    public static function getOrder($order_id, $uniacid)
    {
        return self::where('id', $order_id)
            ->where('uniacid', $uniacid)
            ->first()
            ->toArray();
    }
}