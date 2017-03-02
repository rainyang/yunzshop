<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:16
 */
namespace app\frontend\modules\order\model\factory;
use app\frontend\modules\order\model;
use Illuminate\Database\Eloquent\Collection;

class OrderModelFactory
{
    public static function createOrderModel($order_status, $order_id)
    {

    }

    public static function createOrderModels(Collection $db_order_models)
    {
        $result = [];
        foreach ($db_order_models as $db_order_model){
            switch ($db_order_model->status){
                case 0:
                    $result[] = new model\WaitPayOrderModel($db_order_model);
                    break;
                default:
                    echo '订单状态不存在';
                    break;
            }
        }
        return $result;
    }
}