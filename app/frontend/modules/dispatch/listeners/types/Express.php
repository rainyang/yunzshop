<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/14
 * Time: 上午10:40
 */

namespace app\frontend\modules\dispatch\listeners\types;

use app\common\events\dispatch\OnDispatchTypeInfoDisplayEvent;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\OrderCreatedEvent;

use app\common\exceptions\AppException;
use app\common\models\DispatchType;
use app\common\models\Goods;
use app\common\models\Order;
use app\common\models\OrderAddress;
use app\frontend\modules\member\models\MemberAddress;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Express
{
    use ValidatesRequests;
    private $event;

    public function onSave(AfterOrderCreatedEvent $even)
    {
        $this->event = $even;
        if (!$this->needDispatch()) {
            return;
        }
        //保存信息
        $this->saveExpressInfo();

        return;
    }

    public function onDisplay(OnDispatchTypeInfoDisplayEvent $event)
    {
        $this->event = $event;
        if (!$this->needDispatch()) {
            return;
        }
        //返回信息 todo 需要获取用户当前默认地址

        $data = $event->getOrderModel()->getMember()->defaultAddress;
        if (!isset($data)) {
            return;
        }

        $event->addMap('default_member_address', $data);
        return;
    }

    private function needDispatch()
    {
    //实物
        $allGoodsIsReal = ($this->event->getOrderModel()->getOrderGoodsModels()->contains(function ($orderGoods){

            return $orderGoods->belongsToGood->isRealGoods();
        }));

        if($allGoodsIsReal){
            return true;
        }

        return false;
    }

    private function saveExpressInfo(AfterOrderCreatedEvent $event)
    {

        $request = \Request::capture();
        //$request->input('address');
        $address = json_decode($request->input('address'),true);
        $this->validate(['address'=>$address],[
                //'address' => 'required|array',
                'address.address' => 'required|string',
                'address.mobile' => 'required|string',
                'address.username' => 'required|string',
                'address.province' => 'required|string',
                'address.city' => 'required|string',
                'address.district' => 'required|string',
            ]
        );
        /*$address =['address' => '云霄路188-1',
            'mobile' => '18545571024',
            'username' => '高启',
            'province' => '广东省',
            'city' => '广州市',
            'district' => '白云区',];*/
        $member_address = new MemberAddress($address);

        $order_address = new OrderAddress();

        $order_address->order_id = $this->event->getOrderModel()->id;
        $order_address->address = implode(' ', [$member_address->province, $member_address->city, $member_address->district, $member_address->address]);
        $order_address->mobile = $member_address->mobile;
        $order_address->realname = $member_address->username;
        if(!$order_address->save()){
            throw new AppException('订单地址保存失败');
        }
        $order = Order::find($event->getOrderModel()->id);
        $order->dispatch_type_id = DispatchType::EXPRESS;
        if(!$order->save()){
            throw new AppException('订单配送方式保存失败');
        }
        return true;
    }

    public function subscribe($events)
    {
        $events->listen(
            OnDispatchTypeInfoDisplayEvent::class,
            Express::class . '@onDisplay'
        );
        $events->listen(
            \app\common\events\order\AfterOrderCreatedEvent::class,
            Express::class . '@onSave'
        );
    }
    private function validate( $request, array $rules, array $messages = [], array $customAttributes = [])
    {

        $validator = $this->getValidationFactory()->make($request, $rules, $messages, $customAttributes);
        //$validator->errors();
        if ($validator->fails()) {
            throw new AppException($validator->errors()->first());
        }
    }
}