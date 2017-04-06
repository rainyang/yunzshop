<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/3
 * Time: 上午9:10
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\frontend\modules\order\models\OrderAddress;
use app\frontend\modules\order\models\OrderDetailModel;


class DetailController extends ApiController
{
    public function index(){
        $orderId = \YunShop::request()->order_id;
        if (!$orderId) {
            return $this->errorJson($msg = '缺少访问参数', $data = []);
        } else {
            $orderDetail = OrderDetailModel::getOrderDetail($orderId);
            $data= $orderDetail->toArray();
            
            //todo 配送类型
            //dd($orderDetail);
            if($orderDetail['dispatch_type_id'] == 1){
                $data['address_info'] = OrderAddress::select('address','mobile','realname')->where('order_id',$orderDetail['id'])->first();
            }
            if (!$orderDetail){
                return $this->errorJson($msg = '未找到数据', []);
            } else {
                return $this->successJson($msg = 'ok', $data);
            }
        }
    }
}