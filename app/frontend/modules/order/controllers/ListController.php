<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\frontend\models\Order;
use app\frontend\models\OrderListModel;
use app\frontend\modules\order\services\VideoDemandOrderGoodsService;

class ListController extends ApiController
{
    protected $order;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    protected function getOrder()
    {
        if(!isset($this->order)){
            return $this->_getOrder();
        }
        return $this->order;
    }

    /**
     * @return Order
     */
    protected function _getOrder()
    {
        return $this->order = app('OrderManager')->make('Order')->orders()->where('status', '<>', '-1');
    }

    protected function getData()
    {
        $pageSize = \YunShop::request()->pagesize;
        $pageSize = $pageSize ? $pageSize : 20;
        $data = $this->getOrder()->paginate($pageSize)->toArray();

        //视频点播
        if (VideoDemandOrderGoodsService::whetherEnabled()) {
            foreach ($data['data'] as &$orderCourse) {
                foreach ($orderCourse['has_many_order_goods'] as &$value) {
                    $value['is_course'] = VideoDemandOrderGoodsService::whetherCourse($value['goods_id']);
                }
            }
        }

        return $data;
    }

    /**
     * 所有订单(不包括"已删除"订单)
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        return $this->successJson($msg = 'ok', $data = $this->getData());

    }

    /**
     * 待付款订单
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitPay()
    {
        $this->getOrder()->waitPay();
        return $this->successJson($msg = 'ok', $data = $this->getData());

    }

    /**
     * 待发货订单
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitSend()
    {
        $this->getOrder()->waitSend();
        return $this->successJson($msg = 'ok', $data = $this->getData());

    }

    /**
     * 待收货订单
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitReceive()
    {
        $this->getOrder()->waitReceive();

        return $this->successJson($msg = 'ok', $data = $this->getData());
    }

    /**
     * 已完成订单
     * @return \Illuminate\Http\JsonResponse
     */
    public function completed()
    {
        $this->getOrder()->completed();

        return $this->successJson($msg = 'ok', $data = $this->getData());
    }
}