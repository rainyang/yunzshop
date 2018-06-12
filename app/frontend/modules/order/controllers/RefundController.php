<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\refund\Apply;
use app\common\models\refund\RefundApply;
use app\frontend\models\Order;
use Request;
use app\backend\modules\goods\models\ReturnAddress;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/12
 * Time: 上午10:38
 */
class RefundController extends ApiController
{
    public function returnAddress() {
        $plugins_id = \YunShop::request()->plugins_id ? \YunShop::request()->plugins_id : 0;
        $store_id = \YunShop::request()->store_id ? \YunShop::request()->store_id : 0;
        $supplier_id = \YunShop::request()->supplier_id ? \YunShop::request()->supplier_id : 0;
        $address = ReturnAddress::getOneByPluginsId($plugins_id, $store_id, $supplier_id);
        if ($address) {
            return $this->successJson('获取退货地址成功!', $address->toarray());
        }
        return $this->errorJson('获取退货地址失败',$address);
    }

}