<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:11
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\events\PreGeneratedOrderDisplayEvent;
use app\common\models\Order;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\ShopService;

class PreGeneratedController extends BaseController
{
    private $_data = [];
    //(事件) 添加返回数据
    public function addData($key,$data){
        $this->_data[$key][] = $data;
    }
    //(事件) 设置返回数据
    public function setData($key,$data){
        $this->_data[$key] = $data;
    }
    public function index(){
        //$param = \YunShop::request();
        $param = [
            [
                'goods_id' => 1,
                'total' => 1
            ]
        ];
        $member_model = MemberService::getCurrentMemberModel();
        $shop_model = ShopService::getCurrentShopModel();
        //todo 根据参数
        $order_goods_models = OrderService::getOrderGoodsModels($param);
        $order_model = OrderService::getPreCreateOrder($order_goods_models,$member_model,$shop_model);
        $order = $order_model->toArray();
        \Illuminate\Support\Facades\Event::fire(new PreGeneratedOrderDisplayEvent($this,Order::find(1)));
        $data = [
            'order'=>$order
        ];
        $data = array_merge($data,$this->_data);
        dd($data);

        return $this->successJson($data);
    }
}