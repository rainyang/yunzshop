<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/28
 * Time: 上午11:32
 */

namespace app\common\models;


use app\frontend\modules\order\services\status\StatusServiceFactory;

class OrderDispatch extends BaseModel
{
    public $table = 'yz_order_dispatch';

    public function belongsToOrder(){
        return $this->belongsTo('\app\common\models\Member', 'order_id', 'id');

    }


}