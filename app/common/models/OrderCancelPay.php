<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:19
 * comment: 取消支付
 */

namespace app\common\models;

use app\frontend\modules\order\model\OrderModel;
use Illuminate\Database\Eloquent\Model;

class OrderCancelPay extends Model
{
    public $order_model;

    public function __construct(OrderModel $order_model)
    {
        $this->order_model = $order_model->getData();
    }

    public function pay()
    {
        self::update(['status' => 0])
            ->where('shop_id', '=', $this->order_model['shop_id'])
            ->where('id', '=', $this->order_model['id']);
    }

    public function payable()
    {
        if ($this->order_model['status'] == 1) {
            return true;
        }
    }
}