<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/3
 * Time: 上午9:10
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\requests\Request;
use app\frontend\modules\order\models\OrderAddress;
use app\frontend\modules\order\models\OrderDetailModel;


class DetailController extends ApiController
{
    public function index(Request $request){
        $this->validate($request, [
            'order_id' => 'required|integer'
        ]);
        $orderId = $request->query('order_id');

        $order = OrderDetailModel::getOrderDetail($orderId);

        if ($order->uid != \YunShop::app()->getMemberId()) {
            throw new AppException('无效申请,该订单属于其他用户');
        }
        $order->button_models = $order->button_models;

        $data= $order->toArray();

        //todo 配送类型
        if($order['dispatch_type_id'] == 1){
            $data['address_info'] = OrderAddress::select('address','mobile','realname')->where('order_id',$order['id'])->first();
        }
        if (!$order){
            return $this->errorJson($msg = '未找到数据', []);
        } else {
            return $this->successJson($msg = 'ok', $data);
        }

    }
}