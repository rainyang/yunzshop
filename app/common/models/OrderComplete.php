<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:12
 * comment: 订单完成
 */

namespace app\common\models;

use app\frontend\modules\order\model\OrderModel;
use Illuminate\Database\Eloquent\Model;

class OrderComplete extends Model
{
    public $order_model;

    public function __construct(OrderModel $order_model)
    {
        $this->order_model = $order_model->getData();
    }

    public function payable()
    {
        if ($this->order_model['status'] == 3) {
            return true;
        }
    }
}