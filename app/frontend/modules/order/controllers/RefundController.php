<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\refund\Apply;
use app\common\models\refund\RefundApply;
use app\frontend\modules\order\models\Order;
use Request;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/12
 * Time: 上午10:38
 */
class RefundController extends ApiController
{
    public function apply(Request $request)
    {
//        $params = $request->only(['reason','images','order_id']);
//        dd($params);
        $this->validate($request, [
            'reason' => 'required',
            'images' => 'sometimes|json',
            'order_id' => 'required'
        ],[
            'images.json'=>'images非json格式'
        ]);

        $refundApply = new RefundApply($request->query());
        $refundApply->price = Order::find($refundApply->order_id)->price;
        if(!$refundApply->save()){
            throw new AppException('请求失败');
        }

        return $this->successJson('成功',$refundApply->toArray());
    }

}